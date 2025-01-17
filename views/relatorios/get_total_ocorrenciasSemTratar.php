<?php
// Importa as configurações do banco de dados
require_once '../../config/database.php';

// Criar conexão
$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar conexão
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Consulta para obter a quantidade total de ocorrências
$query = "SELECT COUNT(*) as total_ocorrencias FROM ocorrencia_trafego";
$result = mysqli_query($conexao, $query);

$data = mysqli_fetch_assoc($result);

// Retornar o dado em formato JSON
echo json_encode($data);

mysqli_close($conexao);
?>