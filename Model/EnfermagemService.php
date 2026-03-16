<?php

class EnfermagemService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function salvarTriagem($dados) {
        // Coletamos os dados já filtrados que virão do Controller
        $paciente_id      = $dados['paciente_id'];
        $enfermeiro_id    = $dados['enfermeiro_id'];
        $queixa           = $dados['queixa_principal'];
        $pressao          = $dados['pressao_arterial'];
        $temperatura      = $dados['temperatura'];
        $peso             = $dados['peso'];
        $altura           = $dados['altura'];
        $fc               = $dados['frequencia_cardiaca'];
        $saturacao        = $dados['saturacao'];
        $risco            = $dados['classificacao_risco'];

        $sql = "INSERT INTO triagens (
                    paciente_id, enfermeiro_id, queixa_principal, 
                    pressao_arterial, temperatura, peso, 
                    frequencia_cardiaca, saturacao, classificacao_risco
                ) VALUES (
                    '$paciente_id', '$enfermeiro_id', '$queixa', 
                    '$pressao', '$temperatura', '$peso', 
                    '$fc', '$saturacao', '$risco'
                )";

        if (mysqli_query($this->db, $sql)) {
            return ['sucesso' => true, 'id' => mysqli_insert_id($this->db)];
        }

        return ['sucesso' => false, 'erro' => mysqli_error($this->db)];
    }
}