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

$data_acao = date('Y-m-d');

// Fechar a conexão com o banco de dados
mysqli_close($conexao);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Ação Disciplinar</title>
</head>
<body onload="window.print();">
    <div class="container">
        <h1>Ação Disciplinar</h1>
        <p><strong>Nome do Motorista:</strong> <?php echo htmlspecialchars($nome_motorista); ?></p>
        <p><strong>Matrícula:</strong> <?php echo htmlspecialchars($matricula); ?></p>
        <p><strong>Ação Disciplinar:</strong> <?php echo htmlspecialchars($acao_disciplinar); ?></p>
        <p><strong>Data da Ação:</strong> <?php echo htmlspecialchars($data_acao); ?></p>
        <h3>Histórico de Ações:</h3>
        <ul>
            <?php foreach ($acoes_disciplinas as $acao): ?>
                <li><?php echo htmlspecialchars($acao['acao']); ?> (<?php echo htmlspecialchars($acao['data']); ?>)</li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>