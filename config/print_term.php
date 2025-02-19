<?php
// Definir o fuso horário para o Brasil
date_default_timezone_set('America/Sao_Paulo');

// Importa as configurações do banco de dados
require_once 'database.php';

// Iniciar sessão para acessar o usuário logado
session_start();

// Criar conexão
$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar conexão
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Verificar se os parâmetros necessários foram passados
if (isset($_GET['motorista']) && isset($_GET['valor']) && isset($_GET['datas'])) {
    $motorista = mysqli_real_escape_string($conexao, $_GET['motorista']);
    $valor_a_pagar = mysqli_real_escape_string($conexao, $_GET['valor']);
    $datas_ocorrencias = mysqli_real_escape_string($conexao, $_GET['datas']);
    
    // Buscar nome, matrícula e ID do motorista
    $query_motorista = "SELECT id, nome, matricula, cpf FROM cadastro_motorista WHERE matricula = ?";
    $stmt_motorista = $conexao->prepare($query_motorista);
    $stmt_motorista->bind_param('s', $motorista);
    $stmt_motorista->execute();
    $resultado_motorista = $stmt_motorista->get_result();
    $nome_motorista = '';
    $matricula_motorista = '';
    $cpf_motorista = '';
    $motorista_id = '';
    if ($resultado_motorista->num_rows > 0) {
        $row_motorista = $resultado_motorista->fetch_assoc();
        $nome_motorista = $row_motorista['nome'];
        $matricula_motorista = $row_motorista['matricula'];
        $cpf_motorista = $row_motorista['cpf'];
        $motorista_id = $row_motorista['id']; // Recupera o ID do motorista
    }
    
    // Buscar ocorrências com todas as colunas
    $query_ocorrencias = "SELECT id, data, motorista, descricao, horario, fiscal, carro, linha, ocorrencia, acao, observacoes, video1, video2, video3 FROM ocorrencia_trafego WHERE motorista = ? AND ocorrencia = 'Evasão'";
    $stmt_ocorrencias = $conexao->prepare($query_ocorrencias);
    $stmt_ocorrencias->bind_param('s', $motorista);
    $stmt_ocorrencias->execute();
    $resultado_ocorrencias = $stmt_ocorrencias->get_result();
    $ocorrencias = [];
    if ($resultado_ocorrencias->num_rows > 0) {
        while ($row = $resultado_ocorrencias->fetch_assoc()) {
            $ocorrencias[] = $row;
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

    $data_emissao = date('m/Y');
    $data_completa = date('d/m/Y');
    $data_atual_extenso = formatarDataExtenso(date('Y-m-d'));

    // Determinar a ação disciplinar com base nas últimas ações
    $acao = 'Orientação'; // Valor padrão
    $data_acao = date('Y-m-d'); // Data atual

    if (!empty($ocorrencias)) {
        // Verificar as últimas ações disciplinares do motorista
        $query_ultimas_acoes = "SELECT acao, data_acao FROM acoes_disciplinares WHERE motorista_id = ? ORDER BY data_acao DESC LIMIT 3";
        $stmt_ultimas_acoes = $conexao->prepare($query_ultimas_acoes);
        $stmt_ultimas_acoes->bind_param('i', $motorista_id);
        $stmt_ultimas_acoes->execute();
        $resultado_ultimas_acoes = $stmt_ultimas_acoes->get_result();
        $ultimas_acoes = [];
        if ($resultado_ultimas_acoes->num_rows > 0) {
            while ($row = $resultado_ultimas_acoes->fetch_assoc()) {
                $ultimas_acoes[] = $row;
            }
        }

        if (!empty($ultimas_acoes)) {
            $ultima_acao = $ultimas_acoes[0]['acao'];
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
                    $data_ultima_acao = new DateTime($ultimas_acoes[0]['data_acao']);
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
        }
    }

    // Inserir a ação disciplinar na tabela `acoes_disciplinares`
    $query_inserir_acao = "INSERT INTO acoes_disciplinares (motorista_id, acao, data_acao) VALUES (?, ?, ?)";
    $stmt_inserir_acao = $conexao->prepare($query_inserir_acao);
    $stmt_inserir_acao->bind_param('iss', $motorista_id, $acao, $data_acao);
    if (!$stmt_inserir_acao->execute()) {
        die("Erro ao salvar a ação disciplinar: " . $stmt_inserir_acao->error);
    }

    // Dados adicionais para a tabela finalizada
    $acao_finalizada = $acao; // Ação disciplinar determinada
    $data_finalizacao = date('Y-m-d'); 
    $hora_finalizacao = date('H:i:s'); 
    $usuario_finalizou = $_SESSION['usuario']; 

    // Mover ocorrências para a tabela de finalizados
    $conexao->autocommit(FALSE);
    foreach ($ocorrencias as $ocorrencia) {
        $id = $ocorrencia['id'];

        // Verificar se o ID já existe na tabela de finalizados
        $query_check = "SELECT id FROM ocorrencia_finalizada WHERE id = ?";
        $stmt_check = $conexao->prepare($query_check);
        $stmt_check->bind_param('i', $id);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            continue; // Pula se já existir
        }

        // Inserir na tabela de finalizadas (usando a coluna "acao" em vez de "acao_finalizada")
        $query_insert = "INSERT INTO ocorrencia_finalizada (
            data, motorista, descricao, horario, fiscal, carro, linha, ocorrencia, 
            acao, observacoes, video1, video2, video3, data_finalizacao, 
            hora_finalizacao, usuario_finalizou
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // 16 parâmetros

        $stmt_insert = $conexao->prepare($query_insert);
        $stmt_insert->bind_param(
            'ssssssssssssssss',
            $ocorrencia['data'], 
            $ocorrencia['motorista'], 
            $ocorrencia['descricao'],
            $ocorrencia['horario'], 
            $ocorrencia['fiscal'], 
            $ocorrencia['carro'], 
            $ocorrencia['linha'],
            $ocorrencia['ocorrencia'], 
            $acao_finalizada, // Ação disciplinar atual (agora na coluna "acao")
            $ocorrencia['observacoes'],
            $ocorrencia['video1'], 
            $ocorrencia['video2'], 
            $ocorrencia['video3'],
            $data_finalizacao, 
            $hora_finalizacao, 
            $usuario_finalizou
        );
        $stmt_insert->execute();
        
        // Remover da tabela original
        $query_delete = "DELETE FROM ocorrencia_trafego WHERE id = ?";
        $stmt_delete = $conexao->prepare($query_delete);
        $stmt_delete->bind_param('i', $id);
        $stmt_delete->execute();
    }
    $conexao->commit();
    $conexao->autocommit(TRUE);
} else {
    echo "Parâmetros não especificados.";
    exit;
}

mysqli_close($conexao);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emissão de Vales</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/styles.css">
    <style>

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
    <div class="nav">
        <table>
            <tr>
                <td>
                    <div class="logo">
                        <img src="../assets/img/logo/logo.png" alt="logo-sm">
                    </div>
                </td>
                <td>
                    <div class="dados_cabecalho">
                        <h2 style="text-transform: uppercase;">Autorização Para Desconto Em Folha</h2>
                    </div>
                </td>
            </tr>
        </table>
        
    </div>
    <div class="linha-separadora"></div>
    
    <div>
        <h4>Matrícula: <strong><?php echo htmlspecialchars($matricula_motorista); ?> - <strong><?php echo htmlspecialchars($nome_motorista); ?></strong></strong></h4>
        <!--<h4>Data: <strong><?php //echo $data_completa; ?></strong></h4>-->
    </div>
    <p class="justificar">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pelo presente instrumento particular, que entre si fazem, de um lado como empregadora a firma 
        <strong>TRANSPORTE URBANO SÃO MIGUEL DE ILHÉUS LTDA</strong>, e de outro lado o empregado Sr. 
        <strong><?php echo htmlspecialchars($nome_motorista); ?>, CPF: <?php echo $cpf_motorista; ?></strong>, ficou justo e contratado os seguintes.
    </p>
    <p class="justificar">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O empregado no exercício de suas funções de MOTORISTA, autoriza a efetuar o desconto de <strong>R$ <?php echo number_format($valor_a_pagar, 2, ',', '.'); ?></strong> 
        em seu salário, através da Folha de Pagamento de <strong><?php echo $data_emissao; ?></strong>, referente à(s) ocorrência(s) devidamente demonstrada(s) no relatório em anexo e comprovada(s) por
        gravação interna do veículo, as quais teve acesso.
    </p>
    <p class="justificar">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Declara o empregado que concorda com o relatado e com as orientações que lhe foram
        passadas no caso de reincidência.
    </p>
    <p class="justificar">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Declara ainda, estar ciente que em caso de demissão, terá descontados os referidos
        valores em sua rescisão contratual de trabalho.
    </p><br>
    
    <table border="1">
        <tr>
            <th>OS</th>
            <th>Data</th>
            <th>horario</th>
            <th>Motorista</th>
            <th>Carro</th>
            <th>Valor</th>
            <th>Ocorrência</th>
        </tr>
        <?php foreach ($ocorrencias as $ocorrencia) { ?>
        <tr>
            <td><?php echo htmlspecialchars($ocorrencia['id']); ?></td>
            <td><?php echo htmlspecialchars($ocorrencia['data']); ?></td>
            <td><?php echo htmlspecialchars($ocorrencia['horario']); ?></td>
            <td><?php echo htmlspecialchars($ocorrencia['motorista']); ?></td>
            <td><?php echo htmlspecialchars($ocorrencia['carro']); ?></td>
            <td>R$ 4,80</td>
            <td class="dados_table"><?php echo htmlspecialchars($ocorrencia['descricao']); ?></td>
        </tr>
        <?php } ?>
    </table>
    <div>
        <p class="justificar">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; E por estarem as partes certas, justas e contratadas, firmam o presente.</p>
        <p class="dataatual">Ilhéus, <?php echo $data_atual_extenso; ?></p>
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

<!-- Pop-up de sucesso -->
<div class="popup" id="successPopup">
    <div class="popup-content">
        <h2>Ocorrência Finalizada com Sucesso!</h2>
        <button onclick="closePopup()">OK</button>
    </div>
</div>

<script>
    function showPopup() {
        document.getElementById('successPopup').style.display = 'flex';
    }

    function closePopup() {
        // Fecha o pop-up
        document.getElementById('successPopup').style.display = 'none';

        // Redireciona para a página "ocorrenciasEvasao.php"
        window.location.href = '../views/ocorrenciasEvasao.php';
    }
</script>
</script>

</body>
</html>