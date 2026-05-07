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

    // NOVO MÉTODO: Altera o status para que o paciente saia da fila de espera
    public function atualizarStatusTriagem($triagem_id, $novo_status) {
        $id = mysqli_real_escape_string($this->db, $triagem_id);
        $status = mysqli_real_escape_string($this->db, $novo_status);
    
        $sql = "UPDATE triagens SET status = '$status' WHERE id = '$id'";
    return mysqli_query($this->db, $sql);
    }
}