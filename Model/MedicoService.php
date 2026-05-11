<?php

class MedicoService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function listarFilaEspera() {
        // Query ajustada para as colunas reais do seu dump (triagens)
        $sql = "SELECT 
                    t.id as triagem_id, 
                    u.nome as paciente_nome, 
                    t.classificacao_risco, 
                    t.data_hora, 
                    t.paciente_id, 
                    t.pressao_sistolica, 
                    t.pressao_diastolica, 
                    t.temperatura, 
                    t.queixa_principal
                FROM triagens t
                JOIN usuarios u ON t.paciente_id = u.id
                WHERE t.status = 'Aguardando Médico'
                ORDER BY 
                    CASE 
                        WHEN t.classificacao_risco = 'Vermelho' THEN 1
                        WHEN t.classificacao_risco = 'Laranja' THEN 2
                        WHEN t.classificacao_risco = 'Amarelo' THEN 3
                        WHEN t.classificacao_risco = 'Verde' THEN 4
                        WHEN t.classificacao_risco = 'Azul' THEN 5
                        ELSE 6
                    END ASC, t.data_hora ASC";
                    
        $resultado = mysqli_query($this->db, $sql);
        return $resultado;
    }

    public function buscarPrescricoesAtuais($id_paciente) {
        $id_paciente = mysqli_real_escape_string($this->db, $id_paciente);
        
        $sql = "SELECT ir.*, r.data_prescricao, ir.data_inicio, ir.data_fim 
            FROM itens_receita ir 
            JOIN receitas r ON ir.receita_id = r.id 
            WHERE r.paciente_id = '$id_paciente' 
            ORDER BY r.data_prescricao DESC";
                
        return mysqli_query($this->db, $sql);
    }

    // DICA: Novo método para buscar a última triagem real do paciente para o prontuário
    public function buscarUltimaTriagem($id_paciente) {
        $id_paciente = mysqli_real_escape_string($this->db, $id_paciente);
        $sql = "SELECT * FROM triagens WHERE paciente_id = '$id_paciente' ORDER BY data_hora DESC LIMIT 1";
        $res = mysqli_query($this->db, $sql);
        return mysqli_fetch_assoc($res);
    }
}