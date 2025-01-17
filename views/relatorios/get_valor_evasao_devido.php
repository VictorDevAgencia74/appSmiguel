<?php
// Configurações do banco de dados
$dbHost = "localhost";
$dbUser = "u219851065_admin";
$dbPassword = "Xavier364074$";
$dbName = "u219851065_smiguel";

// Criar conexão
$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar conexão
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Inicializar resposta
$response = array("success" => false, "valor_a_pagar" => 0, "message" => "");

// Buscar a quantidade total de ocorrências de evasão
$query = "SELECT COUNT(*) as total_ocorrencias FROM ocorrencia_trafego WHERE ocorrencia = 'Evasão'";
$resultado = mysqli_query($conexao, $query);

if ($resultado) {
    $row = mysqli_fetch_assoc($resultado);
    $total_ocorrencias = $row['total_ocorrencias'];
    $valor_a_pagar = $total_ocorrencias * 4.80;

    // Configurar resposta
    $response["success"] = true;
    $response["valor_a_pagar"] = number_format($valor_a_pagar, 2, ',', '.');
} else {
    $response["message"] = "Erro ao buscar ocorrências: " . mysqli_error($conexao);
}

// Fechar a conexão
mysqli_close($conexao);

// Retornar resposta em JSON
header('Content-Type: application/json');
echo json_encode($response);
?>