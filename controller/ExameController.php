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
        // Organizamos os dados vindos do formulário
        // O Token será gerado automaticamente dentro do Service
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
            header("Location: ../view/painel-medico.php");
        } else {
            $_SESSION['mensagem'] = "Erro ao processar a guia no servidor.";
            header("Location: ../view/guia-exame-create.php?paciente_id=" . $post['paciente_id'] . "&triagem_id=" . $post['triagem_id']);
        }
        exit;
    }
}
// Verifica se o formulário foi submetido
if (isset($_POST['create_guia_exame'])) {
    $controller = new ExameController($conexao);
    $controller->processarCriacao($_POST);
}