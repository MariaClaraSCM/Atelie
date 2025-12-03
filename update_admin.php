<?php
session_start();
require 'config.php'; 


if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'admin' || !isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_admin = $_SESSION['id_usuario'];
// URL de retorno com a seção correta
$url_retorno = 'dashboard.php?secao=config'; 

$nm_usuario = filter_input(INPUT_POST, 'nm_usuario', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$nova_senha = $_POST['senha'] ?? null;
$data_nascimento = $_POST['data_nascimento'] ?? null;


$sql_set = [];
$params = ['id_admin' => $id_admin];


if (!empty($nm_usuario)) {
    $sql_set[] = "nm_usuario = :nm_usuario";
    $params[':nm_usuario'] = $nm_usuario;
}
if (!empty($email)) {
    $sql_set[] = "email = :email";
    $params[':email'] = $email;
}
if (!empty($telefone)) {
    $sql_set[] = "telefone = :telefone";
    $params[':telefone'] = $telefone;
}
if (!empty($data_nascimento)) {
    $sql_set[] = "dt_nasc = :dt_nasc";
    $params[':dt_nasc'] = $data_nascimento;
}


if (!empty($nova_senha)) {
    
    $sql_set[] = "senha = :senha"; 
    $params[':senha'] = password_hash($nova_senha, PASSWORD_DEFAULT);
}



$caminho_foto = $_SESSION['foto'] ?? 'images/users/default.png'; // Foto atual

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $diretorio_destino = 'images/users/';
    
    
    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $nome_novo = uniqid('admin_') . "." . $extensao;
    $caminho_completo = $diretorio_destino . $nome_novo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_completo)) {
        $sql_set[] = "foto = :foto";
        $params[':foto'] = $caminho_completo;
        $caminho_foto = $caminho_completo;
        
        
        $foto_antiga = $_SESSION['foto'] ?? null;
        if ($foto_antiga && $foto_antiga != 'images/users/default.png' && file_exists($foto_antiga)) {
            unlink($foto_antiga);
        }
    }
}



if (empty($sql_set)) {
    header('Location: ' . $url_retorno . '&status=aviso');
    exit;
}

try {
    $sql = "UPDATE usuario SET " . implode(', ', $sql_set) . " WHERE id_usuario = :id_admin";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    
    if (isset($params[':nm_usuario'])) $_SESSION['nm_usuario'] = $nm_usuario;
    if (isset($params[':foto'])) $_SESSION['foto'] = $caminho_foto; 
    
    header('Location: ' . $url_retorno . '&status=success');
    exit;

} catch (PDOException $e) {
   //trata erro
    header('Location: ' . $url_retorno . '&status=error&msg=' . urlencode('Erro ao atualizar: ' . $e->getMessage()));
    exit;
}
?>