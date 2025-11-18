<?php


// Busca todos os itens (medicamentos) de uma receita específica.

// $conexao é o obejto que representa a conexão com o BD 
function buscarItensReceita($conexao, $receita_id) {
    $itens = [];

    // $sql -> variavel q armazena uma string com as infos do comando SQL
    $sql = "SELECT medicamento_nome, concentracao, posologia 
            FROM itens_receita WHERE receita_id = ?";// ? -> é um placeholder para evitar SQL Injection

    
    if ($stmt = mysqli_prepare($conexao, $sql)) { //pega a conexao e a query SQL envia pro BD e prepara a execução
        mysqli_stmt_bind_param($stmt, "i", $receita_id);//liga variáveis ao parâmetros de uma instrução SQL|vincula o valor do ID da receita ao placeholder '?'|"i" indica que é um inteiro
        mysqli_stmt_execute($stmt);//executa a query 
        $resultado = mysqli_stmt_get_result($stmt); //pega o resultado da query 
        while ($linha = mysqli_fetch_assoc($resultado)) { // mysqli_fetch_assoc -> busca uma linha do resultado como um array associativo
            $itens[] = $linha;
        }
        mysqli_stmt_close($stmt);
    }
    return $itens;
}

// Busca o NOME de um paciente pelo seu ID na tabela 'usuarios'

function buscarNomePaciente($conexao, $paciente_id) {
    // CORREÇÃO CRÍTICA: Busca agora na tabela 'usuarios' e verifica o role='paciente'
    $sql = "SELECT nome FROM usuarios WHERE id = ? AND role = 'paciente'"; 
    
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $paciente_id);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($linha = mysqli_fetch_assoc($resultado)) {
            mysqli_stmt_close($stmt);
            // Retorna a string do nome, sem htmlspecialchars (o Controller ou View fará o escape)
            return $linha['nome']; 
        }
        mysqli_stmt_close($stmt);
    }
    return "Paciente Não Encontrado";
}

/**
 * Busca o histórico completo de um paciente, combinando Exames, diagnosticos e Receitas.
 */
function buscarHistoricoCompleto($conexao, $paciente_id) {
    
    $historico_combinado = []; 
    
    // Buscar Historico de Exames

    $sql_exames = "SELECT *, data, 'Exame' AS tipo_evento FROM exame WHERE paciente_id = ? ORDER BY data DESC"; //Desc = decrescente
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
    // 2. Buscar diagnosticos
    // ------------------------------------
    // ATENÇÃO: Use `diagnostico` (minúsculo) conforme seu script SQL
    $sql_diagnosticos = "SELECT *, data, 'diagnostico' AS tipo_evento FROM diagnostico WHERE paciente_id = ? ORDER BY data DESC";
    if ($stmt_diagnosticos = mysqli_prepare($conexao, $sql_diagnosticos)) {
        mysqli_stmt_bind_param($stmt_diagnosticos, "i", $paciente_id);
        mysqli_stmt_execute($stmt_diagnosticos);
        $resultado_diagnosticos = mysqli_stmt_get_result($stmt_diagnosticos);
        while ($linha = mysqli_fetch_assoc($resultado_diagnosticos)) {
            $historico_combinado[] = $linha;
        }
        mysqli_stmt_close($stmt_diagnosticos);
    }
    
    // Buscar Receitas (e seus itens)

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

    //Ordenar o Histórico Completo Cronologicamente (Mais recente primeiro)
    // compara duas datas (B - A) para garantir que o mais recente venha sempre primeiro
    usort($historico_combinado, function($a, $b) {
        // A ordenação e usa timestamp
        // O valor negativo inverte a ordem (para ser decrescente)
        return strtotime($b['data']) - strtotime($a['data']);
    });

    return $historico_combinado;
}
?>