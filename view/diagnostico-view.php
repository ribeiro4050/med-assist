<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    
    // Proteção de Acesso: Verifica se está logado
    if (!isset($_SESSION['logado'])) {
        header("Location: login.php"); exit;
    }

    require_once '../Model/conexao.php';

    // Captura o ID do diagnóstico (Padrão universal de documentos)
    $id = $_GET['id'] ?? '';

    if (empty($id)) {
        $_SESSION['mensagem'] = "ID do diagnóstico não fornecido.";
        header("Location: painel-medico.php"); exit;
    }

    // Busca os dados completos do diagnóstico, paciente e médico
    // Nota: A probabilidade foi removida conforme a regra de negócio do projeto
    $sql = "SELECT d.id, d.data, d.cid_10, d.descricao, 
                   u_pac.nome as paciente_nome, 
                   u_med.nome as medico_nome 
            FROM diagnostico d
            JOIN usuarios u_pac ON d.paciente_id = u_pac.id
            JOIN usuarios u_med ON d.medico_id = u_med.id
            WHERE d.id = '$id' LIMIT 1";
    
    $res = mysqli_query($conexao, $sql);
    $diag = mysqli_fetch_assoc($res);

    if (!$diag) {
        $_SESSION['mensagem'] = "Diagnóstico não encontrado no sistema.";
        header("Location: painel-medico.php"); exit;
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Laudo - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .d-print-none { display: none !important; }
            .card { border: none !important; shadow: none !important; }
            body { background-color: white !important; }
        }
        .laudo-box {
            min-height: 300px;
            white-space: pre-wrap;
            background-color: #fcfcfc;
            line-height: 1.6;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                
                <div class="d-flex justify-content-between mb-3 d-print-none">
                    <button onclick="window.history.back()" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </button>
                    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-print me-2"></i>Imprimir Laudo
                    </button>
                </div>

                <div class="card shadow border-0">
                    <div class="card-header bg-white border-bottom py-4 text-center">
                        <h2 class="text-primary fw-bold mb-0">MEDASSIST</h2>
                        <p class="text-muted small mb-0">Sistema de Auxílio ao Diagnóstico e Telemedicina</p>
                    </div>

                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <h4 class="fw-bold text-uppercase" style="letter-spacing: 2px;">Laudo de Diagnóstico Clínico</h4>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-7">
                                <label class="text-muted small d-block">PACIENTE</label>
                                <span class="h5 fw-bold"><?= htmlspecialchars($diag['paciente_nome']) ?></span>
                            </div>
                            <div class="col-sm-5 text-sm-end">
                                <label class="text-muted small d-block">DATA E HORA</label>
                                <span class="h6"><?= date('d/m/Y H:i', strtotime($diag['data'])) ?></span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small d-block">CLASSIFICAÇÃO INTERNACIONAL DE DOENÇAS (CID-10)</label>
                            <span class="badge bg-dark fs-6 px-3"><?= htmlspecialchars($diag['cid_10']) ?></span>
                        </div>

                        <div class="mb-5">
                            <label class="text-muted small d-block mb-2">DESCRIÇÃO DO DIAGNÓSTICO E EVOLUÇÃO</label>
                            <div class="laudo-box p-4 border rounded shadow-sm">
                                <?= nl2br(htmlspecialchars($diag['descricao'])) ?>
                            </div>
                        </div>

                        <div class="mt-5 pt-5">
                            <div class="text-center mx-auto" style="max-width: 350px;">
                                <div class="border-top border-dark mb-1"></div>
                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($diag['medico_nome']) ?></h6>
                                <p class="text-muted small">Médico Responsável</p>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>