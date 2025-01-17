<?php
require_once 'database.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

if (isset($_GET['motorista'])) {
    $matricula = mysqli_real_escape_string($conexao, $_GET['motorista']);
    
    // Buscar o ID do motorista usando a matrícula
    $query_id_motorista = "SELECT id FROM cadastro_motorista WHERE matricula = '$matricula'";
    $resultado_id_motorista = mysqli_query($conexao, $query_id_motorista) or die("Erro na consulta do ID do motorista: " . mysqli_error($conexao));
    
    if (mysqli_num_rows($resultado_id_motorista) > 0) {
        $row_id_motorista = mysqli_fetch_assoc($resultado_id_motorista);
        $motorista_id = $row_id_motorista['id'];
        
        // Verificar as três últimas ações disciplinares aplicadas ao motorista
        $query_acoes = "SELECT acao, data_acao FROM acoes_disciplinares WHERE motorista_id = '$motorista_id' ORDER BY data_acao DESC LIMIT 3";
        $resultado_acoes = mysqli_query($conexao, $query_acoes) or die("Erro na consulta das ações disciplinares: " . mysqli_error($conexao));
        
        $acoes_disciplinas = [];
        while ($row_acao = mysqli_fetch_assoc($resultado_acoes)) {
            $acoes_disciplinas[] = [
                'acao' => $row_acao['acao'],
                'data' => $row_acao['data_acao']
            ];
        }

        if (empty($acoes_disciplinas)) {
            $acao = 'Orientação';
            $data_acao = date('Y-m-d');  // Data atual
        } else {
            $ultima_acao = $acoes_disciplinas[0]['acao'];
            switch ($ultima_acao) {
                case 'Orientação':
                    $acao = 'Advertência';
                    break;
                case 'Advertência':
                    $acao = 'Suspensão de 1 dia';
                    break;
                case 'Suspensão de 1 dia':
                    $acao = 'Suspensão de 3 dias';
                    break;
                case 'Suspensão de 3 dias':
                    $data_ultima_acao = new DateTime($acoes_disciplinas[0]['data']);
                    $data_atual = new DateTime();
                    $diferenca_meses = $data_atual->diff($data_ultima_acao)->m + ($data_atual->diff($data_ultima_acao)->y * 12);
                    if ($diferenca_meses > 6) {
                        $acao = 'Advertência';
                    } else {
                        $acao = 'Justa Causa';
                    }
                    break;
                default:
                    $acao = 'Orientação';
                    break;
            }
            $data_acao = date('Y-m-d');  // Data atual
        }

        // Exibir a ação disciplinar atual e as três últimas ações
        echo "<h5>Ação Disciplinar: " . htmlspecialchars($acao) . " (Data: " . htmlspecialchars($data_acao) . ")</h5>";
        echo "<h6>Últimas Ações Disciplinares:</h6>";
        echo "<ul>";
        foreach ($acoes_disciplinas as $acao_disciplina) {
            echo "<li>" . htmlspecialchars($acao_disciplina['acao']) . " (Data: " . htmlspecialchars($acao_disciplina['data']) . ")</li>";
        }
        echo "</ul>";

        // Buscar detalhes do motorista e ocorrências de evasão
        $query_ocorrencia = "SELECT * FROM ocorrencia_trafego WHERE motorista = '$matricula' AND ocorrencia = 'Evasão'";
        $resultado_ocorrencia = mysqli_query($conexao, $query_ocorrencia);

        if (mysqli_num_rows($resultado_ocorrencia) > 0) {
            $total_ocorrencias = mysqli_num_rows($resultado_ocorrencia);
            $valor_a_pagar = $total_ocorrencias * 4.80;

            // Coletar as datas das ocorrências e contá-las
            $ocorrencias_por_data = [];
            while ($row = mysqli_fetch_assoc($resultado_ocorrencia)) {
                $data = $row['data'];
                if (isset($ocorrencias_por_data[$data])) {
                    $ocorrencias_por_data[$data]++;
                } else {
                    $ocorrencias_por_data[$data] = 1;
                }
            }
            $datas_ocorrencias_str = '';
            foreach ($ocorrencias_por_data as $data => $quantidade) {
                $datas_ocorrencias_str .= $data . ', ';
            }
            $datas_ocorrencias_str = rtrim($datas_ocorrencias_str, ', ');

            // Buscar nome do motorista
            $query_motorista = "SELECT nome FROM cadastro_motorista WHERE id = '$motorista_id'";
            $resultado_motorista = mysqli_query($conexao, $query_motorista);
            $nome_motorista = '';
            if (mysqli_num_rows($resultado_motorista) > 0) {
                $row_motorista = mysqli_fetch_assoc($resultado_motorista);
                $nome_motorista = $row_motorista['nome'];
            }
            
            echo "<h5>Detalhes do motorista: " . htmlspecialchars($nome_motorista) . "</h5>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped table-hover table-sm'>
                    <tr>
                        <th class='text-center'>OS</th>
                        <th class='text-center'>Data</th>
                        <th class='text-center'>Motorista</th>
                        <th>Descrição</th>
                        <th class='text-center'>Vídeos</th>
                    </tr>";
            mysqli_data_seek($resultado_ocorrencia, 0);  // Resetar o ponteiro do resultado
            while ($row = mysqli_fetch_assoc($resultado_ocorrencia)) {
                echo "<tr>
                        <td class='text-center'>" . htmlspecialchars($row['id']) . "</td>
                        <td class='text-center'>" . htmlspecialchars($row['data']) . "</td>
                        <td class='text-center'>" . htmlspecialchars($row['motorista']) . "</td>
                        <td class='break-word'>" . htmlspecialchars($row['descricao']) . "</td>
                        <td>";
                if ($row['video1']) {
                    echo "<a href='../views/VisualizaVideo.php?video1=" . urlencode($row['id']) . "'>Vídeo-1</a><br>";
                }
                if ($row['video2']) {
                    echo "<a href='../views/VisualizaVideo.php?video2=" . urlencode($row['id']) . "'>Vídeo-2</a><br>";
                }
                if ($row['video3']) {
                    echo "<a href='../views/VisualizaVideo.php?video3=" . urlencode($row['id']) . "'>Vídeo-3</a><br>";
                }
                echo "</td>
                      </tr>";
            }
            echo "</table>";
            echo "</div>";

            // Exibir o valor a pagar e o link para imprimir o termo
            echo "<div class='imprimir_termo'>
                    <p><strong>Total a pagar: R$ " . number_format($valor_a_pagar, 2, ',', '.') . "</strong></p>
                    <p><a href='#' id='imprimir_termo_link' data-motorista='$matricula' data-valor='" . urlencode($valor_a_pagar) . "' data-datas='" . urlencode($datas_ocorrencias_str) . "'>Imprimir Termo</a></p>
                  </div>";
        } else {
            echo "Nenhuma ocorrência de evasão encontrada para o motorista: " . htmlspecialchars($matricula);
        }
    } else {
        echo "Motorista não encontrado com matrícula: " . htmlspecialchars($matricula);
    }
} else {
    echo "Motorista não especificado.";
}

mysqli_close($conexao);
?>

<!-- Botão para imprimir a ação disciplinar -->
<a href="../config/imprimir_acao_disciplinar.php?motorista_id=<?php echo urlencode($motorista_id); ?>" target="_blank">
    <button>Imprimir Ação Disciplinar</button>
</a>


<script>
document.getElementById('imprimir_termo_link').addEventListener('click', function(e) {
    e.preventDefault();
    var motorista = this.getAttribute('data-motorista');
    var valor = this.getAttribute('data-valor');
    var datas = this.getAttribute('data-datas');

    // Enviar uma solicitação AJAX para salvar a nova ação disciplinar
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../config/salvar_acao.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = xhr.responseText;
            if (response.trim() === "Ação disciplinar salva com sucesso.") {
                // Abrir o PDF para impressão
                var popupWindow = window.open('../config/print_term.php?motorista=' + encodeURIComponent(motorista) + '&valor=' + encodeURIComponent(valor) + '&datas=' + encodeURIComponent(datas), '_blank');
                popupWindow.focus();
            } else {
                alert("Erro ao salvar a ação disciplinar: " + response);
            }
        } else {
            alert("Erro ao salvar a ação disciplinar.");
        }
    };
    xhr.send("motorista=" + encodeURIComponent(motorista) + "&acao=" + encodeURIComponent("<?php echo $acao; ?>") + "&data_acao=" + encodeURIComponent("<?php echo $data_acao; ?>"));
});
</script>