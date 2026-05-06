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
            header("Location: ../view/atendimento-hub.php?triagem_id=" . $dados['triagem_id']);
        } else {
            $_SESSION['mensagem'] = "Erro ao processar a guia.";
            header("Location: ../view/atendimento-hub.php?triagem_id=" . $dados['triagem_id']);
        }
        exit;
    }

    // MÉTODO ATUALIZADO: Processa o diagnóstico e encerra o atendimento[cite: 8]
    public function finalizarAtendimento($post) {
        $dados = [
            'triagem_id'  => $post['triagem_id'],
            'paciente_id' => $post['paciente_id'],
            'medico_id'   => $_SESSION['id_usuario'],
            'cid_10'      => htmlspecialchars(trim($post['cid_10'])),
            'descricao'   => htmlspecialchars(trim($post['diagnostico_descricao']))
        ];

        if ($this->service->salvarDiagnosticoEFinalizar($dados)) {
            $_SESSION['mensagem'] = "Atendimento finalizado e diagnóstico registrado!";
        } else {
            $_SESSION['mensagem'] = "Erro ao registrar o diagnóstico.";
        }
        
        header("Location: ../view/painel-medico.php");
        exit;
    }
}

$controller = new ExameController($conexao);

if (isset($_POST['create_guia_exame'])) {
    $controller->processarCriacao($_POST);
}

if (isset($_POST['concluir_atendimento'])) {
    $controller = new ExameController($_POST);
}