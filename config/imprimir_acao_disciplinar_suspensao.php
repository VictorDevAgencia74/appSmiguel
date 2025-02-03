<?php
// Incluir o arquivo de conexão com o banco de dados
include 'database.php';

// Verificar se o parâmetro motorista_id foi fornecido
if (!isset($_GET['motorista_id'])) {
    die("ID do motorista não fornecido.");
}

$motorista_id = mysqli_real_escape_string($conexao, $_GET['motorista_id']);

// Buscar a matrícula do motorista
$query_matricula = "SELECT matricula, nome FROM cadastro_motorista WHERE id = '$motorista_id'";
$resultado_matricula = mysqli_query($conexao, $query_matricula);

if (!$resultado_matricula || mysqli_num_rows($resultado_matricula) == 0) {
    die("Motorista não encontrado.");
}

$row_matricula = mysqli_fetch_assoc($resultado_matricula);
$matricula = $row_matricula['matricula'];
$nome_motorista = $row_matricula['nome'];

// Buscar as três últimas ações disciplinares
$query_acoes = "SELECT acao, data_acao FROM acoes_disciplinares WHERE motorista_id = '$motorista_id' ORDER BY data_acao DESC LIMIT 3";
$resultado_acoes = mysqli_query($conexao, $query_acoes);

$acoes_disciplinas = [];
while ($row_acao = mysqli_fetch_assoc($resultado_acoes)) {
    $acoes_disciplinas[] = [
        'acao' => $row_acao['acao'],
        'data' => $row_acao['data_acao']
    ];
}

// Determinar a próxima ação disciplinar
if (empty($acoes_disciplinas)) {
    $acao_disciplinar = 'Orientação';
} else {
    $ultima_acao = $acoes_disciplinas[0]['acao'];
    switch ($ultima_acao) {
        case 'Orientação':
            $acao_disciplinar = 'Advertência';
            break;
        case 'Advertência':
            $acao_disciplinar = 'Suspensão de 1 dia';
            break;
        case 'Suspensão de 1 dia':
            $acao_disciplinar = 'Suspensão de 3 dias';
            break;
        case 'Suspensão de 3 dias':
            $data_ultima_acao = new DateTime($acoes_disciplinas[0]['data']);
            $data_atual = new DateTime();
            $diferenca_meses = $data_atual->diff($data_ultima_acao)->m + ($data_atual->diff($data_ultima_acao)->y * 12);
            $acao_disciplinar = $diferenca_meses > 6 ? 'Advertência' : 'Justa Causa';
            break;
        default:
            $acao_disciplinar = 'Orientação';
            break;
    }
}

// Buscar ocorrências do motorista
$query_ocorrencias = "SELECT id, data, motorista, descricao, horario FROM ocorrencia_trafego WHERE motorista = '$matricula'";
$resultado_ocorrencias = mysqli_query($conexao, $query_ocorrencias);

$ocorrencias = [];
if ($resultado_ocorrencias && mysqli_num_rows($resultado_ocorrencias) > 0) {
    while ($row_ocorrencia = mysqli_fetch_assoc($resultado_ocorrencias)) {
        $ocorrencias[] = $row_ocorrencia;
    }
}

// --- FUNÇÃO PARA CALCULAR DATA DE RETORNO ---
function calcularDataRetorno($data_atual, $dias_suspensao) {
    $meses = [
        1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];
    
    $data = new DateTime($data_atual);
    $data->modify("+$dias_suspensao days");
    
    $dia = $data->format('d');
    $mes = $meses[(int)$data->format('m')];
    $ano = $data->format('Y');
    
    return "$dia de $mes de $ano";
}

// --- DETERMINAR AÇÃO DISCIPLINAR ---
if (empty($acoes_disciplinas)) {
    $acao_disciplinar = 'Orientação';
} else {
    $ultima_acao = $acoes_disciplinas[0]['acao'];
    switch ($ultima_acao) {
        case 'Orientação':
            $acao_disciplinar = 'Advertência';
            break;
        case 'Advertência':
            $acao_disciplinar = 'Suspensão de 1 dia';
            break;
        case 'Suspensão de 1 dia':
            $acao_disciplinar = 'Suspensão de 3 dias';
            break;
        case 'Suspensão de 3 dias':
            $data_ultima_acao = new DateTime($acoes_disciplinas[0]['data']);
            $data_atual = new DateTime();
            $diferenca_meses = $data_atual->diff($data_ultima_acao)->m + ($data_atual->diff($data_ultima_acao)->y * 12);
            $acao_disciplinar = $diferenca_meses > 6 ? 'Advertência' : 'Justa Causa';
            break;
        default:
            $acao_disciplinar = 'Orientação';
            break;
    }
}

// --- CALCULAR DIAS DE SUSPENSÃO ---
$dias_suspensao = 0;
if ($acao_disciplinar == 'Suspensão de 1 dia') {
    $dias_suspensao = 1;
} elseif ($acao_disciplinar == 'Suspensão de 3 dias') {
    $dias_suspensao = 3;
}

// --- CALCULAR DATA DE RETORNO ---
if ($dias_suspensao > 0) {
    $data_retorno = calcularDataRetorno(date('Y-m-d'), $dias_suspensao); // Linha 111 (exemplo)
} else {
    $data_retorno = "Data não aplicável";
}

// Função para formatar a data por extenso
function formatarDataExtenso($data) {
    $meses = [
        1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 
        'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];
    
    $dia = date('d', strtotime($data));
    $mes = date('n', strtotime($data));
    $ano = date('Y', strtotime($data));
    
    return "$dia de " . $meses[$mes] . " de $ano";
}

$data_acao = date('d/m/Y');
$data_atual_extenso = formatarDataExtenso(date('Y-m-d'));

// Fechar conexão com o banco de dados
mysqli_close($conexao);
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Imprimir Ação Disciplinar</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/styles.css">
    </head>
    <body onload="window.print(); setTimeout(showPopup, 1000);">
        <div class="container">
            <div class="nav">
                <table border="1">
                    <tr>
                        <td>
                            <div class="logo">
                                <img src="../assets/img/logo/logo.png" alt="logo-sm">
                            </div>
                        </td>
                        <td class="dados_cabecalho">
                            <h2>SUSPENSÃO</h2>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div>
                <h3>TRANSPORTE URBANO SÃO MIGUEL DE ILHÉUS LTDA.</h3>
                <h3>AVISO DE SUSPENÇÃO</h3>
            </div>
            <div>
                <p class="justificar">Senhor <strong><?php echo htmlspecialchars($nome_motorista); ?></strong>, matricula <strong>(<?php echo htmlspecialchars($matricula); ?>)</strong>, função <strong>MOTORISTA</strong> <br><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Pelo presente notificamos que a partir de <strong><?php echo htmlspecialchars($data_acao); ?></strong> está suspenso do exercício de suas funções, pelo prazo de <strong>01(um) dia sem vencimentos, por indisciplina, gerando as ocorrências abaixo:</strong>

                </p>

            </div>
            <table border="1">
                <tr>
                    <th>OS</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Descrição</th>
                </tr>
                <?php if (!empty($ocorrencias)) { ?>
                    <?php foreach ($ocorrencias as $ocorrencia) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ocorrencia['id']); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['data']); ?></td>
                        <td><?php echo htmlspecialchars($ocorrencia['horario']); ?></td>
                        <td class="dados_table"><?php echo htmlspecialchars($ocorrencia['descricao']); ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5">Nenhuma ocorrência encontrada.</td></tr>
                <?php } ?>
            </table>
            <div class="cabecalho_assinatura">
                <p class="justificar">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fica advertido de que, caso volte a reincidir diante das ocorrências já verificadas no decorrer do contrato de trabalho, será despedido por <strong>"Justa Causa" nos termos do Art. 182 da CLT, letra "E".</strong><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Conforme Regulamento Interno Art.(09), que diz o seguinte: "O empregado se obriga a acatar todas as ordens dos seus superiores hierárquicos, não se permitindo, em hipótese alguma, o ato de indisciplina ou de insubordinação.<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Portanto, apresentar-se ao serviço no horário usual, no dia <strong><?php echo $dias_suspensao > 0 ? $data_retorno : '(CALCULO AUTOMÁTICO)'; ?></strong>, assim pedimos a confirmação com o seu ciente.</p>
                <p class="dataatual">Ilhéus, <?php echo $data_atual_extenso; ?></p>
            </div>
            
            <div class="assinatura">
                <div>
                    <P> ____________________________________</P>
                    <p class="assinar"><?php echo htmlspecialchars($nome_motorista); ?></p>
                </div>
                <div>
                    <P>_____________________________________</P>
                    <p class="assinar">Transporte Urbano São Miguel de Ilhéus LTDA</p>
                </div>
            </div>
            <div class="assinatura">
                <div>
                    <P>____________________________________</P>
                    <p class="assinar">Testemunha 1:</p>
                    <p>CPF:________________________________</p>
                </div>
                <div>
                    <P>____________________________________</P>
                    <p class="assinar">Testemunha 2:</p>
                    <p>CPF:________________________________</p>
                </div>
            </div>
        </div>
    </body>
</html>