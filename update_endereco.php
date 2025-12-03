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
    
   
    $cep           = $_POST['cep'] ?? '';
    $rua           = $_POST['rua'] ?? '';
    $numero        = $_POST['numero'] ?? '';
    $bairro        = $_POST['bairro'] ?? '';
    $cidade        = $_POST['cidade'] ?? '';
    $estado        = $_POST['estado'] ?? '';
    $complemento   = $_POST['complemento'] ?? '';
    
    
    $sql_check = $pdo->prepare("SELECT COUNT(*) FROM endereco WHERE id_usuario = ?");
    $sql_check->execute([$id_usuario]);
    $exists = $sql_check->fetchColumn();
    
    try {
        if ($exists > 0) {
           
            $sql = "
                UPDATE endereco SET 
                    cep = ?, rua = ?, numero = ?, bairro = ?, cidade = ?, estado = ?, complemento = ? 
                WHERE id_usuario = ?
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$cep, $rua, $numero, $bairro, $cidade, $estado, $complemento, $id_usuario]);
        } else {
            
            $sql = "
                INSERT INTO endereco 
                (id_usuario, cep, rua, numero, bairro, cidade, estado, complemento) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_usuario, $cep, $rua, $numero, $bairro, $cidade, $estado, $complemento]);
        }
        
       
        header('Location: ' . $url_retorno . '&status=sucesso_end');
        exit;
        
    } catch (PDOException $e) {
        
        header('Location: ' . $url_retorno . '&status=erro&msg=' . urlencode('Erro ao atualizar o endereço no banco de dados.'));
        exit;
    }
} else {
   
    header('Location: ' . $url_retorno);
    exit;
}
?>