<?php
require_once 'database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

if (isset($_POST['motorista']) && isset($_POST['acao']) && isset($_POST['data_acao'])) {
    $motorista = mysqli_real_escape_string($conexao, $_POST['motorista']);
    $acao = mysqli_real_escape_string($conexao, $_POST['acao']);
    $data_acao = mysqli_real_escape_string($conexao, $_POST['data_acao']);
    
    // Buscar o ID do motorista usando a matrícula
    $query_id_motorista = "SELECT id FROM cadastro_motorista WHERE matricula = '$motorista'";
    $resultado_id_motorista = mysqli_query($conexao, $query_id_motorista) or die("Erro na consulta do ID do motorista: " . mysqli_error($conexao));
    
    if (mysqli_num_rows($resultado_id_motorista) > 0) {
        if (mysqli_num_rows($resultado_id_motorista) > 1) {
            die("Erro: Matrícula duplicada no banco de dados.");
        }

        $row_id_motorista = mysqli_fetch_assoc($resultado_id_motorista);
        $motorista_id = $row_id_motorista['id'];
        
        // Inserir a nova ação disciplinar
        $query_inserir_acao = "INSERT INTO acoes_disciplinares (motorista_id, acao, data_acao) VALUES ('$motorista_id', '$acao', '$data_acao')";
        if (mysqli_query($conexao, $query_inserir_acao)) {
            echo "Ação disciplinar salva com sucesso.";
        } else {
            echo "Erro ao salvar a ação disciplinar: " . mysqli_error($conexao);
        }
    } else {
        echo "Motorista não encontrado com matrícula: " . htmlspecialchars($motorista);
    }
} else {
    echo "Dados não especificados.";
}

mysqli_close($conexao);
?>