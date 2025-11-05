<?php

// Inclui os arquivos necessários
// "__DIR__" é uma constante importante que retorna o diretório do arquivo atual, é mais confiavel que o require once sem o DIR
require_once __DIR__ . '/../Model/conexao.php'; 
require_once __DIR__ . '/../Model/historico-paciente-model.php';

session_start();


// RF011: LÓGICA DE SEGURANÇA (COMENTADA PARA TESTES)
// Usuário deve estar logado E ser Médico ou Assistente
// REMOVA OS COMENTÁRIOS DESTE BLOCO PARA ATIVAR A SEGURANÇA NOVAMENTE!

// ----------------------------------------------------------------------

/*
if (!isset($_SESSION['usuario_logado']) || ($_SESSION['perfil'] != 'Medico' && $_SESSION['perfil'] != 'Assistente')) {
    header('Location: ../view/login.php?erro=acesso_negado'); 
    exit();
}
*/

// Inicializa variáveis para evitar o erro 'Undefined variable' na View
$nome_paciente = "Paciente Desconhecido"; 
$historico = []; 


// === OBTER E VALIDAR O ID DO PACIENTE ===

// isset -> verifica se a variavelexiste e não é nula
// ! -> operador de negação, inverte o resultado da condição se é true vira false e vice versa
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ERRO: ID do paciente ausente ou inválido. Por favor, use o formato: ?id=X");
}

$paciente_id = (int)$_GET['id'];

$nome_paciente = buscarNomePaciente($conexao, $paciente_id);


// Se o nome não for encontrado pode ser um ID que não é de paciente
if ($nome_paciente == "Paciente Não Encontrado") {
    die("ERRO: Paciente com ID $paciente_id não encontrado ou não tem o perfil de paciente.");
}

// === BUSCAR O HISTÓRICO COMPLETO DO PACIENTE ===
$historico = buscarHistoricoCompleto($conexao, $paciente_id);


//   Carregar a View. a View agora tem acesso a $nome_paciente e $historico
require_once '../view/historico-paciente-view.php';

mysqli_close($conexao);
?>