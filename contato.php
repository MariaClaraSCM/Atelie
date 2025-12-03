<?php
session_start();
require 'config.php';


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}


$query = $pdo->query("SELECT id_contato, nm_contato, email, mensagem, data_envio FROM contato ORDER BY data_envio DESC");
$mensagens = $query->fetchAll(PDO::FETCH_ASSOC);


$nome_admin = $_SESSION['nm_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="./assets/imagotipo.svg" sizes="any" />
    <title>Mensagens de Contato - Doces da Mary</title>
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
        <h1 class="mb-4 custom-heading-admin text-center">Mensagens de Contato</h1>
        
        <div class="d-flex justify-content-center mb-4">
            <a href="dashboard.php" class="btn btn-secondary custom-btn-link">Voltar para o Dashboard</a>
        </div>

        <?php if (empty($mensagens)): ?>
            <div class="alert alert-info text-center">Nenhuma mensagem recebida.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($mensagens as $m): ?>
                    <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 border-secondary bg-white shadow-sm rounded-3 p-4">
                        <div class="d-flex w-100 justify-content-between">
                          
                            <h5 class="mb-1 fw-bold text-doce-rosa"><?php echo htmlspecialchars($m['nm_contato']); ?> 
                                <small class="text-muted fw-normal">(<?php echo htmlspecialchars($m['email']); ?>)</small>
                            </h5>
                            <small class="text-secondary"><?php echo date('d/m/Y H:i', strtotime($m['data_envio'])); ?></small>
                        </div>
                        <p class="mb-1 mt-3 fs-6 text-dark"><?php echo nl2br(htmlspecialchars($m['mensagem'])); ?></p>
                        <small class="text-end d-block">
                           
                            <a href="deleteservico.php?tipo=contato&id=<?php echo $m['id_contato']; ?>" 
                               class="btn btn-sm custom-btn-rosa-excluir mt-3" 
                               onclick="return confirm('ATENÇÃO: Deseja realmente excluir a mensagem de <?php echo htmlspecialchars($m['nm_contato']); ?>?');">Excluir Mensagem</a>
                        </small>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>