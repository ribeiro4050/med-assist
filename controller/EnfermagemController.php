<?php
session_start();
require_once '../Model/conexao.php';
require_once '../Model/EnfermagemService.php';

// Instanciação correta do Service injetando a conexão com o banco
$enfermagemService = new EnfermagemService($conexao);

// --- FLUXO DE CRIAÇÃO DE TRIAGEM ---
if (isset($_POST['create_triagem'])) {
    $dados_triagem = [
        'paciente_id'         => filtrar_sql($_POST['paciente_id']),
        'enfermeiro_id'       => $_SESSION['id_usuario'],
        'queixa_principal'    => filtrar_sql($_POST['queixa_principal']),
        'pressao_sistolica'   => filtrar_sql($_POST['pressao_sistolica']),
        'pressao_diastolica'  => filtrar_sql($_POST['pressao_diastolica']),
        'temperatura'         => filtrar_sql($_POST['temperatura']),
        'peso'                => filtrar_sql($_POST['peso']),
        'altura'              => filtrar_sql($_POST['altura']),
        'frequencia_cardiaca' => filtrar_sql($_POST['frequencia_cardiaca']),
        'saturacao'           => filtrar_sql($_POST['saturacao']),
        'classificacao_risco' => filtrar_sql($_POST['classificacao_risco'])
    ];

    $resultado = $enfermagemService->salvarTriagem($dados_triagem);

    if ($resultado['sucesso']) {
        $_SESSION['mensagem'] = "Triagem realizada com sucesso!";
        header('Location: ../view/painel-enfermagem.php');
    } else {
        $_SESSION['mensagem'] = "Erro na triagem: " . $resultado['erro'];
        header('Location: ../view/triagem-create.php');
    }
    exit;
}

// --- FLUXO DO CHECKLIST DE ADMINISTRAÇÃO UNIFICADO ---
if (isset($_POST['registrar_administracao']) || isset($_POST['confirmar_dose'])) {
    $item_id = filtrar_sql($_POST['item_id']);
    $observacao = isset($_POST['observacao']) ? filtrar_sql($_POST['observacao']) : '';
    $enfermeiro_id = $_SESSION['id_usuario'];
    $status = isset($_POST['status']) ? filtrar_sql($_POST['status']) : 'Administrado';
    
    // Identifica se a view enviou o ID do paciente de forma direta
    $paciente_id = isset($_POST['paciente_id']) ? filtrar_sql($_POST['paciente_id']) : null;

    // Se o paciente_id não veio (origem view/perfil.php), delegamos a busca para o Model
    if (empty($paciente_id)) {
        $paciente_id = $enfermagemService->buscarPacienteIdPorItemReceita($item_id);
    }

    // Executa a persistência de dados isolada no Service
    $resultado = $enfermagemService->registrarAdministracao($item_id, $enfermeiro_id, $paciente_id, $status, $observacao);

    if ($resultado) {
        $_SESSION['mensagem'] = "Administração registrada com sucesso!";
        $_SESSION['tipo_mensagem'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao registrar administração.";
        $_SESSION['tipo_mensagem'] = "danger";
    }
    
    // Redirecionamento baseado na tela que originou a requisição
    if (isset($_POST['confirmar_dose'])) {
        header("Location: ../view/perfil.php");
    } else {
        header("Location: ../view/gestao-paciente.php?id=$paciente_id");
    }
    exit;
}