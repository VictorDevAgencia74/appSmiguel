<?php
session_start();

// Verifica se o usuário não está logado
if (!isset($_SESSION['user'])) {

    // Inicializa as variáveis
    $email = $password = $error = "";

    // Verifica se o formulário de login foi enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Importa as configurações do banco de dados
        require_once 'database.php';

        // Conecta ao banco de dados
        $conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

        // Verifica se a conexão foi bem-sucedida
        if ($conn->connect_error) {
            die("Erro na conexão com o banco de dados: " . $conn->connect_error);
        }

        // Consulta para verificar as credenciais
        $query = "SELECT id, senha, status FROM u219851065_smiguel.usuarios WHERE email = ?";
        $stmt = $conn->prepare($query);

        // Verifica se a preparação da consulta foi bem-sucedida
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Verifica se a execução da consulta foi bem-sucedida
        $result = $stmt->get_result();

        // Verifica se a consulta retornou alguma linha
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verifica se o usuário está ativo
            if (in_array($row['status'], [1, 2, 3, 4, 10, 11])) {
                // Verifica se a senha é válida
                if (password_verify($password, $row['senha'])) {
                    // Autenticado com sucesso
                    $_SESSION['user'] = $email;
                    $_SESSION['status'] = $row['status'];
                    $_SESSION['user_id'] = $row['id'];
                    header("Location: ../views/ocorrenciasVideos.php");
                    exit();
                } else {
                    // Senha inválida
                    $error = "Senha inválida";
                }
            } else {
                // Usuário não está ativo
                $error = "Usuário não está ativo";
            }
        } else {
            // E-mail não encontrado
            $error = "E-mail não encontrado";
        }

        // Fecha a conexão com o banco de dados
        $stmt->close();
        $conn->close(); // Adicionei o fechamento da conexão aqui
    }

    // Redireciona para a tela de login se houver um erro
    if ($error) {
        // Pode usar session para armazenar a mensagem de erro e exibi-la na página de login, se necessário
        $_SESSION['error'] = $error;
        header("Location: ../index.html");
        exit();
    }

} else {
    // Se o usuário já estiver logado, redireciona para o painel
    header("Location: ../views/relatorios.php");
    exit();
}
?>
