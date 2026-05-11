<?php
session_start();
require_once '../Model/conexao.php';
require_once '../Model/EnfermagemService.php';

// Segurança: Apenas administradores/gerentes
if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'admin') {
    header("Location: ../view/login.php?erro=acesso_negado");
    exit;
}

$enfermagemService = new EnfermagemService($conexao);
$pacientes = $enfermagemService->listarResumoMedicacaoGeral();

// Se houver necessidade de processar alguma ação específica do gerente antes de carregar a página, faríamos aqui.