<?php
// Importa as configurações do banco de dados
require_once 'database.php';

// Criar conexão
$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar conexão
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Verifica se o ID da ocorrência foi passado na URL
if (isset($_GET['id'])) {
    $id_ocorrencia = intval($_GET['id']); // Certifique-se de converter o valor para um inteiro

    // Busca os dados da tabela ocorrencia_trafego
    $query_dados = "SELECT * FROM ocorrencia_trafego WHERE id = ?";
    $stmt_dados = mysqli_prepare($conexao, $query_dados);
    mysqli_stmt_bind_param($stmt_dados, 'i', $id_ocorrencia);
    mysqli_stmt_execute($stmt_dados);
    $resultado_dados = mysqli_stmt_get_result($stmt_dados);

    if ($resultado_dados && $dados = mysqli_fetch_assoc($resultado_dados)) {
        // Atribuindo os valores das colunas a variáveis
        $data = $dados['data'];
        $horario = $dados['horario'];
        $fiscal = $dados['fiscal'];
        $motorista = $dados['motorista'];
        $carro = $dados['carro'];
        $linha = $dados['linha'];
        $ocorrencia = $dados['ocorrencia'];
        $descricao = $dados['descricao'];
        $acao = $dados['acao'];
        $observacoes = $dados['observacoes'];
        $video1 = $dados['video1'];
        $video2 = $dados['video2'];
        $video3 = $dados['video3'];

        // Insere os dados na tabela ocorrencia_finalizada
        $query_move_ocorrencia = "
            INSERT INTO ocorrencia_finalizada 
            (data, horario, fiscal, motorista, carro, linha, ocorrencia, descricao, acao, observacoes, video1, video2, video3)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt_move = mysqli_prepare($conexao, $query_move_ocorrencia);
        mysqli_stmt_bind_param($stmt_move, 'sssssssssssss', $data, $horario, $fiscal, $motorista, $carro, $linha, $ocorrencia, $descricao, $acao, $observacoes, $video1, $video2, $video3);
        $resultado_move = mysqli_stmt_execute($stmt_move);

        if ($resultado_move) {
            echo "Ocorrência movida com sucesso para ocorrencia_finalizada.<br>";

            // Remove a ocorrência da tabela original
            $query_excluir_ocorrencia = "DELETE FROM ocorrencia_trafego WHERE id = ?";
            $stmt_excluir = mysqli_prepare($conexao, $query_excluir_ocorrencia);
            mysqli_stmt_bind_param($stmt_excluir, 'i', $id_ocorrencia);
            $resultado_excluir = mysqli_stmt_execute($stmt_excluir);

            if ($resultado_excluir) {
                echo "Ocorrência finalizada com sucesso!";
            } else {
                echo "Erro ao excluir ocorrência da tabela original: " . mysqli_error($conexao);
            }
        } else {
            echo "Erro ao mover ocorrência para outra tabela: " . mysqli_error($conexao);
        }
    } else {
        echo "Nenhum dado encontrado para o ID fornecido.";
        exit;
    }
} else {
    echo "ID da ocorrência não especificado.";
}

mysqli_close($conexao);
?>