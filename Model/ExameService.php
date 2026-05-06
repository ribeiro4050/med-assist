<?php

class ExameService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function criarGuiaExame($dados) {
        $token_assinatura = hash('sha256', $dados['paciente_id'] . $dados['medico_id'] . uniqid() . time());

        $paciente_id = mysqli_real_escape_string($this->db, $dados['paciente_id']);
        $medico_id = mysqli_real_escape_string($this->db, $dados['medico_id']);
        $triagem_id = mysqli_real_escape_string($this->db, $dados['triagem_id']);
        $carater = mysqli_real_escape_string($this->db, $dados['carater_solicitacao']);
        $cid = mysqli_real_escape_string($this->db, $dados['cid_10']);
        $indicacao = mysqli_real_escape_string($this->db, $dados['indicacao_clinica']);
        $descricao = mysqli_real_escape_string($this->db, $dados['descricao_exames']);
        $data = date('Y-m-d H:i:s');

        $sql = "INSERT INTO guia_exames (paciente_id, medico_id, triagem_id, carater_solicitacao, cid_10, indicacao_clinica, descricao_exames, data_solicitacao, token_assinatura) 
                VALUES ('$paciente_id', '$medico_id', '$triagem_id', '$carater', '$cid', '$indicacao', '$descricao', '$data', '$token_assinatura')";

        return mysqli_query($this->db, $sql);
    }

    // MÉTODO CONSOLIDADO: Salva o diagnóstico e altera o status da triagem
    public function salvarDiagnosticoEFinalizar($dados) {
        $triagem_id  = mysqli_real_escape_string($this->db, $dados['triagem_id']);
        $paciente_id = mysqli_real_escape_string($this->db, $dados['paciente_id']);
        $medico_id   = mysqli_real_escape_string($this->db, $dados['medico_id']);
        $cid_10      = mysqli_real_escape_string($this->db, $dados['cid_10']);
        $descricao   = mysqli_real_escape_string($this->db, $dados['descricao']);
        $data        = date('Y-m-d H:i:s');

        // 1. Insere o registro na nova tabela de diagnóstico[cite: 7]
        $sql_diag = "INSERT INTO diagnostico (triagem_id, paciente_id, medico_id, data, cid_10, descricao) 
                     VALUES ('$triagem_id', '$paciente_id', '$medico_id', '$data', '$cid_10', '$descricao')";
        
        if (mysqli_query($this->db, $sql_diag)) {
            // 2. Se salvou o diagnóstico, atualiza o status para 'atendido'[cite: 7]
            $sql_status = "UPDATE triagens SET status = 'atendido' WHERE id = '$triagem_id'";
            return mysqli_query($this->db, $sql_status);
        }
        return false;
    }
}