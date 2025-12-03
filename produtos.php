<?php
require 'config.php';

// Proteção
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

/* =======================================
   DELETE via GET: produtos.php?delete=ID
   ======================================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Buscar foto antes de deletar
    $q = $pdo->prepare("SELECT foto_produto FROM produto WHERE id_produto = ?");
    $q->execute([$id]);
    $foto = $q->fetchColumn();

    // Excluir do BD
    $del = $pdo->prepare("DELETE FROM produto WHERE id_produto = ?");
    $del->execute([$id]);

    // Excluir foto física se existir
    if ($foto && file_exists($foto)) {
        unlink($foto);
    }

    header("Location: produtos.php?msg=excluido");
    exit;
}

/* =======================================
   LISTAGEM
   ======================================= */
$stmt = $pdo->query("SELECT p.*, c.nm_categoria 
                     FROM produto p
                     INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                     ORDER BY p.id_produto DESC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Produtos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">

    <h1 class="mb-4 text-center">Lista de Produtos</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'excluido'): ?>
        <div class="alert alert-success">Produto excluído com sucesso!</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Tipo</th>
                <th>Preço</th>
                <th>Tam.</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($produtos as $p): ?>
            <tr>
                <td>
                    <?php if ($p['foto_produto']): ?>
                        <img src="<?= $p['foto_produto'] ?>" style="width:70px; border-radius:4px; height:70px; object-fit:contain;">
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($p['nm_produto']) ?></td>
                <td><?= htmlspecialchars($p['nm_categoria']) ?></td>
                <td><?= htmlspecialchars($p['tipo']) ?></td>
                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($p['qt_tamanho']) ?></td>
                <td>
                    <a href="produto_form.php?id=<?= $p['id_produto'] ?>" class="btn btn-primary btn-sm">Editar</a>

                    <a href="?delete=<?= $p['id_produto'] ?>"
                       onclick="return confirm('Deseja realmente excluir?')"
                       class="btn btn-danger btn-sm">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>

    <?php include 'footer.php'; ?>
</body>
</html>
