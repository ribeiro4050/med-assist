<?php
// Model/historico-paciente-model.php

/**
 * Busca todos os itens (medicamentos) de uma receita específica.
 */
function buscarItensReceita($conexao, $receita_id) {
    $itens = [];
    $sql = "SELECT medicamento_nome, concentracao, posologia 
            FROM itens_receita WHERE receita_id = ?";
    
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $receita_id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        while ($linha = mysqli_fetch_assoc($resultado)) {
            $itens[] = $linha;
        }
        mysqli_stmt_close($stmt);
    }
    return $itens;
}

/**
 * Busca o nome de um paciente pelo seu ID na nova tabela 'Paciente'.
 */
function buscarNomePaciente($conexao, $paciente_id) {
    // ATENÇÃO: Agora busca na tabela 'Paciente'
    $sql = "SELECT nome FROM Paciente WHERE id = ?"; 
    
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $paciente_id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($linha = mysqli_fetch_assoc($resultado)) {
            mysqli_stmt_close($stmt);
            return htmlspecialchars($linha['nome']);
        }
        mysqli_stmt_close($stmt);
    }
    return "Paciente Não Encontrado";
}

/**
 * Busca o histórico completo de um paciente, combinando Exames, Diagnósticos e Receitas.
 */
function buscarHistoricoCompleto($conexao, $paciente_id) {
    
    $historico_combinado = []; 
    
    // ------------------------------------
    // 1. Buscar Exames
    // ------------------------------------
    $sql_exames = "SELECT *, data, 'Exame' AS tipo_evento FROM Exame WHERE paciente_id = ? ORDER BY data DESC";
    if ($stmt_exames = mysqli_prepare($conexao, $sql_exames)) {
        mysqli_stmt_bind_param($stmt_exames, "i", $paciente_id);
        mysqli_stmt_execute($stmt_exames);
        $resultado_exames = mysqli_stmt_get_result($stmt_exames);
        while ($linha = mysqli_fetch_assoc($resultado_exames)) {
            $historico_combinado[] = $linha;
        }
        mysqli_stmt_close($stmt_exames);
    }
    
    // ------------------------------------
    // 2. Buscar Diagnósticos
    // ------------------------------------
    $sql_diagnosticos = "SELECT *, data, 'Diagnóstico' AS tipo_evento FROM Diagnostico WHERE paciente_id = ? ORDER BY data DESC";
    if ($stmt_diagnosticos = mysqli_prepare($conexao, $sql_diagnosticos)) {
        mysqli_stmt_bind_param($stmt_diagnosticos, "i", $paciente_id);
        mysqli_stmt_execute($stmt_diagnosticos);
        $resultado_diagnosticos = mysqli_stmt_get_result($stmt_diagnosticos);
        while ($linha = mysqli_fetch_assoc($resultado_diagnosticos)) {
            $historico_combinado[] = $linha;
        }
        mysqli_stmt_close($stmt_diagnosticos);
    }
    
    // ------------------------------------
    // 3. Buscar Receitas (e seus itens)
    // ------------------------------------
    $sql_receitas = "SELECT id, data_prescricao AS data, 'Receita' AS tipo_evento FROM receitas WHERE paciente_id = ? ORDER BY data_prescricao DESC";
    
    if ($stmt_receitas = mysqli_prepare($conexao, $sql_receitas)) {
        mysqli_stmt_bind_param($stmt_receitas, "i", $paciente_id);
        mysqli_stmt_execute($stmt_receitas);
        $resultado_receitas = mysqli_stmt_get_result($stmt_receitas);
        
        while ($linha = mysqli_fetch_assoc($resultado_receitas)) {
            // Chamada para a nova função que busca os itens da receita
            $linha['itens'] = buscarItensReceita($conexao, $linha['id']);
            $historico_combinado[] = $linha; 
        }
        mysqli_stmt_close($stmt_receitas);
    }

    // 4. Ordenar o Histórico Completo Cronologicamente
    usort($historico_combinado, function($a, $b) {
        return strtotime($b['data']) - strtotime(isset($a['data']) ? $a['data'] : '1970-01-01') - strtotime(isset($b['data']) ? $b['data'] : '1970-01-01');
    });

    return $historico_combinado;
}
?>