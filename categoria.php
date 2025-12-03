<?php
require 'config.php';
session_start();

/* ============================================
   DELETE (React) — via categoria.php?id=#
   ============================================ */
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    header("Content-Type: application/json; charset=UTF-8");

    // pega o ID da query string
    parse_str($_SERVER["QUERY_STRING"] ?? "", $query);
    $id = $query["id"] ?? null;

    if (!$id) {
        echo json_encode(["erro" => "ID não enviado"]);
        exit;
    }

    try {
        $del = $pdo->prepare("DELETE FROM categoria WHERE id_categoria = ?");
        $del->execute([$id]);

        echo json_encode(["sucesso" => true, "mensagem" => "Categoria excluída com sucesso!"]);
        exit;

    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao excluir categoria"]);
        exit;
    }
}

/* ============================================
   PUT (React)
   ============================================ */
if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    header("Content-Type: application/json; charset=UTF-8");

    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["erro" => "JSON inválido"]);
        exit;
    }

    $id   = $input["id_categoria"] ?? null;
    $nome = trim($input["nm_categoria"] ?? "");

    if (!$id || $nome === "") {
        echo json_encode(["erro" => "Dados incompletos"]);
        exit;
    }

    try {
        // Verifica duplicidade
        $chk = $pdo->prepare("SELECT id_categoria FROM categoria WHERE nm_categoria = ? AND id_categoria != ?");
        $chk->execute([$nome, $id]);

        if ($chk->rowCount() > 0) {
            echo json_encode(["erro" => "Categoria já existe"]);
            exit;
        }

        // Atualiza
        $upd = $pdo->prepare("UPDATE categoria SET nm_categoria = ? WHERE id_categoria = ?");
        $upd->execute([$nome, $id]);

        echo json_encode(["sucesso" => true, "mensagem" => "Categoria atualizada com sucesso!"]);
        exit;

    } catch (Exception $e) {
        echo json_encode(["erro" => "Erro ao atualizar categoria"]);
        exit;
    }
}

/* ============================================
   POST (form)
   ============================================ */

$mensagem = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nm_categoria"] ?? "");

    if ($nome === "") {
        $erro = "Digite o nome da categoria!";
    } else {
        try {
            $chk = $pdo->prepare("SELECT id_categoria FROM categoria WHERE nm_categoria = ?");
            $chk->execute([$nome]);

            if ($chk->rowCount() > 0) {
                $erro = "Categoria já existe!";
            } else {
                $ins = $pdo->prepare("INSERT INTO categoria (nm_categoria) VALUES (?)");
                $ins->execute([$nome]);

                header('Location: dashboard.php?secao=categorias');
                exit;
            }
        } catch (Exception $e) {
            $erro = "Erro ao cadastrar categoria.";
        }
    }
}

/* ============================================
   GET — mostrar HTML do formulário
   ============================================ */
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Categoria</title>

    <style>
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
            font-family: Arial, sans-serif;
        }
        .modal h2 { margin-bottom: 15px; }
        .modal label { display: block; margin-bottom: 6px; font-weight: bold; }
        .modal input { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #aaa; }
        .buttons { display: flex; gap: 10px; margin-top: 10px; }
        .buttons button { flex: 1; padding: 10px; border: none; cursor: pointer; border-radius: 4px; font-weight: bold; }
        .salvar { background: #4caf50; color: white; }
        .cancelar { background: #888; color: white; }
        .alerta { padding: 10px; margin-bottom: 15px; background: #ffdddd; color: #b30000; border-left: 4px solid #b30000; }
        .sucesso { padding: 10px; margin-bottom: 15px; background: #ddffdd; color: #008000; border-left: 4px solid #008000; }
    </style>
</head>

<body>

    <div class="overlay">
        <div class="modal">
            <h2>Adicionar Categoria</h2>

            <?php if ($erro): ?>
                <div class="alerta"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <?php if ($mensagem): ?>
                <div class="sucesso"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <form method="POST">
                <label>Nome da categoria:</label>
                <input type="text" name="nm_categoria" placeholder="Digite aqui...">

                <div class="buttons">
                    <button type="submit" class="salvar">Salvar</button>

                    <button type="button" class="cancelar"
                        onclick="window.location.href='dashboard.php?secao=categorias'">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
