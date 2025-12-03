<?php
require "config.php";
session_start();

if (!isset($_SESSION["id_usuario"])) {
    echo json_encode(["ok" => false, "msg" => "Usuário não logado"]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];

// RECEBE DADOS
$id_produto   = $_POST["id_produto"] ?? null;
$quantidade   = $_POST["quantidade"] ?? 1;
$cor_item     = $_POST["cor_item"] ?? null;
$personagem   = $_POST["personagem"] ?? null;

if (!$id_produto) {
    echo json_encode(["ok" => false, "msg" => "Produto inválido"]);
    exit;
}

// BUSCAR PREÇO ATUAL
$sql = $pdo->prepare("SELECT preco FROM produto WHERE id_produto = ?");
$sql->execute([$id_produto]);
$produto = $sql->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo json_encode(["ok" => false, "msg" => "Produto não encontrado"]);
    exit;
}

$preco_unitario = $produto["preco"];
$subtotal = $preco_unitario * $quantidade;

$pdo->beginTransaction();

try {

    // ======================
    // 1) CRIAR PEDIDO
    // ======================
    $sql = $pdo->prepare("
        INSERT INTO pedido (dt_pedido, vl_total, metodo_pagamento, status_pedido, id_usuario)
        VALUES (NOW(), ?, NULL, 'Pendente', ?)
    ");
    $sql->execute([$subtotal, $id_usuario]);

    $id_pedido = $pdo->lastInsertId();

    // ======================
    // 2) CRIAR ITEM DO PEDIDO
    // ======================
    $sql = $pdo->prepare("
        INSERT INTO item_pedido
        (id_pedido, id_produto, preco_unitario, subtotal, qt_item, cor_item, nm_personagem)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $sql->execute([
        $id_pedido,
        $id_produto,
        $preco_unitario,
        $subtotal,
        $quantidade,
        $cor_item,
        $personagem
    ]);

    $pdo->commit();

    echo json_encode(["ok" => true, "id_pedido" => $id_pedido]);

} catch (Exception $e) {

    $pdo->rollBack();
    echo json_encode(["ok" => false, "msg" => $e->getMessage()]);
}
