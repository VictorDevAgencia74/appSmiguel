<?php
// Importa as configurações do banco de dados
require_once '../../config/database.php';

// Criar conexão
$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar conexão
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Consulta para obter a quantidade de cada tipo de ocorrência
$query = "SELECT ocorrencia, COUNT(*) as quantidade FROM ocorrencia_trafego GROUP BY ocorrencia";
$result = mysqli_query($conexao, $query);

$data = array();

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Retornar os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($data);

mysqli_close($conexao);
?>