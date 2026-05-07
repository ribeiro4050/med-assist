<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once '../Model/conexao.php';
require_once '../Model/ExameService.php';

class ExameController {
    private $service;

    public function __construct($conexao) {
        $this->service = new ExameService($conexao);
    }

    public function processarCriacao($post) {
        $dados = [
            'paciente_id'         => $post['paciente_id'],
            'medico_id'           => $post['medico_id'],
            'triagem_id'          => $post['triagem_id'],
            'carater_solicitacao' => $post['carater_solicitacao'],
            'cid_10'              => htmlspecialchars(trim($post['cid_10'])),
            'indicacao_clinica'   => htmlspecialchars(trim($post['indicacao_clinica'])),
            'descricao_exames'    => htmlspecialchars(trim($post['descricao_exames']))
        ];

        if ($this->service->criarGuiaExame($dados)) {
            $_SESSION['mensagem'] = "Guia de Exame gerada com sucesso!";
            header("Location: ../view/atendimento-hub.php?triagem_id=" . $dados['triagem_id'] . "&paciente_id=" . $dados['paciente_id']);
        } else {
            $_SESSION['mensagem'] = "Erro ao processar a guia no servidor.";
            header("Location: ../view/guia-exame-create.php?paciente_id=" . $post['paciente_id'] . "&triagem_id=" . $post['triagem_id']);
        }
        exit;
    }

    // MÉTODO ATUALIZADO: Agora altera o status no banco de dados
    public function finalizarAtendimento($post) {
        $triagem_id = $post['triagem_id'];

        if ($this->service->atualizarStatusTriagem($triagem_id, 'atendido')) {
            $_SESSION['mensagem'] = "Atendimento finalizado com sucesso!";
        } else {
            $_SESSION['mensagem'] = "Erro ao atualizar o status para atendido.";
        }
        
        header("Location: ../view/painel-medico.php");
        exit;
    }
}

// Lógica de captura das ações do formulário
if (isset($_POST['create_guia_exame'])) {
    $controller = new ExameController($conexao);
    $controller->processarCriacao($_POST);
}

if (isset($_POST['concluir_atendimento'])) {
    $controller = new ExameController($conexao);
    $controller->finalizarAtendimento($_POST);
}