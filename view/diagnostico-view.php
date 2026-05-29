<?php 
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    // Carrega o controlador para obter os dados processados do laudo ($diag)
    require_once '../controller/DiagnosticoController.php'; 

    /**
     * @var array $diag
     * @var string $triagem_id
     * @var string $token_limpo
     */

    // =========================================================================
    // VALIDAÇÃO DE SEGURANÇA SIMPLIFICADA (Anti-invasão / IDOR)
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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Laudo de Diagnóstico #<?= $diag['id'] ?> - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { background-color: white !important; }
            .card { border: none !important; box-shadow: none !important; }
        }
        .header-logo { font-size: 1.5rem; font-weight: bold; color: #0d6efd; }
        .laudo-box { background-color: #f8f9fa; min-height: 200px; font-size: 1.1rem; line-height: 1.6; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="no-print mb-4">
            <a href="javascript:history.go(-1)" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Imprimir Laudo</button>
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
                        <p class="mb-0 small text-muted">Emissão: <?= date('d/m/Y H:i', strtotime($diag['data'])) ?></p>
                    </div>
                </div>

                <h3 class="text-center mb-4 text-uppercase fw-bold text-secondary">Laudo de Diagnóstico Médico</h3>

                <div class="row mb-4 bg-light p-3 rounded mx-1">
                    <div class="col-6">
                        <label class="text-muted small d-block">PACIENTE</label>
                        <span class="fw-bold fs-5"><?= htmlspecialchars($diag['paciente_nome']) ?></span>
                    </div>
                    <div class="col-6 text-end">
                        <label class="text-muted small d-block">CID-10</label>
                        <span class="badge bg-dark fs-6"><?= htmlspecialchars($diag['cid_10']) ?: '---' ?></span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="fw-bold mb-2 text-primary"><i class="bi bi-file-earmark-text"></i> DESCRIÇÃO DO DIAGNÓSTICO / ANAMNESE:</label>
                    <div class="laudo-box p-4 border rounded shadow-sm">
                        <?= nl2br(htmlspecialchars($diag['descricao'])) ?>
                    </div>
                </div>

                <div class="row mt-5 pt-3 border-top align-items-end">
                    <div class="col-md-3 mx-auto text-center">
                        <?php 
                            // Token SHA-256 gerado para fins de validação digital
                            $token_validacao = hash('sha256', $diag['id'] . $diag['cid_10'] . $diag['medico_nome'] . $diag['data']);
                            $token_limpo = trim($token_validacao);
                        ?>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($token_limpo) ?>" alt="QR Code" class="img-fluid border p-1 shadow-sm mb-2" style="width: 100px; height: 100px;">
                    </div>
                    
                    <div class="col-md-9 text-end">
                        <div class="mb-3">
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($diag['medico_nome']) ?></p>
                            <p class="text-muted small mb-0">Médico Responsável</p>
                        </div>
                        <div class="bg-light p-2 rounded d-inline-block text-start" style="max-width: 100%;">
                            <small class="text-muted d-block" style="font-size: 0.65rem;">VALIDAÇÃO DIGITAL (SHA-256):</small>
                            <code class="text-muted" style="font-size: 0.7rem;"><?= $token_limpo ?></code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light text-center py-3">
                <small class="text-muted">
                    Este documento é um registro eletrônico gerado pelo sistema MedAssist. 
                    ID de Autenticidade: <?= str_pad($diag['id'], 8, '0', STR_PAD_LEFT) ?>
                </small>
            </div>
        </div>
        
        <p class="text-center text-muted mt-4 small d-print-none">
            MedAssist &copy; <?= date('Y') ?> - Todos os direitos reservados.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>