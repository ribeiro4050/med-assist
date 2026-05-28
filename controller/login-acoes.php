<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../Model/conexao.php';
require_once '../Model/AuthService.php';

$authService = new AuthService($conexao);

// --- Ação de Login para Usuário/Paciente ---
if (isset($_POST['login_user'])) {
    $email = $_POST['email_user'];
    $senha = $_POST['senha_user'];

    $resultado = $authService->autenticar($email, null, $senha);

    if ($resultado['sucesso']) {
        $usuario = $resultado['dados'];
        $_SESSION['logado'] = true;
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_role'] = $usuario['role'];

        $_SESSION['mensagem'] = "Login realizado com sucesso! Bem-vindo(a), " . $usuario['nome'];
        header('Location: ../view/index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = $resultado['erro'];
        header('Location: ../view/login.php');
        exit;
    }
}

// --- Ação de Login para Profissional/Admin ---
if (isset($_POST['login_admin'])) {
    $email = $_POST['email_admin'];
    $registro = $_POST['registro_profissional_admin'];
    $senha = $_POST['senha_admin'];

    // Se o e-mail não foi preenchido na aba de profissionais, autentica estritamente pelo Registro
    $resultado = $authService->autenticar($email, $registro, $senha);

    if ($resultado['sucesso']) {
        $usuario = $resultado['dados'];
        $_SESSION['logado'] = true;
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_role'] = $usuario['role'];

        $_SESSION['mensagem'] = "Login realizado com sucesso! Bem-vindo(a), " . $usuario['nome'];
        header('Location: ../view/index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = $resultado['erro'];
        header('Location: ../view/login.php');
        exit;
    }
}

// Se a página for acessada de forma incorreta
$_SESSION['mensagem'] = "Acesso inválido.";
header('Location: ../view/login.php');
exit;
?>