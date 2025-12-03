<?php
require 'config.php';
session_start();

$id_user = $_SESSION['id_usuario'] ?? null;

if (!$id_user) {
    echo json_encode([]);
    exit;
}

// Buscar carrinho do usuário
$sql = $pdo->prepare("SELECT id_carrinho FROM carrinho WHERE id_usuario = ?");
$sql->execute([$id_user]);
$carrinho = $sql->fetch(PDO::FETCH_ASSOC);

if (!$carrinho) {
    echo json_encode([]);
    exit;
}

$id_carrinho = $carrinho['id_carrinho'];

// Buscar itens do carrinho
$sql = $pdo->prepare("
    SELECT 
        ic.id_item_carrinho,
        p.id_produto,
        p.nm_produto,
        p.preco,
        p.foto_produto,
        ic.quantidade,
        ic.cor_item,
        ic.nm_personagem
    FROM item_carrinho ic
    JOIN produto p ON p.id_produto = ic.id_produto
    WHERE ic.id_carrinho = ?
");

$sql->execute([$id_carrinho]);
$itens = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($itens);
