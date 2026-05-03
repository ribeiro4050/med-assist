<?php

class MedicoService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function listarFilaEspera() {
        // Query robusta: busca dados do paciente e sinais vitais da triagem
        // Certifique-se que estas colunas existem na sua tabela 'triagens'
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
                WHERE t.status = 'Aguardando Medico'
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
        
        if (!$resultado) {
            // Se falhar, retorna false para o controller tratar
            return false;
        }
        
        return $resultado;
    }
}