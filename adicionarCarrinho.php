<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. VERIFICA/CARREGA USUÁRIO
    if (!isset($_SESSION['id_usuario'])) {
        $_SESSION['id_usuario'] = 1; // Temporário
    }
    $id_usuario = $_SESSION['id_usuario'];

    // 2. BUSCA OU CRIA CARRINHO
    $query = $pdo->prepare('SELECT id_carrinho FROM carrinho WHERE id_usuario = ?');
    $query->execute([$id_usuario]);
    $carrinho = $query->fetch();

    if (!$carrinho) {
        // Cria novo carrinho
        $query = $pdo->prepare('INSERT INTO carrinho (id_usuario) VALUES (?)');
        $query->execute([$id_usuario]);
        $id_carrinho = $pdo->lastInsertId();
    } else {
        $id_carrinho = (int) ($_POST['id_carrinho'] ?? 0);
    }

    // 3. RECEBE DEMAIS DADOS
    $id_produto = (int) ($_POST['id_produto'] ?? 0);
    $quantidade = (int) ($_POST['quantidade'] ?? 1);
    $cor_item = !empty($_POST['cor_item']) ? $_POST['cor_item'] : null;
    $nm_personagem = !empty($_POST['nm_personagem']) ? $_POST['nm_personagem'] : null;

    // 4. VALIDAÇÃO BÁSICA
    if ($id_produto <= 0) {
        echo json_encode(['success' => false, 'message' => 'Produto inválido']);
        exit;
    }

    // 5. TENTA INSERIR
    try {
        // Verifica se já existe no carrinho (opcional - atualiza quantidade)
        $query = $pdo->prepare('SELECT id_item_carrinho, quantidade FROM item_carrinho 
                               WHERE id_carrinho = ? AND id_produto = ?');
        $query->execute([$id_carrinho, $id_produto]);
        $itemExistente = $query->fetch();

        if ($itemExistente) {
            // Atualiza quantidade se já existir
            $query = $pdo->prepare('UPDATE item_carrinho 
                                   SET quantidade = quantidade + ?, 
                                       cor_item = ?, 
                                       nm_personagem = ? 
                                   WHERE id_item_carrinho = ?');
            $query->execute([$quantidade, $cor_item, $nm_personagem, $itemExistente['id_item_carrinho']]);
            $acao = 'atualizado';
        } else {
            // Insere novo
            $query = $pdo->prepare('INSERT INTO item_carrinho 
                                   (id_carrinho, id_produto, quantidade, cor_item, nm_personagem) 
                                   VALUES (?, ?, ?, ?, ?)');
            $query->execute([$id_carrinho, $id_produto, $quantidade, $cor_item, $nm_personagem]);
            $acao = 'adicionado';
        }

        // 6. RETORNA SUCESSO
        echo json_encode([
            'success' => true,
            'message' => "Produto $acao ao carrinho!",
            'id_carrinho' => $id_carrinho, // Envia pro JS atualizar
            'acao' => $acao
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco: ' . $e->getMessage()]);
    }
}
