<?php
session_start();
require_once '../Model/conexao.php';

// Segurança: Apenas administradores/gerentes
if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'admin') {
    header("Location: ../view/login.php?erro=acesso_negado");
    exit;
}

// O Controller aguarda ações/comandos específicos vindos por POST (Ex: alterar status, limpar logs, etc)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógicas de ação de auditoria entrarão aqui futuramente
}