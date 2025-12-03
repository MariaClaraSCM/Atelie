<?php
session_start();
require 'config.php';

// Proteção
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $id > 0;

$produto = [
    'nm_produto' => '',
    'descricao'  => '',
    'preco'      => '',
    'foto_produto' => '',
    'tipo'       => 'Pronta entrega',
    'qt_tamanho' => '',
    'id_categoria' => ''
];

/* ===== Carregar categorias ===== */
$categorias = $pdo->query("SELECT id_categoria, nm_categoria FROM categoria ORDER BY nm_categoria ASC")
                  ->fetchAll(PDO::FETCH_ASSOC);

/* ===== EDITAR: buscar dados ===== */
if ($is_edit) {
    $q = $pdo->prepare("SELECT * FROM produto WHERE id_produto = ?");
    $q->execute([$id]);
    $res = $q->fetch(PDO::FETCH_ASSOC);

    if ($res) {
        $produto = $res;
    } else {
        header("Location: produtos.php");
        exit;
    }
}

/* ========= SALVAR ========= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST['nm_produto'];
    $desc = $_POST['descricao'];
    $preco = $_POST['preco'];
    $tipo = $_POST['tipo'];
    $tam  = $_POST['qt_tamanho'];
    $cat  = $_POST['id_categoria'];

    // Foto atual
    $foto_final = $produto['foto_produto'];

    // Upload de foto
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nomeFoto = 'images/' . uniqid() . "." . $ext;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $nomeFoto)) {

            // Deleta a antiga se existir
            if ($foto_final && file_exists($foto_final)) {
                unlink($foto_final);
            }

            $foto_final = $nomeFoto;
        }
    }

    if ($is_edit) {
        $sql = "UPDATE produto 
                SET nm_produto=?, descricao=?, preco=?, foto_produto=?, tipo=?, qt_tamanho=?, id_categoria=?
                WHERE id_produto=?";
        $pdo->prepare($sql)->execute([$nome,$desc,$preco,$foto_final,$tipo,$tam,$cat,$id]);
    } else {
        $sql = "INSERT INTO produto (nm_produto, descricao, preco, foto_produto, tipo, qt_tamanho, id_categoria)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$nome,$desc,$preco,$foto_final,$tipo,$tam,$cat]);
    }

    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title><?= $is_edit ? "Editar Produto" : "Novo Produto" ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container my-5">

    <h1 class="text-center mb-4"><?= $is_edit ? "Editar Produto" : "Novo Produto" ?></h1>

    <div class="card p-4 shadow">
        <form method="POST" enctype="multipart/form-data">
            
            <!-- Nome -->
            <div class="mb-3">
                <label class="form-label fw-bold">Nome do Produto:</label>
                <input type="text" name="nm_produto" class="form-control"
                       value="<?= htmlspecialchars($produto['nm_produto']) ?>" required>
            </div>

            <!-- Descrição -->
            <div class="mb-3">
                <label class="form-label fw-bold">Descrição:</label>
                <textarea name="descricao" class="form-control" rows="3" required><?= htmlspecialchars($produto['descricao']) ?></textarea>
            </div>

            <!-- Categoria -->
            <div class="mb-3">
                <label class="form-label fw-bold">Categoria:</label>
                <select name="id_categoria" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= $c['id_categoria'] ?>"
                            <?= $produto['id_categoria'] == $c['id_categoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nm_categoria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Tipo -->
            <div class="mb-3">
                <label class="form-label fw-bold">Tipo:</label>
                <select name="tipo" class="form-select" required>
                    <option value="Pronta entrega" <?= $produto['tipo']=='Pronta entrega'?'selected':'' ?>>Pronta entrega</option>
                    <option value="Encomenda" <?= $produto['tipo']=='Encomenda'?'selected':'' ?>>Encomenda</option>
                </select>
            </div>

            <!-- Tamanho -->
            <div class="mb-3">
                <label class="form-label fw-bold">Tamanho / Quantidade:</label>
                <input type="text" name="qt_tamanho" class="form-control"
                       value="<?= htmlspecialchars($produto['qt_tamanho']) ?>">
            </div>

            <!-- Preço -->
            <div class="mb-3">
                <label class="form-label fw-bold">Preço (R$):</label>
                <input type="number" step="0.01" name="preco" class="form-control"
                       value="<?= htmlspecialchars($produto['preco']) ?>" required>
            </div>

            <!-- Foto -->
            <div class="mb-3">
                <label class="form-label fw-bold">Foto:</label>
                <input type="file" name="foto" class="form-control" accept="image/*">

                <?php if ($is_edit && $produto['foto_produto']): ?>
                    <p class="mt-2">Foto atual:</p>
                    <img src="<?= $produto['foto_produto'] ?>" style="max-width:150px;border-radius:5px;">
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-between">
                <button class="btn btn-success"><?= $is_edit ? "Salvar Alterações" : "Cadastrar" ?></button>
                <a href="produtos.php" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>
    </div>

</div>
</body>
</html>
