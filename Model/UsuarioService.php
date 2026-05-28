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
        
        try {
            if (mysqli_query($this->db, $sql)) {
                return ['sucesso' => true];
            }
        } catch (mysqli_sql_exception $e) {
            return ['sucesso' => false, 'codigo_erro' => $e->getCode()];
        }
        return ['sucesso' => false, 'codigo_erro' => 0];
    }

    public function cadastrarProfissional($nome, $email, $cpf, $data_nascimento, $senha_hash, $role, $coluna, $registro) {
        $sql = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, senha, role, $coluna) 
                VALUES ('$nome', '$email', '$cpf', '$data_nascimento', '$senha_hash', '$role', '$registro')";
        
        try {
            if (mysqli_query($this->db, $sql)) {
                return true;
            }
        } catch (mysqli_sql_exception $e) {
            return false;
        }
        return false;
    }

    public function atualizarUsuario($usuario_id, $nome, $email, $cpf, $data_nascimento, $senha) {
        $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', cpf = '$cpf', data_nascimento = '$data_nascimento'";
        
        if (!empty($senha)) {
            $sql .= ", senha = '" . password_hash($senha, PASSWORD_DEFAULT) . "'";
        }
        $sql .= " WHERE id = $usuario_id";

        return mysqli_query($this->db, $sql);
    }

    public function deletarUsuario($id) {
        $sql = "DELETE FROM usuarios WHERE id = '$id'";
        return mysqli_query($this->db, $sql);
    }

    public function buscarPacientesParaSelect() {
        $sql = "SELECT id, nome FROM usuarios WHERE role = 'paciente' ORDER BY nome ASC";
        return mysqli_query($this->db, $sql);
    }
}