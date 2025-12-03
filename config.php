<?php

$host='localhost';
$banco='db_atelie';
$usuario='root';
$senha='';
try {
    $pdo=new PDO("mysql:host=$host;dbname=$banco", $usuario, $senha);
} catch(PDOException $e){
    die("erro na conexão: ". $e->getMessage());
}

?>