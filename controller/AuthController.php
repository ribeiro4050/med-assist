<?php
session_start();
require_once '../Model/conexao.php';
require_once '../Model/AuthService.php';
require_once 'RecuperacaoEmailController.php';

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
    
    if ($auth->solicitarRecuperacao($email)) {
        header('Location: ../view/verificar-codigo.php');
    } else {
        header('Location: ../view/login.php');
    }
    exit;
}

// --- 4. VALIDAÇÃO DO CÓDIGO ---
if (isset($_POST['validar_codigo'])) {
    $email = $_SESSION['email_recuperacao'];
    $codigo_digitado = filtrar_sql($_POST['codigo_verificacao']);

    if ($auth->validarCodigoRecuperacao($email, $codigo_digitado)) {
        header('Location: ../view/nova-senha.php');
    } else {
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

    if ($auth->atualizarSenhaEsquecida($email, $nova_senha)) {
        header('Location: ../view/login.php');
    } else {
        header('Location: ../view/nova-senha.php');
    }
    exit;
}