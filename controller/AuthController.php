<?php
session_start();
require_once '../Model/conexao.php';
require_once '../Model/AuthService.php';
require_once 'RecuperacaoEmailController.php'; // Certifique-se que o caminho está correto

$auth = new AuthService($conexao);

// --- 1. LÓGICA DE LOGIN ---
if (isset($_POST['login_usuario'])) {
    $email = filtrar_sql($_POST['email'] ?? '');
    $registro = filtrar_sql($_POST['registro'] ?? '');
    $senha = $_POST['senha'];

    $resultado = $auth->autenticar($email, $registro, $senha);

    if ($resultado['sucesso']) {
        $user = $resultado['dados'];
        
        $_SESSION['logado'] = true;
        $_SESSION['id_usuario'] = $user['id'];
        $_SESSION['nome_usuario'] = $user['nome'];
        $_SESSION['role_usuario'] = $user['role'];
        $_SESSION['mensagem'] = "Bem-vindo(a), " . $user['nome'] . "!";

        // Redirecionamento baseado na Role
        $urls = [
            'paciente'   => 'home.php',
            'enfermeiro' => 'home-enfermeiro.php',
            'medico'     => 'painel-medico.php',
            'admin'      => 'lista-de-usuarios.php'
        ];
        
        $url = $urls[$user['role']] ?? 'home.php';
        header("Location: ../view/$url");
    } else {
        $_SESSION['mensagem'] = $resultado['erro'];
        header('Location: ../view/login.php');
    }
    exit;
}

// --- 2. LÓGICA DE LOGOUT ---
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    session_start(); 
    $_SESSION['mensagem'] = "Sessão encerrada com sucesso.";
    header('Location: ../view/index.php');
    exit;
}

// --- 3. RECUPERAÇÃO DE SENHA (SOLICITAÇÃO) ---
if (isset($_POST['esqueci_senha'])) {
    $email = filtrar_sql($_POST['email']);
    $query = mysqli_query($conexao, "SELECT id FROM usuarios WHERE email = '$email'");
    
    if (mysqli_num_rows($query) > 0) {
        $codigo = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $expiracao = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        mysqli_query($conexao, "UPDATE recuperacao_senha SET usado = 1 WHERE email = '$email'");
        $sql = "INSERT INTO recuperacao_senha (email, codigo, data_expiracao) VALUES ('$email', '$codigo', '$expiracao')";
        
        if (mysqli_query($conexao, $sql)) {
            if (RecuperacaoEmailController::enviarCodigo($email, $codigo)) {
                $_SESSION['mensagem'] = "Código enviado com sucesso!";
            } else {
                $_SESSION['mensagem'] = "Erro ao enviar e-mail. Verifique o banco (Teste).";
            }
            $_SESSION['email_recuperacao'] = $email;
            header('Location: ../view/verificar-codigo.php');
        }
    } else {
        $_SESSION['mensagem'] = "E-mail não encontrado.";
        header('Location: ../view/login.php');
    }
    exit;
}

// --- 4. VALIDAÇÃO DO CÓDIGO ---
if (isset($_POST['validar_codigo'])) {
    $email = $_SESSION['email_recuperacao'];
    $codigo_digitado = filtrar_sql($_POST['codigo_verificacao']);
    $agora = date('Y-m-d H:i:s');

    $sql = "SELECT * FROM recuperacao_senha 
            WHERE email = '$email' AND codigo = '$codigo_digitado' 
            AND usado = 0 AND data_expiracao > '$agora' LIMIT 1";

    if (mysqli_num_rows(mysqli_query($conexao, $sql)) > 0) {
        $_SESSION['pode_mudar_senha'] = true;
        header('Location: ../view/nova-senha.php');
    } else {
        $_SESSION['mensagem'] = "Código inválido ou expirado.";
        header('Location: ../view/verificar-codigo.php');
    }
    exit;
}

// --- 5. DEFINIÇÃO DE NOVA SENHA ---
if (isset($_POST['atualizar_senha_esquecida'])) {
    $email = $_SESSION['email_recuperacao'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar = $_POST['confirmar_senha'];

    if ($nova_senha !== $confirmar) {
        $_SESSION['mensagem'] = "As senhas não conferem!";
        header('Location: ../view/nova-senha.php');
        exit;
    }

    $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    if (mysqli_query($conexao, "UPDATE usuarios SET senha = '$hash' WHERE email = '$email'")) {
        mysqli_query($conexao, "UPDATE recuperacao_senha SET usado = 1 WHERE email = '$email'");
        unset($_SESSION['email_recuperacao'], $_SESSION['pode_mudar_senha']);
        $_SESSION['mensagem'] = "Senha atualizada! Faça login.";
        header('Location: ../view/login.php');
    }
    exit;
}