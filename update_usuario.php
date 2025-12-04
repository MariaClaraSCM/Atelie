<?php
session_start();
require 'config.php'; 


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$url_retorno = 'dashboard_user.php?section=conta'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
    $nome_usuario = $_POST['nm_usuario'] ?? '';
    $email        = $_POST['email'] ?? '';
    $telefone     = $_POST['telefone'] ?? '';
    $nova_senha   = $_POST['senha'] ?? '';
    
    
    $sql_campos = [];
    $params = [];
    
    
    if (!empty($nome_usuario)) {
        $sql_campos[] = "nm_usuario = ?";
        $params[] = $nome_usuario;
    }
    
    if (!empty($email)) {
        $sql_campos[] = "email = ?";
        $params[] = $email;
    }
    
    if (!empty($telefone)) {
        $sql_campos[] = "telefone = ?";
        $params[] = $telefone;
    }
    
    
    if (!empty($nova_senha)) {
       
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $sql_campos[] = "senha = ?";
        $params[] = $senha_hash;
    }
    
   
    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
      
        $upload_dir = 'uploads/perfil/'; 
        
        if (!is_dir($upload_dir)) {
           
            mkdir($upload_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novo_nome = $id_usuario . '_' . time() . '.' . $ext;
        $foto_path = $upload_dir . $novo_nome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_path)) {
            $sql_campos[] = "foto = ?";
            $params[] = $foto_path;
            $_SESSION['foto'] = $foto_path; 
        } else {
            
        }
    }
    
    
    if (!empty($sql_campos)) {
        $sql = "UPDATE usuario SET " . implode(', ', $sql_campos) . " WHERE id_usuario = ?";
        $params[] = $id_usuario;
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            
            if (!empty($nome_usuario)) {
                $_SESSION['nm_usuario'] = $nome_usuario;
            }
            
            
            header('Location: ' . $url_retorno . '&status=sucesso');
            exit;
            
        } catch (PDOException $e) {
           
            header('Location: ' . $url_retorno . '&status=erro&msg=' . urlencode('Erro ao atualizar o banco de dados.'));
            exit;
        }
    } else {
        
        header('Location: ' . $url_retorno . '&status=aviso&msg=' . urlencode('Nenhuma alteração foi enviada.'));
        exit;
    }
    
} else {
   
    header('Location: ' . $url_retorno);
    exit;
}
?>