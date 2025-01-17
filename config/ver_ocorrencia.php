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

    // Prepara e executa a consulta para obter os detalhes da ocorrência com base no ID
    $consulta_sql = "SELECT * FROM ocorrencia_trafego WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $consulta_sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_ocorrencia);
    mysqli_stmt_execute($stmt);
    $resultado_consulta = mysqli_stmt_get_result($stmt);

    // Verifica se a consulta retornou resultados
    if ($resultado_consulta && mysqli_num_rows($resultado_consulta) > 0) {
        // Recupera os detalhes da ocorrência
        $detalhes_ocorrencia = mysqli_fetch_assoc($resultado_consulta);
        // Define os caminhos dos arquivos de vídeo
        $caminho_arquivo1 = $detalhes_ocorrencia['video1'];
        $caminho_arquivo2 = $detalhes_ocorrencia['video2'];
        $caminho_arquivo3 = $detalhes_ocorrencia['video3'];

        // Exibe os detalhes da ocorrência
        echo "<div class='container detalhes-ocorrencia'>";
        echo "<div class='row my-2' style='color: #5a5f64;'>";
        echo "<div class='col-4 detalhes-info-1' style='border-right: solid 1px #d8dee3;'>";
        echo "<p><strong>ID Ocorrência:</strong> " . htmlspecialchars($detalhes_ocorrencia['id']) . "</p>";
        echo "<p><strong>Data:</strong> " . htmlspecialchars($detalhes_ocorrencia['data']) . "</p>";
        echo "<p><strong>Horário:</strong> " . htmlspecialchars($detalhes_ocorrencia['horario']) . "</p>";
        echo "<p><strong>Fiscal:</strong> " . htmlspecialchars($detalhes_ocorrencia['fiscal']) . "</p>";

        echo "<div class='d-flex'>";
        echo "<p><strong>Link:</strong></p>";

        // Constrói o caminho do arquivo de vídeo
        if ($caminho_arquivo1) {
            echo "<a class='mx-2 link-danger' href='VisualizaVideo.php?video1=" . htmlspecialchars($detalhes_ocorrencia['id']) . "'>Vídeo-1</a>";
        }
        if ($caminho_arquivo2) {
            echo "<a class='mx-2 link-danger' href='VisualizaVideo.php?video2=" . htmlspecialchars($detalhes_ocorrencia['id']) . "'>Vídeo-2</a>";
        }
        if ($caminho_arquivo3) {
            echo "<a class='mx-2 link-danger' href='VisualizaVideo.php?video3=" . htmlspecialchars($detalhes_ocorrencia['id']) . "'>Vídeo-3</a>";
        }
        echo "</div>";
        echo "</div>";

        echo "<div class='col-8 detalhes-info-2'>";
        echo "<p><strong>Linha:</strong> " . htmlspecialchars($detalhes_ocorrencia['linha']) . "</p>";
        echo "<p><strong>Carro:</strong> " . htmlspecialchars($detalhes_ocorrencia['carro']) . "</p>";
        echo "<p><strong>Motorista:</strong> " . htmlspecialchars($detalhes_ocorrencia['motorista']) . "</p>";
        echo "<p><strong>Ocorrência:</strong> " . htmlspecialchars($detalhes_ocorrencia['ocorrencia']) . "</p>";
        echo "<p class='text-wrap w-100'><strong>Descrição:</strong> " . htmlspecialchars($detalhes_ocorrencia['descricao']) . "</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";

        // Adiciona uma linha separadora
        echo "<div style='border-top: 1px solid #d8dee3;'></div>";

        // Adicionar um formulário para enviar as informações do motorista, ação e observações
        echo "<div class='container'>";
        echo "<form action='../views/atualiza.php' method='get'>";
        echo "<div class='row my-3'>";

        // Div para o campo de texto de observações
        echo "<div class='col-6'>";
        echo "<label for='observacoes'>Observações:</label>";
        echo "<textarea class='form-control' name='observacoes' id='observacoes' rows='3' required></textarea>";
        echo "</div>";

        // Div para o campo de seleção de ação
        echo "<div class='col-6'>";
        echo "<label for='acao'>Ação:</label>";
        echo "<select class='form-select' name='acao' id='acao' required>";
        echo "<option value=''>Informe a Ação tomada</option>";

        // Consulta para obter os tipos de ação
        $query = "SELECT id, descricao FROM cadastroTipoAcao";
        $resultado = mysqli_query($conexao, $query);

        if ($resultado) {
            // Preencher o select com as opções do banco de dados
            while ($row = mysqli_fetch_assoc($resultado)) {
                $id = htmlspecialchars($row['id']);
                $descricao = htmlspecialchars($row['descricao']);
                echo "<option value='$id'>$descricao</option>";
            }
        } else {
            echo "<option value=''>Nenhuma ação disponível</option>";
        }

        echo "</select>";

        // Adicionar um campo oculto para enviar o ID da ocorrência
        echo "<input type='hidden' name='id' value='" . htmlspecialchars($id_ocorrencia) . "'>";

        // Botão de envio do formulário
        echo "<input class='btn btn-outline-danger mt-2' type='submit' value='>>>  FINALIZAR OS  <<<'>";
        echo "</div>";

        echo "</div>";
        echo "</form>";
        echo "</div>";

    } else {
        // Se não houver resultados para o ID especificado, exiba uma mensagem de erro
        echo "Ocorrência não encontrada.";
    }

    // Fecha a conexão com o banco de dados
    mysqli_close($conexao);
} else {
    // Se o ID da ocorrência não foi passado na URL, redireciona para a página anterior
    header("Location: pagina_anterior.php"); // Substitua "pagina_anterior.php" pelo nome da página anterior
    exit();
}
?>