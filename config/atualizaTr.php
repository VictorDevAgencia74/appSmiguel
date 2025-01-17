<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado e tem permissão (status)
if (!isset($_SESSION['user']) || ($_SESSION['status'] != 2 && $_SESSION['status'] != 10 && $_SESSION['status'] != 11)) {
    // Se não estiver logado ou não tiver permissão, redireciona para a página de login
    header("Location: index.php");
    exit();
}

// Importa as configurações do banco de dados
require_once 'database.php';

// Verifica se a conexão foi estabelecida com sucesso
if (!$conexao) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

// Verifica se os dados foram enviados via método POST e se os campos necessários estão definidos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_ocorrencia"]) && isset($_POST["observacoes"]) && isset($_POST["acao"])) {
    // Recupera os dados do formulário
    $id = $_POST["id_ocorrencia"];
    $observacoes = $_POST["observacoes"];
    $acao = $_POST["acao"];

    // Prepara a consulta SQL para atualizar as observações e a ação
    $sql = "UPDATE u219851065_smiguel.ocorrencia_trafego SET observacoes = ?, acao = ? WHERE id = ?";
    
    // Prepara a declaração SQL
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        // Associa as variáveis aos parâmetros da declaração preparada
        mysqli_stmt_bind_param($stmt, "ssi", $observacoes, $acao, $id);

        // Executa a declaração
        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='fs-4'>Atualizações efetuadas com sucesso!!!<div/><br>";
            
            // Query para mover a ocorrência para outra tabela
            $query_move_ocorrencia = "INSERT INTO u219851065_smiguel.ocorrencia_finalizada SELECT * FROM u219851065_smiguel.ocorrencia_trafego WHERE id = $id";

            // Executa a query
            $resultado_move = mysqli_query($conexao, $query_move_ocorrencia);

            if ($resultado_move) {
                // Se o movimento foi bem-sucedido, exclua a ocorrência da tabela original
                $query_excluir_ocorrencia = "DELETE FROM u219851065_smiguel.ocorrencia_trafego WHERE id = $id";
                $resultado_excluir = mysqli_query($conexao, $query_excluir_ocorrencia);

                if ($resultado_excluir) {
                    echo "<div class='fs-4'>Ocorrência finalizada com sucesso!!!</div>";
                } else {
                    echo "Erro ao excluir ocorrência da tabela original.";
                }
            } else {
                echo "Erro ao mover ocorrência para outra tabela.";
            }
        } else {
            echo "Erro ao executar a declaração: " . mysqli_error($conexao);
        }

        // Fecha a declaração
        mysqli_stmt_close($stmt);
    } else {
        echo "Erro ao preparar a declaração: " . mysqli_error($conexao);
    }

    // Fecha a conexão com o banco de dados
    mysqli_close($conexao);
} else {
    // Se os dados não foram enviados corretamente, exibe uma mensagem de erro
    echo "Erro: Dados não recebidos corretamente.";
}
?>