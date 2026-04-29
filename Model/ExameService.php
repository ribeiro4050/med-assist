<?php

class ExameService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function criarGuiaExame($dados) {
        // Agora o Service gera o próprio Token de Assinatura
        // Usamos os dados que já temos para criar um hash único
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
}