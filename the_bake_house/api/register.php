<?php
// api/register.php
require 'db.php';
$data = json_decode(file_get_contents("php://input"));

$stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, 'customer')");
if($stmt->execute([$data->username, $data->password, $data->full_name])) {
    echo json_encode(["message" => "Registrasi sukses!"]);
} else {
    http_response_code(400);
    echo json_encode(["message" => "Gagal mendaftar."]);
}