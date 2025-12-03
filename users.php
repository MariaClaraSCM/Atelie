<?php
session_start();
require 'config.php';


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}


$query = $pdo->query("SELECT id_usuario, nm_usuario, email, tipo FROM usuario ORDER BY tipo DESC, nm_usuario ASC");
$usuarios = $query->fetchAll(PDO::FETCH_ASSOC);


$nome_admin = $_SESSION['nm_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários - Doces da Mary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stylepagina.css">
</head>
<body class="bg-doce-creme">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark custom-navbar-admin shadow-sm">
            <div class="container">
                <a class="navbar-brand logo-doce" href="dashboard.php">Admin: Doces da Mary</a>
                <div class="d-flex align-items-center ms-auto">
                    <span class="navbar-text me-3 text-white">
                        Olá, **<?php echo htmlspecialchars($nome_admin); ?>**
                    </span>
                    <a href="logout.php" class="btn btn-danger btn-sm rounded-pill fw-bold">Sair</a>
                </div>
            </div>
        </nav>
    </header>
    
    <div class="container my-5">
        <h1 class="mb-4 custom-heading-admin text-center">Gerenciamento de Usuários</h1>
        
        <div class="d-flex justify-content-center mb-3">
            <a href="dashboard.php" class="btn btn-secondary custom-btn-link">Voltar para o Dashboard</a>
        </div>

        <?php if (empty($usuarios)): ?>
            <div class="alert alert-info text-center">Nenhum usuário cadastrado.</div>
        <?php else: ?>
            <div class="table-responsive bg-white p-3 rounded shadow-sm">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-primary custom-bg-header-table">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Tipo de Acesso</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                         
                            <tr class="<?php echo ($u['tipo'] == 'admin') ? 'table-warning' : ''; ?>">
                            
                                <td><?php echo htmlspecialchars($u['id_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($u['nm_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo ucfirst($u['tipo']); ?></td>
                                <td>
                                  
                                    <?php if ($u['id_usuario'] != $_SESSION['id_usuario']): ?>
                                        <a href="deleteservico.php?tipo=usuario&id=<?php echo $u['id_usuario']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('ATENÇÃO: Deseja realmente excluir o usuário <?php echo htmlspecialchars($u['nm_usuario']); ?>?');">Excluir</a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Você (Logado)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>