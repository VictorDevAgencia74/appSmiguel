<?php
// Importa as configurações do banco de dados
require_once 'database.php';

// Criar conexão
$conexao = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

// Verificar conexão
if (!$conexao) {
    die("Falha na conexão: " . mysqli_connect_error());
}

if (isset($_GET['motorista']) && isset($_GET['valor']) && isset($_GET['datas'])) {
    $motorista = mysqli_real_escape_string($conexao, $_GET['motorista']);
    $valor_a_pagar = mysqli_real_escape_string($conexao, $_GET['valor']);
    $datas_ocorrencias = mysqli_real_escape_string($conexao, $_GET['datas']);
    
    // Buscar nome e matrícula do motorista
    $query_motorista = "SELECT nome, matricula FROM cadastro_motorista WHERE matricula = '$motorista'";
    $resultado_motorista = mysqli_query($conexao, $query_motorista);
    $nome_motorista = '';
    $matricula_motorista = '';
    if (mysqli_num_rows($resultado_motorista) > 0) {
        $row_motorista = mysqli_fetch_assoc($resultado_motorista);
        $nome_motorista = $row_motorista['nome'];
        $matricula_motorista = $row_motorista['matricula'];
    }
    
    // Buscar ocorrências com todas as colunas
    $query_ocorrencias = "SELECT id, data, motorista, descricao, horario, fiscal, carro, linha, ocorrencia, acao, observacoes, video1, video2, video3 FROM ocorrencia_trafego WHERE motorista = '$motorista' AND ocorrencia = 'Evasão'";
    $resultado_ocorrencias = mysqli_query($conexao, $query_ocorrencias);
    $ocorrencias = [];
    if (mysqli_num_rows($resultado_ocorrencias) > 0) {
        while($row = mysqli_fetch_assoc($resultado_ocorrencias)) {
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

    // Mover ocorrências para a tabela de finalizados
    $conexao->autocommit(FALSE);
    foreach ($ocorrencias as $ocorrencia) {
        $id = $ocorrencia['id'];

        // Inserir na tabela de finalizadas com todas as colunas
        $query_insert = "INSERT INTO ocorrencia_finalizada (id, data, motorista, descricao, horario, fiscal, carro, linha, ocorrencia, acao, observacoes, video1, video2, video3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($query_insert);
        $stmt->bind_param(
            'ssssssssssssss',
            $ocorrencia['id'], $ocorrencia['data'], $ocorrencia['motorista'], $ocorrencia['descricao'],
            $ocorrencia['horario'], $ocorrencia['fiscal'], $ocorrencia['carro'], $ocorrencia['linha'],
            $ocorrencia['ocorrencia'], $ocorrencia['acao'], $ocorrencia['observacoes'],
            $ocorrencia['video1'], $ocorrencia['video2'], $ocorrencia['video3']
        );
        $stmt->execute();
        
        // Remover da tabela original
        $query_delete = "DELETE FROM ocorrencia_trafego WHERE id = ?";
        $stmt = $conexao->prepare($query_delete);
        $stmt->bind_param('s', $id);
        $stmt->execute();
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
                        <h2>Autorização Para Desconto Em Folha</h2>
                    </div>
                </td>
            </tr>
        </table>
        
    </div>
    <div class="linha-separadora"></div>
    
    <div>
        <h4>Matrícula: <strong><?php echo htmlspecialchars($matricula_motorista); ?> - <strong><?php echo htmlspecialchars($nome_motorista); ?></strong></strong></h4>
        <h4>Data: <strong><?php echo $data_completa; ?></strong></h4>
    </div>
    <p class="justificar">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pelo presente instrumento particular, que entre si fazem, de um lado como empregadora a firma 
        <strong>TRANSPORTE URBANO SÃO MIGUEL DE ILHÉUS LTDA</strong>, e de outro lado o empregado Sr. 
        <strong><?php echo htmlspecialchars($nome_motorista); ?></strong>, ficou justo e contratado os seguintes.
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
            <th>Motorista</th>
            <th>Descrição</th>
        </tr>
        <?php foreach ($ocorrencias as $ocorrencia) { ?>
        <tr>
            <td><?php echo htmlspecialchars($ocorrencia['id']); ?></td>
            <td><?php echo htmlspecialchars($ocorrencia['data']); ?></td>
            <td><?php echo htmlspecialchars($ocorrencia['motorista']); ?></td>
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
        document.getElementById('successPopup').style.display = 'none';
        window.opener.location.href = '../views/ocorrenciasEvasao.php'; // Atualiza a página de detalhes do motorista
        window.close(); // Fecha a aba atual (imprimir_termo.php)
    }
</script>

</body>
</html>