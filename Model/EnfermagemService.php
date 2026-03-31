<?php

/**
 * Classe responsável por todas as regras de negócio 
 * e persistência de dados do módulo de Enfermagem (Triagem).
 */
class EnfermagemService {
    private $db;

    // O construtor recebe a conexão do banco de dados (Injeção de Dependência)
    public function __construct($conexao) {
        $this->db = $conexao;
    }

    /**
     * Salva uma nova triagem no banco de dados.
     * @param array $dados - Array associativo vindo do Controller (acoes.php)
     */
    public function salvarTriagem($dados) {
        // Escapando os dados para evitar SQL Injection (Segurança)
        $paciente_id      = mysqli_real_escape_string($this->db, $dados['paciente_id']);
        $enfermeiro_id    = mysqli_real_escape_string($this->db, $dados['enfermeiro_id']);
        $queixa           = mysqli_real_escape_string($this->db, $dados['queixa_principal']);
        $pressao          = mysqli_real_escape_string($this->db, $dados['pressao_arterial']);
        $temperatura      = mysqli_real_escape_string($this->db, $dados['temperatura']);
        $peso             = mysqli_real_escape_string($this->db, $dados['peso']);
        $altura           = mysqli_real_escape_string($this->db, $dados['altura']);
        $fc               = mysqli_real_escape_string($this->db, $dados['frequencia_cardiaca']);
        $saturacao        = mysqli_real_escape_string($this->db, $dados['saturacao']);
        $risco            = mysqli_real_escape_string($this->db, $dados['classificacao_risco']);

        $sql = "INSERT INTO triagens (
                    paciente_id, enfermeiro_id, queixa_principal, 
                    pressao_arterial, temperatura, peso, altura,
                    frequencia_cardiaca, saturacao, classificacao_risco
                ) VALUES (
                    '$paciente_id', '$enfermeiro_id', '$queixa', 
                    '$pressao', '$temperatura', '$peso', '$altura', 
                    '$fc', '$saturacao', '$risco'
                )";

        if (mysqli_query($this->db, $sql)) {
            return ['sucesso' => true, 'id' => mysqli_insert_id($this->db)];
        }

        return ['sucesso' => false, 'erro' => mysqli_error($this->db)];
    }

    /**
     * Busca as triagens mais recentes para exibir no painel.
     * Faz um JOIN com a tabela usuarios para obter o nome do paciente.
     * @param int $limite - Quantidade de registros a retornar
     */
    public function listarTriagensRecentes($limite = 10) {
        // O JOIN é essencial para não mostrar apenas o ID do paciente, mas o nome real
        $sql = "SELECT t.*, u.nome as paciente_nome 
                FROM triagens t 
                JOIN usuarios u ON t.paciente_id = u.id 
                ORDER BY t.id DESC 
                LIMIT $limite";
                
        $resultado = mysqli_query($this->db, $sql);
        
        // Retornamos o objeto de resultado do MySQL para o Painel percorrer (while)
        return $resultado;
    }

    public function buscarPorId($id) {
    $id = mysqli_real_escape_string($this->db, $id);
    
    $sql = "SELECT t.*, 
                   u_pac.nome as paciente_nome, u_pac.data_nascimento,
                   u_enf.nome as enfermeiro_nome
            FROM triagens t
            JOIN usuarios u_pac ON t.paciente_id = u_pac.id
            JOIN usuarios u_enf ON t.enfermeiro_id = u_enf.id
            WHERE t.id = '$id'";
            
    $resultado = mysqli_query($this->db, $sql);
    return mysqli_fetch_assoc($resultado);
}
}