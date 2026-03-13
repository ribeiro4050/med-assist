<?php

class AuthService {
    private $db;

    // O construtor recebe a conexão que você já criou
    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function autenticar($email, $registro, $senha) {
      $sql = "";

        // Tenta localizar por Email ou Registro (CRM/COREN)
        if (!empty($email)) {
            $email_limpo = mysqli_real_escape_string($this->db, $email);
            $sql = "SELECT * FROM usuarios WHERE email = '$email_limpo'";
        } else if (!empty($registro)) {
            $reg_limpo = mysqli_real_escape_string($this->db, $registro);
            $sql = "SELECT * FROM usuarios WHERE crm_registro = '$reg_limpo' OR coren_registro = '$reg_limpo'";
        } else {
            return ['sucesso' => false, 'erro' => "Preencha o Email ou o Registro."];
        }

        $query = mysqli_query($this->db, $sql);

        if (mysqli_num_rows($query) == 1) {
            $usuario = mysqli_fetch_assoc($query);

            // Verifica a senha
            if (password_verify($senha, $usuario['senha'])) {
                // Validação extra para profissionais
                if (!empty($registro) && !in_array($usuario['role'], ['medico', 'enfermeiro', 'admin'])) {
                    return ['sucesso' => false, 'erro' => "Acesso negado para este registro."];
                }
                
                // Retorna os dados do usuário para o Controller iniciar a sessão
                return ['sucesso' => true, 'dados' => $usuario];
            }
        }

        return ['sucesso' => false, 'erro' => "Usuário e/ou senha inválidos."];
    }
}