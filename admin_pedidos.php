<?php
session_start();
require 'config.php';

header("Content-Type: application/json");

// Apenas admin pode usar este arquivo
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    echo json_encode(['ok' => false, 'msg' => 'Acesso negado']);
    exit;
}

$action = $_POST['action'] ?? null;

if (!$action) {
    echo json_encode(['ok' => false, 'msg' => 'Ação não informada']);
    exit;
}


/* =====================================================
    1) CRIAR PEDIDO
===================================================== */
if ($action === 'criar_pedido') {

    if (!isset($_POST['cliente']) || !isset($_POST['produtos'])) {
        echo json_encode(['ok' => false, 'msg' => 'Dados incompletos']);
        exit;
    }

    $id_usuario = intval($_POST['cliente']);
    $produtos = json_decode($_POST['produtos'], true);

    if (!$produtos || count($produtos) == 0) {
        echo json_encode(['ok' => false, 'msg' => 'Nenhum produto enviado']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Criando pedido
        $sql = $pdo->prepare("
            INSERT INTO pedido (dt_pedido, vl_total, metodo_pagamento, status_pedido, id_usuario)
            VALUES (NOW(), 0, NULL, 'Pendente', ?)
        ");
        $sql->execute([$id_usuario]);

        $id_pedido = $pdo->lastInsertId();
        $total = 0;

        foreach ($produtos as $p) {
            $id_produto = intval($p['id']);
            $cor = $p['cor'] ?: null;
            $person = $p['personagem'] ?: null;

            // Buscar preço
            $query = $pdo->prepare("SELECT preco FROM produto WHERE id_produto = ?");
            $query->execute([$id_produto]);
            $preco = floatval($query->fetchColumn());

            $subtotal = $preco * 1;

            // Inserindo item
            $insert = $pdo->prepare("
                INSERT INTO item_pedido 
                (id_pedido, id_produto, preco_unitario, subtotal, qt_item, cor_item, nm_personagem)
                VALUES (?, ?, ?, ?, 1, ?, ?)
            ");
            $insert->execute([$id_pedido, $id_produto, $preco, $subtotal, $cor, $person]);

            $total += $subtotal;
        }

        // Atualiza valor total
        $up = $pdo->prepare("UPDATE pedido SET vl_total = ? WHERE id_pedido = ?");
        $up->execute([$total, $id_pedido]);

        $pdo->commit();

        echo json_encode(['ok' => true, 'msg' => 'Pedido criado!', 'id_pedido' => $id_pedido]);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        exit;
    }
}



/* =====================================================
    2) ALTERAR STATUS DO PEDIDO
===================================================== */
if ($action === 'alterar_status') {

    $id_pedido = $_POST['id_pedido'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$id_pedido || !$status) {
        echo json_encode(['ok' => false, 'msg' => 'Dados inválidos']);
        exit;
    }

    $validos = ['Pendente','Em andamento','A caminho','Concluído','Entregue','Cancelado'];

    if (!in_array($status, $validos)) {
        echo json_encode(['ok' => false, 'msg' => 'Status inválido']);
        exit;
    }

    $sql = $pdo->prepare("UPDATE pedido SET status_pedido = ? WHERE id_pedido = ?");
    $sql->execute([$status, $id_pedido]);

    echo json_encode(['ok' => true, 'msg' => 'Status atualizado!']);
    exit;
}



/* =====================================================
    3) CANCELAR PEDIDO (DELETAR)
===================================================== */
if ($action === 'cancelar_pedido') {

    $id_pedido = $_POST['id_pedido'] ?? null;

    if (!$id_pedido) {
        echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Deletar itens
        $delItens = $pdo->prepare("DELETE FROM item_pedido WHERE id_pedido = ?");
        $delItens->execute([$id_pedido]);

        // Deletar pedido
        $delPed = $pdo->prepare("DELETE FROM pedido WHERE id_pedido = ?");
        $delPed->execute([$id_pedido]);

        $pdo->commit();

        echo json_encode(['ok' => true, 'msg' => 'Pedido cancelado e removido!']);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
        exit;
    }
}


echo json_encode(['ok' => false, 'msg' => 'Ação não reconhecida']);
