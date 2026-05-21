<?php
session_start();
require_once '../Model/conexao.php';
require_once '../Model/EnfermagemService.php';

$enfermagemService = new EnfermagemService($conexao);

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

// --- CHECKLIST DE ADMINISTRAÇÃO ---
if (isset($_POST['registrar_administracao'])) {
    $item_id = filtrar_sql($_POST['item_id']);
    $paciente_id = filtrar_sql($_POST['paciente_id']);
    $enfermeiro_id = $_SESSION['id_usuario']; // Seguindo seu padrão de nome de sessão
    $status = filtrar_sql($_POST['status']); // Administrado, Recusado, etc.
    $observacao = filtrar_sql($_POST['observacao']);

    $resultado = $enfermagemService->registrarAdministracao($item_id, $enfermeiro_id, $paciente_id, $status, $observacao);

    if ($resultado) {
        $_SESSION['mensagem'] = "Administração registrada com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao registrar administração.";
    }
    
    header("Location: ../view/gestao-paciente.php?id=$paciente_id");
    exit;
}