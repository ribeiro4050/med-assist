<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../Model/conexao.php'; 
require_once __DIR__ . '/../Model/historico-paciente-model.php';

// Valida ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ERRO: ID do paciente ausente ou inválido. Por favor, use o formato: ?id=X");
}

$paciente_id = (int)$_GET['id'];

// Instancia o Service e busca os dados
$historicoService = new HistoricoPacienteService($conexao);

$nome_paciente = $historicoService->obterNomePaciente($paciente_id);
$historico = $historicoService->obterHistoricoCompleto($paciente_id);

// Verifica se encontrou o paciente
if ($nome_paciente == "Paciente Não Encontrado") {
    die("ERRO: Paciente com ID $paciente_id não encontrado.");
}

// Envia as variáveis prontas para a View
require_once '../view/historico-paciente-view.php';

mysqli_close($conexao);
?>