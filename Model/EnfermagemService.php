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

    /**
     * Busca o checklist de medicamentos prescritos para um paciente específico.
     * Ajustado para os nomes exatos do banco: itens_receita e item_receita_id.
     */

    public function buscarHistoricoCompletoPaciente($id_paciente) {
        $id_paciente = mysqli_real_escape_string($this->db, $id_paciente);
        
        $sql = "SELECT 
                    ir.medicamento_nome,
                    ir.concentracao,
                    ir.posologia,
                    ir.data_inicio,
                    ir.data_fim,
                    ir.justificativa_cancelamento,
                    am.data_administracao,
                    u_enf.nome as nome_enfermeiro,
                    am.status as status_dose
                FROM itens_receita ir
                LEFT JOIN administracao_medicamentos am ON ir.id = am.item_receita_id
                LEFT JOIN usuarios u_enf ON am.enfermeiro_id = u_enf.id
                JOIN receitas r ON ir.receita_id = r.id
                WHERE r.paciente_id = '$id_paciente'
                ORDER BY ir.data_inicio DESC, am.data_administracao DESC";

        return mysqli_query($this->db, $sql);
    }

    public function listarResumoMedicacaoGeral() {
        $sql = "SELECT 
                    u.id, u.nome, u.cpf,
                    COUNT(ir.id) as total_medicamentos,
                    SUM(CASE WHEN (SELECT COUNT(*) FROM administracao_medicamentos am 
                                WHERE am.item_receita_id = ir.id 
                                AND DATE(am.data_administracao) = CURDATE()) > 0 THEN 1 ELSE 0 END) as doses_aplicadas
                FROM usuarios u
                JOIN receitas r ON u.id = r.paciente_id
                JOIN itens_receita ir ON r.id = ir.receita_id
                WHERE u.role = 'paciente' 
                AND (ir.data_fim >= CURDATE() OR ir.data_fim IS NULL)
                AND ir.justificativa_cancelamento IS NULL
                GROUP BY u.id";
        return mysqli_query($this->db, $sql);
    }

    /**
     * Busca o checklist de medicação para o dia atual.
     * Necessário para a página de gestão e perfil.
     */
    public function buscarChecklistPaciente($id_paciente) {
        $id_paciente = mysqli_real_escape_string($this->db, $id_paciente);
        
        $sql = "SELECT 
                    ir.*, 
                    ir.id AS item_id,
                    u_med.nome as nome_medico,
                    (SELECT COUNT(*) FROM administracao_medicamentos am 
                     WHERE am.item_receita_id = ir.id 
                     AND DATE(am.data_administracao) = CURDATE()) as ja_administrado,
                    (SELECT u_enf.nome FROM administracao_medicamentos am
                     JOIN usuarios u_enf ON am.enfermeiro_id = u_enf.id
                     WHERE am.item_receita_id = ir.id 
                     AND DATE(am.data_administracao) = CURDATE() LIMIT 1) as nome_enfermeiro
                FROM itens_receita ir
                JOIN receitas r ON ir.receita_id = r.id
                JOIN usuarios u_med ON r.medico_id = u_med.id
                WHERE r.paciente_id = '$id_paciente'
                AND (ir.data_fim >= CURDATE() OR ir.data_fim IS NULL)
                GROUP BY ir.id";

        return mysqli_query($this->db, $sql);
    }

    /**
     * Registra a administração de um medicamento no banco de dados.
     */
    public function registrarAdministracao($item_id, $enfermeiro_id, $paciente_id, $status, $observacao) {
        $item_id       = mysqli_real_escape_string($this->db, $item_id);
        $enfermeiro_id = mysqli_real_escape_string($this->db, $enfermeiro_id);
        $paciente_id   = mysqli_real_escape_string($this->db, $paciente_id);
        $status        = mysqli_real_escape_string($this->db, $status);
        $observacao    = mysqli_real_escape_string($this->db, $observacao);
        
        $obs_val = empty($observacao) ? "NULL" : "'$observacao'";

        $sql = "INSERT INTO administracao_medicamentos 
                (item_receita_id, enfermeiro_id, paciente_id, data_administracao, status, observacao) 
                VALUES ('$item_id', '$enfermeiro_id', '$paciente_id', NOW(), '$status', $obs_val)";

        return mysqli_query($this->db, $sql);
    }

}