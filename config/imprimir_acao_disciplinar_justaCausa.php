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

// Função para formatar a data
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

    // $data_emissao = date('m/Y');
    $data_acao = date('d/m/Y');
    $data_atual_extenso = formatarDataExtenso(date('Y-m-d'));

// Fechar a conexão com o banco de dados
mysqli_close($conexao);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Ação Disciplinar</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            padding: 20px;
        }

        img {
            max-width: 120px;
        }

        #title {
            width: 100%;
            padding-top: 10px;
            text-align: center;
        }

        #nav {
            display: flex;
            justify-content: left;
        }

        h1 {
            color: #dc3545; /* Cor vermelho para destacar o título */
        }
        .container {
            width: 600px;
            height: 900px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;

        }
        p {
            font-size: .9rem;
            margin: 10px 0;
        }
        .justificar {
            text-align: justify;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            font-size: .9rem;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }

        .linha-separadora {
            border: solid 1px #ccc;
        }

        #dataatual {
            text-align: end;
            margin-top: 20px;
        }

        .assinatura {
            display: flex;
            gap: 10%;
            padding-top: 10px;
        }

        .assinar {
            margin-bottom: 10px;
            font-size: .8rem;
        }

        /* Estilo para o pop-up de sucesso */
        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .popup-content button {
            margin-top: 10px;
        }
    </style>
</head>  
<body onload="window.print(); setTimeout(showPopup, 1000);">

<div class="container">
    <div id="nav">
        <div id="logo">
            <img src="../assets/img/logo/logo.png" alt="logo-sm">
        </div>
        <div id="title">
            <h3>Ficha de <?php echo htmlspecialchars($acao_disciplinar); ?></h3>
        </div>
    </div>
    <div class="linha-separadora"></div>
    
    <div>
        <h4>Matrícula: <strong><?php echo htmlspecialchars($matricula); ?> - <strong><?php echo htmlspecialchars($nome_motorista); ?></strong></strong></h4>
        <h4>Data: <strong><?php echo htmlspecialchars($data_acao); ?></strong></h4>
        <h3>Histórico de Ações:</h3>
        <ul>
            <?php foreach ($acoes_disciplinas as $acao): ?>
                <li><?php echo htmlspecialchars($acao['acao']); ?> (<?php echo htmlspecialchars($acao['data']); ?>)</li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div>
        <p class="justificar">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pelo presente instrumento particular, que entre si fazem, de um lado como empregadora a firma 
        <strong>TRANSPORTE URBANO SÃO MIGUEL DE ILHÉUS LTDA</strong>, e de outro lado o empregado Sr. 
        <strong><?php echo htmlspecialchars($nome_motorista); ?></strong>, ficou justo e contratado os seguintes.
    </p>

    </div>
    
    <div>
        <p class="justificar">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; E por estarem as partes certas, justas e contratadas, firmam o presente.</p>
        <p id="dataatual">Ilhéus, <?php echo $data_atual_extenso; ?></p>
    </div>
    
    <div class="assinatura">
        <div>
            <P> __________________________________</P>
            <p class="assinar"><?php echo htmlspecialchars($nome_motorista); ?></p>
        </div>
        <div>
            <P>__________________________________</P>
            <p class="assinar">Transporte Urbano São Miguel de Ilhéus LTDA</p>
        </div>
    </div>
    <div class="assinatura">
        <div>
            <P>__________________________________</P>
            <p class="assinar">Testemunha 1:</p>
            <p>CPF:______________________________</p>
        </div>
        <div>
            <P>__________________________________</P>
            <p class="assinar">Testemunha 2:</p>
            <p>CPF:______________________________</p>
        </div>
    </div>
</div>

</body>
</html>