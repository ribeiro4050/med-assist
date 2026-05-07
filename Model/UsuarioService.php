<?php
class UsuarioService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function salvarUsuario($nome, $email, $cpf, $data_nascimento, $senha_hash, $role, $crm, $coren) {
        $sql = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, senha, role, crm_registro, coren_registro) 
                VALUES ('$nome', '$email', '$cpf', '$data_nascimento', '$senha_hash', '$role', ";
        $sql .= empty($crm) ? "NULL, " : "'$crm', ";
        $sql .= empty($coren) ? "NULL)" : "'$coren')";
        
        return mysqli_query($this->db, $sql);
    }

    public function cadastrarProfissional($nome, $email, $cpf, $senha_hash, $role, $coluna, $registro) {
        $sql = "INSERT INTO usuarios (nome, email, cpf, senha, role, $coluna) 
                VALUES ('$nome', '$email', '$cpf', '$senha_hash', '$role', '$registro')";
        return mysqli_query($this->db, $sql);
    }

    public function atualizarUsuario($sql) {
        // Recebe o SQL montado pelo controller para manter a compatibilidade com sua lógica atual
        return mysqli_query($this->db, $sql);
    }

    public function deletarUsuario($id) {
        $sql = "DELETE FROM usuarios WHERE id = '$id'";
        return mysqli_query($this->db, $sql);
    }
}