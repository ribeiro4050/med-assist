<?php
// controller/historico-paciente-controller.php

require_once __DIR__ . '/../Model/conexao.php'; 
require_once __DIR__ . '/../Model/historico-paciente-model.php';

session_start();

// ----------------------------------------------------------------------
// RF011: LÓGICA DE SEGURANÇA (RESTAURADA)
// Usuário deve estar logado E ser Médico ou Assistente
// ----------------------------------------------------------------------
if (!isset($_SESSION['usuario_logado']) || ($_SESSION['perfil'] != 'Medico' && $_SESSION['perfil'] != 'Assistente')) {
    header('Location: ../view/login.php?erro=acesso_negado'); // Redireciona para o login
    exit();
}
// ----------------------------------------------------------------------


// ----------------------------------------------------------------------
// GARANTIA DE VARIÁVEIS (Correção de Warnings)
// ----------------------------------------------------------------------
$nome_paciente = "Paciente Desconhecido"; 
$historico = []; 


// ----------------------------------------------------------------------
// AÇÃO 1: Obter e Validar o ID do Paciente
// ----------------------------------------------------------------------

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Ação temporária para evitar o 404 de lista-pacientes.php (MANTIDO O DIE)
    die("ERRO: ID do paciente ausente ou inválido. Por favor, use o formato: ?id=X");
}

$paciente_id = (int)$_GET['id'];

// ----------------------------------------------------------------------
// AÇÃO 2: Buscar o Nome Real do Paciente
// ----------------------------------------------------------------------

$nome_paciente = buscarNomePaciente($conexao, $paciente_id);

// Se o nome não for encontrado
if ($nome_paciente == "Paciente Não Encontrado") {
    die("ERRO: Paciente com ID $paciente_id não encontrado.");
}

// ----------------------------------------------------------------------
// AÇÃO 3: Chamar a função do Model para buscar o histórico completo
// ----------------------------------------------------------------------

$historico = buscarHistoricoCompleto($conexao, $paciente_id);

// ----------------------------------------------------------------------
// AÇÃO 4: Carregar a View
// ----------------------------------------------------------------------

require_once '../view/historico-paciente-view.php';

mysqli_close($conexao);
?>