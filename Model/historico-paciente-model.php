<?php

class HistoricoPacienteService {
    private $db;

    // O construtor recebe a conexão centralizada do banco de dados
    public function __construct($conexao) {
        $this->db = $conexao;
    }

    /**
     * Busca o NOME de um paciente pelo seu ID na tabela 'usuarios'
     */
    public function obterNomePaciente($paciente_id) {
        // CORREÇÃO CRÍTICA: Busca na tabela 'usuarios' e verifica o role='paciente'
        $sql = "SELECT nome FROM usuarios WHERE id = ? AND role = 'paciente'"; 
        
        if ($stmt = mysqli_prepare($this->db, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $paciente_id);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($linha = mysqli_fetch_assoc($resultado)) {
                mysqli_stmt_close($stmt);
                return $linha['nome']; 
            }
            mysqli_stmt_close($stmt);
        }
        return "Paciente Não Encontrado";
    }

    /**
     * Busca todos os itens (medicamentos) de uma receita específica.
     */
    private function buscarItensReceita($receita_id) {
        $itens = [];
        $sql = "SELECT medicamento_nome, concentracao, posologia 
                FROM itens_receita WHERE receita_id = ?";

        if ($stmt = mysqli_prepare($this->db, $sql)) {
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
     * Busca o histórico completo de um paciente, combinando Exames, Diagnósticos e Receitas.
     */
    public function obterHistoricoCompleto($paciente_id) {
        $historico_combinado = []; 
        
        // 1. Buscar Histórico de Exames
        $sql_exames = "SELECT id, 
                              data_solicitacao AS data, 
                              carater_solicitacao AS tipo, 
                              descricao_exames AS resultado, 
                              'Exame' AS tipo_evento 
                       FROM guia_exames 
                       WHERE paciente_id = ? 
                       ORDER BY data_solicitacao DESC";
                       
        if ($stmt_exames = mysqli_prepare($this->db, $sql_exames)) {
            mysqli_stmt_bind_param($stmt_exames, "i", $paciente_id);
            mysqli_stmt_execute($stmt_exames);
            $resultado_exames = mysqli_stmt_get_result($stmt_exames);
            while ($linha = mysqli_fetch_assoc($resultado_exames)) {
                $historico_combinado[] = $linha;
            }
            mysqli_stmt_close($stmt_exames);
        }
        
        // 2. Buscar Diagnósticos
        $sql_diagnosticos = "SELECT *, data, 'diagnostico' AS tipo_evento FROM diagnostico WHERE paciente_id = ? ORDER BY data DESC";
        if ($stmt_diagnosticos = mysqli_prepare($this->db, $sql_diagnosticos)) {
            mysqli_stmt_bind_param($stmt_diagnosticos, "i", $paciente_id);
            mysqli_stmt_execute($stmt_diagnosticos);
            $resultado_diagnosticos = mysqli_stmt_get_result($stmt_diagnosticos);
            while ($linha = mysqli_fetch_assoc($resultado_diagnosticos)) {
                $historico_combinado[] = $linha;
            }
            mysqli_stmt_close($stmt_diagnosticos);
        }
        
        // 3. Buscar Receitas (e seus itens)
        $sql_receitas = "SELECT id, data_prescricao AS data, 'Receita' AS tipo_evento FROM receitas WHERE paciente_id = ? ORDER BY data_prescricao DESC";
        if ($stmt_receitas = mysqli_prepare($this->db, $sql_receitas)) {
            mysqli_stmt_bind_param($stmt_receitas, "i", $paciente_id);
            mysqli_stmt_execute($stmt_receitas);
            $resultado_receitas = mysqli_stmt_get_result($stmt_receitas);
            
            while ($linha = mysqli_fetch_assoc($resultado_receitas)) {
                // Chamada interna para buscar os itens vinculados a esta receita
                $linha['itens'] = $this->buscarItensReceita($linha['id']);
                $historico_combinado[] = $linha; 
            }
            mysqli_stmt_close($stmt_receitas);
        }

        // Ordenar o Histórico Completo Cronologicamente (Mais recente primeiro)
        usort($historico_combinado, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return $historico_combinado;
    }
}
?>