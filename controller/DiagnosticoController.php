<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../Model/conexao.php';
require_once '../Model/DiagnosticoService.php';

class DiagnosticoController {
    private $service;

    public function __construct($conexao) {
        $this->service = new DiagnosticoService($conexao);
    }

    public function criar($post) {
        $dados = [
            'triagem_id'  => $post['triagem_id'],
            'paciente_id' => $post['paciente_id'],
            'medico_id'   => $_SESSION['id_usuario'],
            'cid_10'      => htmlspecialchars(trim($post['cid_10'])),
            'descricao'   => htmlspecialchars(trim($post['diagnostico_descricao']))
        ];

        if ($this->service->salvar($dados)) {
            $_SESSION['mensagem'] = "Laudo diagnóstico gravado com sucesso!";
        } else {
            $_SESSION['mensagem'] = "Erro ao gravar o laudo.";
        }
        
        // Retorna sempre para o Hub para o médico decidir o próximo passo[cite: 6, 10]
        header("Location: ../view/atendimento-hub.php?triagem_id=" . $dados['triagem_id']);
        exit;
    }
}

$controller = new DiagnosticoController($conexao);

if (isset($_POST['btn_salvar_diagnostico'])) {
    $controller->criar($_POST);
}