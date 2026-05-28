<?php
session_start();
require_once '../Model/conexao.php';
require_once '../Model/ReceitaService.php';

$receitaService = new ReceitaService($conexao);

if (isset($_POST['create_receita'])) {
    $medico_id = filtrar_sql($_POST['medico_id']);
    $paciente_id = filtrar_sql($_POST['paciente_id']);
    $triagem_id = filtrar_sql($_POST['triagem_id'] ?? ''); 
    $timestamp = date('Y-m-d H:i:s');
    
    $token_assinatura = hash('sha256', $medico_id . $paciente_id . $timestamp);

    $dados_receita = [
        'medico_id' => $medico_id,
        'paciente_id' => $paciente_id,
        'tipo_receita' => filtrar_sql($_POST['tipo_receita']),
        'observacoes' => filtrar_sql($_POST['observacoes'] ?? ''),
        'token_assinatura' => $token_assinatura
    ];

    $itens_receita = [
        'nomes' => array_map('filtrar_sql', $_POST['medicamento_nome']),
        'concentracoes' => array_map('filtrar_sql', $_POST['concentracao']),
        'quantidades' => array_map('filtrar_sql', $_POST['quantidade_total']),
        'posologias' => array_map('filtrar_sql', $_POST['posologia']),
        'datas_inicio' => array_map('filtrar_sql', $_POST['data_inicio']),
        'datas_fim' => array_map('filtrar_sql', $_POST['data_fim'])
    ];

    $resultado = $receitaService->criarReceita($dados_receita, $itens_receita);

    if ($resultado['sucesso']) {
        $_SESSION['mensagem'] = "Receita prescrita com sucesso!";
        
        if (!empty($triagem_id)) {
            header('Location: ../view/atendimento-hub.php?triagem_id=' . $triagem_id);
        } else {
            header('Location: ../view/receitas.php');
        }
    } else {
        $_SESSION['mensagem'] = "Erro ao criar receita: " . $resultado['erro'];
        header('Location: ../view/receita-create.php');
    }
    exit;
}

if (isset($_POST['cancelar_item'])) {
    $item_id = (int)$_POST['item_id'];
    $paciente_id = (int)$_POST['paciente_id'];
    $justificativa = $_POST['justificativa']; // Repassado limpo, o Model fará o escape seguro

    // Executa a interrupção através da camada especialista Model
    if ($receitaService->cancelarItemReceita($item_id, $justificativa)) {
        $_SESSION['mensagem'] = "O medicamento foi interrompido com sucesso.";
    } else {
        $_SESSION['mensagem'] = "Erro ao interromper o medicamento no servidor.";
    }

    // Redireciona de volta para o prontuário do paciente específico
    header("Location: ../view/prontuario-medico.php?id=" . $paciente_id);
    exit;
}