<?php
class DiagnosticoService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function salvar($dados) {
        $triagem_id  = mysqli_real_escape_string($this->db, $dados['triagem_id']);
        $paciente_id = mysqli_real_escape_string($this->db, $dados['paciente_id']);
        $medico_id   = mysqli_real_escape_string($this->db, $dados['medico_id']);
        $cid_10      = mysqli_real_escape_string($this->db, $dados['cid_10']);
        $descricao   = mysqli_real_escape_string($this->db, $dados['descricao']);
        $data        = date('Y-m-d H:i:s');

        // Apenas insere o laudo, sem mexer no status da triagem ainda[cite: 7]
        $sql = "INSERT INTO diagnostico (triagem_id, paciente_id, medico_id, data, cid_10, descricao) 
                VALUES ('$triagem_id', '$paciente_id', '$medico_id', '$data', '$cid_10', '$descricao')";
        
        return mysqli_query($this->db, $sql);
    }

    public function buscarPorTriagem($triagem_id) {
        $id = mysqli_real_escape_string($this->db, $triagem_id);
        $sql = "SELECT * FROM diagnostico WHERE triagem_id = '$id' LIMIT 1";
        $res = mysqli_query($this->db, $sql);
        return mysqli_fetch_assoc($res);
    }
}