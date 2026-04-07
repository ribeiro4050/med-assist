<?php

/**
 * Classe responsável pelas regras de negócio e persistência 
 * do módulo de Enfermagem (Triagem) do MedAssist.
 */
class EnfermagemService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    /**
     * Salva uma nova triagem usando valores separados para pressão.
     */
    public function salvarTriagem($dados) {
        // Escapando dados para segurança (Segurança da Informação)
        $paciente_id        = mysqli_real_escape_string($this->db, $dados['paciente_id']);
        $enfermeiro_id      = mysqli_real_escape_string($this->db, $dados['enfermeiro_id']);
        $queixa             = mysqli_real_escape_string($this->db, $dados['queixa_principal']);
        
        // campos de pressão separados
        $sistolica          = mysqli_real_escape_string($this->db, $dados['pressao_sistolica']);
        $diastolica         = mysqli_real_escape_string($this->db, $dados['pressao_diastolica']);
        
        $temperatura        = mysqli_real_escape_string($this->db, $dados['temperatura']);
        $peso               = mysqli_real_escape_string($this->db, $dados['peso']);
        $altura             = mysqli_real_escape_string($this->db, $dados['altura']);
        $frequencia         = mysqli_real_escape_string($this->db, $dados['frequencia_cardiaca']);
        $saturacao          = mysqli_real_escape_string($this->db, $dados['saturacao']);
        $risco              = mysqli_real_escape_string($this->db, $dados['classificacao_risco']);

        // Query atualizada: removemos pressao_arterial e usamos os dois campos novos
        $sql = "INSERT INTO triagens (
                    paciente_id, enfermeiro_id, queixa_principal, 
                    pressao_sistolica, pressao_diastolica, temperatura, 
                    peso, altura, frequencia_cardiaca, saturacao, 
                    classificacao_risco, data_hora
                ) VALUES (
                    '$paciente_id', '$enfermeiro_id', '$queixa', 
                    '$sistolica', '$diastolica', '$temperatura', 
                    '$peso', '$altura', '$frequencia', '$saturacao', 
                    '$risco', NOW()
                )";

        if (mysqli_query($this->db, $sql)) {
            return ['sucesso' => true, 'id' => mysqli_insert_id($this->db)];
        }

        return ['sucesso' => false, 'erro' => mysqli_error($this->db)];
    }

    /**
     * Lista as triagens para o painel principal.
     */
    public function listarTriagensRecentes($limite = 15) {
        $sql = "SELECT t.*, u.nome as paciente_nome 
                FROM triagens t 
                JOIN usuarios u ON t.paciente_id = u.id 
                ORDER BY t.data_hora DESC 
                LIMIT $limite";
                
        return mysqli_query($this->db, $sql);
    }

    /**
     * Busca detalhes completos de uma triagem específica para a triagem-view.
     */
    public function buscarPorId($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        
        // O JOIN u_enf serve para sabermos qual enfermeiro fez a triagem
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