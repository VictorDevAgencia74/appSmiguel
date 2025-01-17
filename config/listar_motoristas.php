<?php
// Importa as configurações do banco de dados
require_once 'database.php';

// Criar conexão
$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar conexão
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Buscar motoristas com "Evasão" e ordenar numericamente
$query = "SELECT DISTINCT motorista FROM ocorrencia_trafego WHERE ocorrencia = 'Evasão' ORDER BY CAST(motorista AS UNSIGNED) ASC";
$resultado = mysqli_query($conexao, $query);

if (mysqli_num_rows($resultado) > 0) {
    echo "<ul>";
    while($row = mysqli_fetch_assoc($resultado)) {
        echo "<li><a href='../views/detalhesMotoristasEvasao.php?motorista=" . urlencode($row['motorista']) . "'>" . htmlspecialchars($row['motorista']) . "</a></li>";
    }
    echo "</ul>";
} else {
    echo "Nenhum motorista encontrado com ocorrência de evasão.";
}

mysqli_close($conexao);
?>
