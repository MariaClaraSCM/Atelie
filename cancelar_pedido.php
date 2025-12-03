<?php
session_start();
require 'config.php';

// Verifica login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    echo json_encode(["ok" => false, "msg" => "Usuário não logado"]);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_pedido  = $_POST['id_pedido'] ?? null;

if (!$id_pedido) {
    echo json_encode(["ok" => false, "msg" => "Pedido inválido"]);
    exit;
}

// 🔒 Verifica se o pedido realmente pertence ao usuário
$sql = $pdo->prepare("SELECT id_pedido FROM pedido WHERE id_pedido = ? AND id_usuario = ?");
$sql->execute([$id_pedido, $id_usuario]);

if ($sql->rowCount() == 0) {
    echo json_encode(["ok" => false, "msg" => "Pedido não encontrado"]);
    exit;
}

// ------------------------------
// 1° EXCLUI ITENS DO PEDIDO
// ------------------------------
$deleteItens = $pdo->prepare("DELETE FROM item_pedido WHERE id_pedido = ?");
$deleteItens->execute([$id_pedido]);

// ------------------------------
// 2° EXCLUI O PEDIDO
// ------------------------------
$deletePedido = $pdo->prepare("DELETE FROM pedido WHERE id_pedido = ?");
$deletePedido->execute([$id_pedido]);

echo json_encode(["ok" => true]);
exit;
?>
