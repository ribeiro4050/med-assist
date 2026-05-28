<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../Model/conexao.php';
require_once __DIR__ . '/../Model/AuthService.php';

// Trata o formulário de solicitação de código enviado pela view esqueci-senha.php
if (isset($_POST['solicitar_codigo'])) {
    $email = $_POST['email'];

    $authService = new AuthService($conexao);
    
    if ($authService->solicitarRecuperacao($email)) {
        // Código gerado e enviado com sucesso, vai para a view de validação
        header('Location: ../view/verificar-codigo.php');
    } else {
        // Erro (e-mail não cadastrado ou falha no envio)
        header('Location: ../view/esqueci-senha.php');
    }
    exit;
}