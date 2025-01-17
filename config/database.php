<?php
// Configurações do banco de dados
$dbHost = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "u219851065_smiguel";

// Criar conexão
$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar conexão
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}
?>