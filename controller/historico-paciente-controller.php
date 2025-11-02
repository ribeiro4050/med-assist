<?php

// Inclui os arquivos necessários
require_once __DIR__ . '/../Model/conexao.php'; 
require_once __DIR__ . '/../Model/historico-paciente-model.php';

session_start();

// ----------------------------------------------------------------------
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
// ----------------------------------------------------------------------


// ----------------------------------------------------------------------
// GARANTIA DE VARIÁVEIS
// Inicializa variáveis para evitar o erro 'Undefined variable' na View.
// ----------------------------------------------------------------------
$nome_paciente = "Paciente Desconhecido"; 
$historico = []; 


// ----------------------------------------------------------------------
// AÇÃO 1: Obter e Validar o ID do Paciente
// ----------------------------------------------------------------------

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Se não houver ID válido na URL, encerra a execução e exibe uma mensagem
    die("ERRO: ID do paciente ausente ou inválido. Por favor, use o formato: ?id=X");
}

$paciente_id = (int)$_GET['id'];

// ----------------------------------------------------------------------
// AÇÃO 2: Buscar o Nome Real do Paciente (Corrigido para buscar em 'usuarios')
// ----------------------------------------------------------------------

$nome_paciente = buscarNomePaciente($conexao, $paciente_id);

// Se o nome não for encontrado (pode ser um ID que não é de paciente)
if ($nome_paciente == "Paciente Não Encontrado") {
    // Para fins de teste, é melhor parar o script
    die("ERRO: Paciente com ID $paciente_id não encontrado ou não tem o perfil de paciente.");
}

// ----------------------------------------------------------------------
// AÇÃO 3: Chamar a função do Model para buscar o histórico completo
// ----------------------------------------------------------------------

$historico = buscarHistoricoCompleto($conexao, $paciente_id);

// ----------------------------------------------------------------------
// AÇÃO 4: Carregar a View (A View agora tem acesso a $nome_paciente e $historico)
// ----------------------------------------------------------------------

require_once '../view/historico-paciente-view.php';

mysqli_close($conexao);
?>