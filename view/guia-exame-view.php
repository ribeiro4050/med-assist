<?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require '../Model/conexao.php';

    // 1. Verifica se o ID da guia foi passado
    if(!isset($_GET['id'])) {
        header('Location: painel-medico.php');
        exit;
    }

    $guia_id = mysqli_real_escape_string($conexao, $_GET['id']);
    
    // 2. Consulta SQL completa para trazer dados da Guia, Paciente e Médico
    $sql = "SELECT g.*, p.nome AS nome_paciente, p.data_nascimento, \r
                   m.nome AS nome_medico, m.crm_registro\r
            FROM guia_exames g\r
            JOIN usuarios p ON g.paciente_id = p.id\r
            JOIN usuarios m ON g.medico_id = m.id\r
            WHERE g.id = $guia_id";
    
    $query = mysqli_query($conexao, $sql);
    $guia = mysqli_fetch_assoc($query);

    if(!$guia) {
        $_SESSION['mensagem'] = "Guia de exame não encontrada.";
        header('Location: painel-medico.php');
        exit;
    }

    // =========================================================================
    // VALIDAÇÃO DE SEGURANÇA SIMPLIFICADA (Anti-invasão / IDOR)
    // =========================================================================
    // Bloqueia se: Não estiver logado OU (Se for paciente E o ID logado for diferente do dono da guia)
    if (
        !isset($_SESSION['logado']) || 
        ($_SESSION['role_usuario'] === 'paciente' && $_SESSION['id_usuario'] != $guia['paciente_id'])
    ) {
        $_SESSION['mensagem'] = "Acesso negado. Você não tem permissão para ver este documento.";
        header("Location: login.php"); 
        exit;
    }
    // =========================================================================
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Guia de Exame #<?= $guia['id'] ?> - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { background-color: white !important; }
            /* Correção do CSS para sumir com o sublinhado amarelo do VS Code */
            .card { border: none !important; box-shadow: none !important; }
        }
        .header-logo { font-size: 1.5rem; font-weight: bold; color: #0d6efd; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="no-print mb-4">
            <a href="javascript:history.go(-1)" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Imprimir Guia</button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-5">
                <div class="row border-bottom pb-3 mb-4">
                    <div class="col-md-6">
                        <span class="header-logo">MedAssist</span>
                        <p class="mb-0 text-muted">Unidade de Saúde Integrada</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p class="mb-0 fw-bold">CNES: 04206769</p>
                        <p class="mb-0 small text-muted">Emissão: <?= date('d/m/Y H:i', strtotime($guia['data_solicitacao'])) ?></p>
                    </div>
                </div>

                <h3 class="text-center mb-4">GUIA DE SOLICITAÇÃO DE EXAMES</h3>

                <div class="row mb-4">
                    <div class="col-6">
                        <label class="text-muted small d-block">PACIENTE</label>
                        <span class="fw-bold"><?= htmlspecialchars($guia['nome_paciente']) ?></span>
                    </div>
                    <div class="col-3 text-center">
                        <label class="text-muted small d-block">CARÁTER</label>
                        <span class="badge bg-secondary"><?= strtoupper($guia['carater_solicitacao']) ?></span>
                    </div>
                    <div class="col-3 text-end">
                        <label class="text-muted small d-block">CID-10</label>
                        <span><?= $guia['cid_10'] ?: '---' ?></span>
                    </div>
                </div>

                <div class="border p-4 mb-4 bg-white rounded">
                    <label class="fw-bold mb-2 text-primary">EXAMES SOLICITADOS:</label>
                    <p class="fs-5" style="white-space: pre-wrap;"><?= htmlspecialchars($guia['descricao_exames']) ?></p>
                    
                    <?php if($guia['indicacao_clinica']): ?>
                        <hr>
                        <label class="fw-bold mb-1 small text-muted">INDICAÇÃO CLÍNICA:</label>
                        <p class="small italic text-muted"><?= htmlspecialchars($guia['indicacao_clinica']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="row mt-5 pt-3 border-top align-items-end">
                    <div class="col-md-3 mx-auto text-center">
                        <?php 
                            // Remove espaços em branco ou quebras de linha invisíveis do token original
                            $token = trim($guia['token_assinatura']);
                        ?>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($token) ?>" alt="QR Code" class="img-fluid border p-1 shadow-sm mb-2" style="width: 100px; height: 100px;">
                    </div>
                    
                    <div class="col-md-9 text-end">
                        <div class="mb-3">
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($guia['nome_medico']) ?></p>
                            <p class="text-muted small mb-0">Médico Responsável | CRM: <?= $guia['crm_registro'] ?></p>
                        </div>
                        <div class="bg-light p-2 rounded d-inline-block text-start" style="max-width: 100%;">
                            <small class="text-muted d-block" style="font-size: 0.65rem;">VALIDAÇÃO DIGITAL (SHA-256):</small>
                            <code class="text-muted" style="font-size: 0.7rem;"><?= $token ?></code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>