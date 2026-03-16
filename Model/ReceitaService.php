<?php

class ReceitaService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function criarReceita($dados, $itens) {
        // Desempacota os dados da receita principal
        $medico_id = $dados['medico_id'];
        $paciente_id = $dados['paciente_id'];
        $tipo_receita = $dados['tipo_receita'];
        $observacoes = $dados['observacoes'];
        $data_prescricao = date('Y-m-d H:i:s');

        // SQL da Receita Principal
        $obs_value = empty($observacoes) ? "NULL" : "'$observacoes'";
        $sql = "INSERT INTO receitas (medico_id, paciente_id, data_prescricao, tipo_receita, observacoes) 
                VALUES ('$medico_id', '$paciente_id', '$data_prescricao', '$tipo_receita', $obs_value)";

        if (mysqli_query($this->db, $sql)) {
            $receita_id = mysqli_insert_id($this->db);
            
            // Loop para inserir os itens (Usa a lógica que estava no acoes.php)
            foreach ($itens['nomes'] as $key => $nome) {
                $nome_seguro = $nome; // Já vem filtrado pelo filtrar_sql no controller
                $conc = $itens['concentracoes'][$key];
                $qtd = $itens['quantidades'][$key];
                $pos = $itens['posologias'][$key];

                if (empty($nome_seguro)) continue;

                $sql_item = "INSERT INTO itens_receita (receita_id, medicamento_nome, concentracao, quantidade_total, posologia) 
                             VALUES ('$receita_id', '$nome_seguro', '$conc', '$qtd', '$pos')";
                
                mysqli_query($this->db, $sql_item);
            }
            return ['sucesso' => true, 'id' => $receita_id];
        }

        return ['sucesso' => false, 'erro' => mysqli_error($this->db)];
    }
}