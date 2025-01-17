<?php
// Importa as configurações do banco de dados
require_once 'database.php';

// Obtém o número de linhas por página a partir do POST ou define um valor padrão
$rowsPerPage = isset($_POST['rows']) ? intval($_POST['rows']) : 10;

// Verifica se a conexão está definida e não é null
if ($conexao) {
    // Modifica a consulta SQL para excluir ocorrências de Evasão
    $consulta_sql = "SELECT * FROM ocorrencia_trafego WHERE ocorrencia != 'Evasão' ORDER BY id DESC LIMIT $rowsPerPage";
    $resultado_consulta = mysqli_query($conexao, $consulta_sql);

    if ($resultado_consulta) {
        echo "<table class='table table-striped table-hover table-sm'>
            <thead class='table-light'>
                <tr>
                    <th class='text-center align-middle'>Os</th>
                    <th class='text-center align-middle'>Data</th>
                    <th class='text-center align-middle mobile-report'>Horário</th>
                    <th class='text-center align-middle'>Motorista</th>
                    <th class='text-center align-middle'>Carro</th>
                    <th class='text-center align-middle mobile-report'>Linha</th>
                    <th class='text-start align-middle'>Ocorrência</th>
                    <th class='text-center align-middle'>Finalizar OS</th>
                </tr>
            </thead>
            <tbody class='table-group-divider'>";
        
        // Itera sobre os resultados da consulta
        while ($linha = mysqli_fetch_assoc($resultado_consulta)) {
            echo "<tr>
                <td class='text-center text-danger align-middle'>{$linha['id']}</td>
                <td class='text-center align-middle'>{$linha['data']}</td>
                <td class='text-center align-middle mobile-report'>{$linha['horario']}</td>
                <td class='text-center align-middle'>{$linha['motorista']}</td>
                <td class='text-center align-middle'>{$linha['carro']}</td>
                <td class='text-center align-middle mobile-report'>{$linha['linha']}</td>
                <td class='text-start align-middle'>{$linha['ocorrencia']}</td>
                <td class='text-center align-middle update-action-column'>
                    <a href='../views/view.php?id={$linha['id']}' class='btn btn-outline-danger btn-sm'>Abrir OS</a>
                </td>
            </tr>";
        }
        
        echo "</tbody>
            </table>";
        
        // Libera o resultado da consulta
        mysqli_free_result($resultado_consulta);
    } else {
        // Se houver erro na consulta, exibe a mensagem de erro
        echo "ERRO NA CONSULTA: " . mysqli_error($conexao);
    }
} else {
    echo "ERRO: Conexão com o banco de dados não está definida ou é nula.";
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao);
?>