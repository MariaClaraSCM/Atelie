<?php
require 'config.php';
session_start();
header("Content-Type: application/json");

$user_id = $_SESSION['id_usuario'] ?? 0;
$id_produto = $_POST['id_produto'] ?? 0;

// Se não estiver logado
if ($user_id == 0) {
    echo json_encode(["ok" => false, "message" => "não logado"]);
    exit;
}

// Se id do produto inválido
if ($id_produto == 0) {
    echo json_encode(["ok" => false, "message" => "produto inválido"]);
    exit;
}

// Verifica se já está favoritado
$check = $pdo->prepare("SELECT 1 FROM favoritos WHERE id_usuario = ? AND id_produto = ?");
$check->execute([$user_id, $id_produto]);

if ($check->rowCount() > 0) {
    // Remove favorito
    $del = $pdo->prepare("DELETE FROM favoritos WHERE id_usuario = ? AND id_produto = ?");
    $del->execute([$user_id, $id_produto]);

    echo json_encode(["ok" => true, "favorito" => false]);
    exit;
}

// Adiciona favorito
$add = $pdo->prepare("INSERT INTO favoritos (id_usuario, id_produto) VALUES (?, ?)");
$add->execute([$user_id, $id_produto]);

echo json_encode(["ok" => true, "favorito" => true]);
exit;
