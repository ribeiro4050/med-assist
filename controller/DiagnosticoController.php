<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Se não estiver logado, ou se o papel (role) NÃO for médico: Bloqueia!
if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'medico') {
    $_SESSION['mensagem'] = "Acesso negado. Apenas médicos podem emitir laudos e diagnósticos.";
    header("Location: ../view/index.php"); 
    exit;
}

require_once '../Model/conexao.php';
require_once '../Model/DiagnosticoService.php';

class DiagnosticoController {
    private $service;
    private $conexao; 

    public function __construct($conexao) {
        $this->conexao = $conexao;
        $this->service = new DiagnosticoService($conexao);
    }

    /**
     * Ação: Prepara os dados necessários para renderizar a View de cadastro
     */
    public function carregarTelaCadastro($get) {
        // TRAVA DE SEGURANÇA ORIGINAL (Movida para dentro da ação específica de criação)
        if ($_SESSION['role_usuario'] !== 'medico' && $_SESSION['role_usuario'] !== 'admin') {
            header("Location: login.php"); 
            exit;
        }

        $triagem_id = $get['triagem_id'] ?? '';
        $paciente_id = $get['paciente_id'] ?? '';

        if (empty($triagem_id) || empty($paciente_id)) {
            header("Location: painel-medico.php"); 
            exit;
        }

        // Executa a busca que antes ficava na View
        $sql = "SELECT nome FROM usuarios WHERE id = '$paciente_id'";
        $res = mysqli_query($this->conexao, $sql);
        $p = mysqli_fetch_assoc($res);

        return [
            'triagem_id'  => $triagem_id,
            'paciente_id' => $paciente_id,
            'p'           => $p
        ];
    }

    /**
     * Ação: Busca os dados do laudo e executa a segurança específica da View de visualização
     */
    public function visualizar($get) {
        $id = $get['id'] ?? '';

        if (empty($id)) {
            $_SESSION['mensagem'] = "ID do diagnóstico não fornecido.";
            header("Location: painel-medico.php"); 
            exit;
        }

        // Busca os dados completos do diagnóstico, paciente e médico no banco (Trazido da View)
        $id = mysqli_real_escape_string($this->conexao, $id);
        $sql = "SELECT d.id, d.data, d.cid_10, d.descricao, d.paciente_id, 
                       u_pac.nome as paciente_nome, 
                       u_med.nome as medico_nome 
                FROM diagnostico d
                JOIN usuarios u_pac ON d.paciente_id = u_pac.id
                JOIN usuarios u_med ON d.medico_id = u_med.id
                WHERE d.id = '$id' LIMIT 1";
        
        $res = mysqli_query($this->conexao, $sql);
        $diag = mysqli_fetch_assoc($res);

        if (!$diag) {
            $_SESSION['mensagem'] = "Diagnóstico não encontrado.";
            header("Location: painel-medico.php"); 
            exit;
        }

        // =========================================================================
        // VALIDAÇÃO DE SEGURANÇA SIMPLIFICADA ORIGINAL (Anti-invasão / IDOR)
        // =========================================================================
        // Bloqueia se: Não estiver logado OU (Se for paciente E o ID logado for diferente do dono do laudo)
        if (
            !isset($_SESSION['logado']) || 
            ($_SESSION['role_usuario'] === 'paciente' && $_SESSION['id_usuario'] != $diag['paciente_id'])
        ) {
            $_SESSION['mensagem'] = "Acesso negado. Você não tem permissão para ver este documento.";
            header("Location: login.php"); 
            exit;
        }
        // =========================================================================

        return [
            'diag' => $diag
        ];
    }

    /**
     * Ação: Processa o formulário de envio (POST)
     */
    public function criar($post) {
        // TRAVA DE SEGURANÇA ORIGINAL (Garante que apenas médicos e admins criem registros)
        if ($_SESSION['role_usuario'] !== 'medico' && $_SESSION['role_usuario'] !== 'admin') {
            header("Location: login.php"); 
            exit;
        }

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
        
        header("Location: ../view/atendimento-hub.php?triagem_id=" . $dados['triagem_id']);
        exit;
    }
}

// Instanciação do Controller
$controller = new DiagnosticoController($conexao);

// ==========================================================
// ROTEAMENTO DE AÇÕES (DETERMINA O FLUXO DA APLICAÇÃO)
// ==========================================================

// Se a View estiver requisitando renderização (Acesso via GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // Rota: Carregamento da tela de Cadastro (diagnostico-create.php)
    if (isset($_GET['triagem_id'])) {
        $dadosTela = $controller->carregarTelaCadastro($_GET);
        $triagem_id  = $dadosTela['triagem_id'];
        $paciente_id = $dadosTela['paciente_id'];
        $p           = $dadosTela['p'];
    }
    
    // Rota: Carregamento da tela de Visualização (diagnostico-view.php)
    if (isset($_GET['id'])) {
        $dadosVisualizacao = $controller->visualizar($_GET);
        $diag = $dadosVisualizacao['diag'];
    }
}

// Se a View estiver enviando o formulário para salvar (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_salvar_diagnostico'])) {
    $controller->criar($_POST);
}