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
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
            break;

        case 'POST':
            if (!$input || empty($input['name'])) {
                http_response_code(400);
                echo json_encode(["message" => "Nama produk wajib diisi"]);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, emoji) VALUES (?, ?, ?, ?)");
            $stmt->execute([$input['name'], $input['price'], $input['stock'], $input['emoji']]);
            echo json_encode(["message" => "Produk berhasil ditambah"]);
            break;

        case 'PUT':
            if (!$input || empty($input['id'])) {
                http_response_code(400);
                echo json_encode(["message" => "ID produk wajib diisi"]);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, stock=?, emoji=? WHERE id=?");
            $stmt->execute([$input['name'], $input['price'], $input['stock'], $input['emoji'], $input['id']]);
            echo json_encode(["message" => "Produk berhasil diupdate"]);
            break;

        case 'DELETE':
            if (!$input || empty($input['id'])) {
                http_response_code(400);
                echo json_encode(["message" => "ID produk wajib diisi"]);
                exit;
            }
            
            $id = intval($input['id']);
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(["message" => "Produk berhasil dihapus"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Produk tidak ditemukan"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => "Server error: " . $e->getMessage()]);
}
?>