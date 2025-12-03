<?php
require 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // 1. Coleta de Dados do Formulário
    $nome          = $_POST['nome'] ?? '';
    $cpf           = $_POST['cpf'] ?? '';
    $dt_nascimento = $_POST['dt_nascimento'] ?? '';
    $telefone      = $_POST['telefone'] ?? '';
    $email         = $_POST['email'] ?? '';
    $senha         = $_POST['senha'] ?? '';
    
    // Inicializa $caminho_foto como NULL (pois a coluna aceita NULL)
    $caminho_foto = NULL;
    $erro_upload = false;

    // 2. Hash da Senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // 3. Processamento do Upload da Foto
    // Certifique-se de que o campo 'foto' existe e não houve erro
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK ){
        $extensao = pathinfo($_FILES['foto'] ['name'], PATHINFO_EXTENSION);
        // Cria um nome único com base no timestamp
        $nome_arquivo = uniqid('user_', true) . '.' . $extensao;
        // Define o caminho de destino (Certifique-se que esta pasta existe e tem permissão de escrita)
        $caminho_foto_destino = 'images/users/' . $nome_arquivo; 

        if(move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_foto_destino)){
            $caminho_foto = $caminho_foto_destino; // Salva o caminho para o banco
        } else {
            $erro_upload = true;
            $erro = 'Erro ao salvar a foto no servidor.';
        }
    } else if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $erro_upload = true;
        $erro = 'Erro no upload da foto. Código: ' . $_FILES['foto']['error'];
    }

    // 4. Inserção no Banco de Dados (Só insere se não houver erro fatal de upload)
    if (!$erro_upload) {
        // CORREÇÃO: A query deve incluir todos os 7 campos obrigatórios (nm_usuario, cpf, dt_nascimento, telefone, email, senha, foto)
        // O campo 'tipo' tem DEFAULT 'user', então pode ser omitido.
        $query = $pdo->prepare("
            INSERT INTO usuario (nm_usuario, cpf, dt_nascimento, telefone, email, senha, foto) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        // CORREÇÃO: Os valores devem ser passados na ordem correta
        $executado = $query->execute([
            $nome, 
            $cpf, 
            $dt_nascimento, 
            $telefone, 
            $email, 
            $senha_hash, 
            $caminho_foto
        ]);
        
        if ($executado) {
             header("Location: login.php");
             exit;
        } else {
             // Tratamento de erro de PDO (ex: CPF/Email duplicado)
             $erro = 'Erro ao cadastrar. Verifique se o CPF ou E-mail já estão em uso.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cadastro - Ateliê</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="stylepagina.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-doce-creme d-flex align-items-center justify-content-center vh-100">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                <div class="card shadow-lg p-4 custom-login-card">
                    <h2 class="text-center mb-4 custom-heading">Cadastre-se</h2>

                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post" enctype="multipart/form-data"> 
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome:</label>
                            <input type="text" name="nome" id="nome" class="form-control" placeholder="Seu nome completo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF (Apenas números):</label>
                            <input type="text" name="cpf" id="cpf" class="form-control" placeholder="Ex: 11122233344" pattern="\d{11}" maxlength="11" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="dt_nascimento" class="form-label">Data de Nascimento:</label>
                            <input type="date" name="dt_nascimento" id="dt_nascimento" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone:</label>
                            <input type="tel" name="telefone" id="telefone" class="form-control" placeholder="(00) 90000-0000" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail:</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="seuemail@exemplo.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha:</label>
                            <input type="password" name="senha" id="senha" class="form-control" placeholder="Crie sua senha" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="foto" class="form-label">Adicione foto ao seu perfil</label>
                            <input class="form-control" type="file" name="foto" id="foto" accept="image/*">
                        </div>
                        
                        <div class="d-grid mt-4">
                            <input type="submit" value="Cadastrar" class="btn btn-primary custom-btn-cta">
                        </div>
                    </form>

                    <p class="text-center mt-3">
                        Já tem conta? <a href="login.php" class="text-doce-rosa-link">Clique aqui</a>
                    </p>
                    
                    <p class="text-center mt-2">
                        <a href="index.php" class="text-secondary custom-btn-link">Voltar para a Página Inicial</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>