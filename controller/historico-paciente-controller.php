<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Inclui os arquivos necessários utilizando caminhos robustos baseados na raiz do arquivo
require_once __DIR__ . '/../Model/conexao.php'; 
require_once __DIR__ . '/../Model/historico-paciente-model.php';

// RF011: LÓGICA DE SEGURANÇA (COMENTADA PARA TESTES)
/*
if (!isset($_SESSION['usuario_logado']) || ($_SESSION['perfil'] != 'Medico' && $_SESSION['perfil'] != 'Assistente')) {
    header('Location: ../view/login.php?erro=acesso_negado'); 
    exit();
}
*/

// === OBTER E VALIDAR O ID DO PACIENTE ===
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ERRO: ID do paciente ausente ou inválido. Por favor, use o formato: ?id=X");
}

$paciente_id = (int)$_GET['id'];

// Instanciação da classe de Serviço seguindo o padrão unificado do projeto
$historicoService = new HistoricoPacienteService($conexao);

// Executa a busca do Nome na Camada Model
$nome_paciente = $historicoService->obterNomePaciente($paciente_id);

// Validação de Integridade do ID
if ($nome_paciente == "Paciente Não Encontrado") {
    die("ERRO: Paciente com ID $paciente_id não encontrado ou não tem o perfil de paciente.");
}

// Coleta o histórico unificado tratado pelo Model
$historico = $historicoService->obterHistoricoCompleto($paciente_id);

// Carrega a camada View injetando de forma limpa as variáveis preenchidas
require_once '../view/historico-paciente-view.php';

mysqli_close($conexao);
?>