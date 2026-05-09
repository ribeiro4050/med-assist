<?php
session_start();
require_once '../Model/conexao.php';

if (isset($_POST['confirmar_dose'])) {
    // 1. Coleta os dados do formulário e da sessão
    $item_receita_id = mysqli_real_escape_string($conexao, $_POST['item_id']);
    $observacao = mysqli_real_escape_string($conexao, $_POST['observacao']);
    $enfermeiro_id = $_SESSION['id_usuario']; // ID de quem está logado
    
    // 2. Busca o paciente_id vinculado a esse item de receita
    $sql_busca_paciente = "SELECT r.paciente_id 
                           FROM itens_receita ir 
                           JOIN receitas r ON ir.receita_id = r.id 
                           WHERE ir.id = '$item_receita_id'";
    $res_paciente = mysqli_query($conexao, $sql_busca_paciente);
    $dados_paciente = mysqli_fetch_assoc($res_paciente);
    $paciente_id = $dados_paciente['paciente_id'];

    // 3. Insere na tabela de administração
    $sql_insert = "INSERT INTO administracao_medicamentos 
                   (item_receita_id, enfermeiro_id, paciente_id, status, observacao, data_administracao) 
                   VALUES 
                   ('$item_receita_id', '$enfermeiro_id', '$paciente_id', 'Administrado', '$observacao', NOW())";

    if (mysqli_query($conexao, $sql_insert)) {
        $_SESSION['mensagem'] = "Administração confirmada com sucesso!";
        $_SESSION['tipo_mensagem'] = "success";
    } else {
        $_SESSION['mensagem'] = "Erro ao confirmar: " . mysqli_error($conexao);
        $_SESSION['tipo_mensagem'] = "danger";
    }

    // 4. Volta para o perfil
    header("Location: ../view/perfil.php");
    exit();
} else {
    header("Location: ../view/perfil.php");
    exit();
}