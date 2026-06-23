<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

try {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("
                SELECT 
                    o.*, 
                    u.full_name as username 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.id ASC
            ");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($orders as &$order) {
                $itemStmt = $pdo->prepare("
                    SELECT oi.*, p.name, p.emoji 
                    FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = ?
                ");
                $itemStmt->execute([$order['id']]);
                $order['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            echo json_encode($orders);
            break;

        case 'POST':
            if (!$input || empty($input['items'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Keranjang kosong!']);
                exit;
            }
            
            $validItems = array_filter($input['items'], function($item) {
                return isset($item['quantity']) && $item['quantity'] > 0;
            });
            
            if (empty($validItems)) {
                http_response_code(400);
                echo json_encode(['message' => 'Tidak ada item valid untuk dipesan!']);
                exit;
            }
            
            // CEK STOK SEBELUM TRANSAKSI
            foreach ($validItems as $item) {
                $stockStmt = $pdo->prepare("SELECT id, name, stock FROM products WHERE id = ?");
                $stockStmt->execute([$item['product_id']]);
                $product = $stockStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$product) {
                    http_response_code(400);
                    echo json_encode(['message' => "Produk ID {$item['product_id']} tidak ditemukan!"]);
                    exit;
                }
                
                if ($product['stock'] < $item['quantity']) {
                    http_response_code(400);
                    echo json_encode([
                        'message' => "Stok {$product['name']} tidak mencukupi! (Stok: {$product['stock']}, Pesan: {$item['quantity']})"
                    ]);
                    exit;
                }
            }

            $pdo->beginTransaction();

            $orderStmt = $pdo->prepare("
                INSERT INTO orders (user_id, customer_name, customer_phone, total_amount, status) 
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $orderStmt->execute([
                $input['user_id'] ?? null,
                $input['customer_name'],
                $input['customer_phone'] ?? '',
                $input['total_amount']
            ]);
            $order_id = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stockStmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

            foreach ($validItems as $item) {
                $itemStmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                $stockStmt->execute([$item['quantity'], $item['product_id']]);
            }

            $pdo->commit();
            
            echo json_encode([
                'orderNumber' => $order_id, 
                'message' => 'Pesanan berhasil!'
            ]);
            break;

        case 'PUT':
            if (!$input || empty($input['id']) || empty($input['status'])) {
                http_response_code(400);
                echo json_encode(['message' => 'ID dan status required']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$input['status'], $input['id']]);
            
            echo json_encode(['message' => 'Status berhasil diupdate']);
            break;

        case 'DELETE':
            if (isset($input['reset']) && $input['reset'] === true) {
                $pdo->exec("DELETE FROM order_items");
                $pdo->exec("DELETE FROM orders");
                $pdo->exec("ALTER TABLE orders AUTO_INCREMENT = 1");
                $pdo->exec("ALTER TABLE order_items AUTO_INCREMENT = 1");
                
                echo json_encode(['message' => 'Semua data order berhasil direset!']);
            } else {
                if (!$input || empty($input['id'])) {
                    http_response_code(400);
                    echo json_encode(['message' => 'ID required']);
                    exit;
                }
                $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
                $stmt->execute([$input['id']]);
                echo json_encode(['message' => 'Pesanan berhasil dihapus']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    if ($method === 'POST' && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['message' => 'Server error: ' . $e->getMessage()]);
}
?>