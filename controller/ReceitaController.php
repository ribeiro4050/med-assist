<?php
session_start();
require_once '../Model/conexao.php';
require_once '../Model/ReceitaService.php';

$receitaService = new ReceitaService($conexao);

if (isset($_POST['create_receita'])) {
    $medico_id = filtrar_sql($_POST['medico_id']);
    $paciente_id = filtrar_sql($_POST['paciente_id']);
    $triagem_id = filtrar_sql($_POST['triagem_id'] ?? ''); // Captura o ID da triagem
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
        'posologias' => array_map('filtrar_sql', $_POST['posologia'])
    ];

    $resultado = $receitaService->criarReceita($dados_receita, $itens_receita);

    if ($resultado['sucesso']) {
        $_SESSION['mensagem'] = "Receita prescrita com sucesso!";
        
        // Lógica de Redirecionamento Inteligente
        if (!empty($triagem_id)) {
            // Se veio do Hub, volta para o Hub
            header('Location: ../view/atendimento-hub.php?triagem_id=' . $triagem_id);
        } else {
            // Se não, vai para a listagem de receitas
            header('Location: ../view/receitas.php?id=' . $resultado['id']);
        }
    } else {
        $_SESSION['mensagem'] = "Erro ao criar receita: " . $resultado['erro'];
        header('Location: ../view/receita-create.php');
    }
    exit;
}