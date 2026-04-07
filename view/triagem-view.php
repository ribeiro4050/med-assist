<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['logado'])) { header("Location: login.php"); exit; }

    require_once '../Model/conexao.php';
    require_once '../Model/EnfermagemService.php';

    $id = $_GET['id'] ?? null;
    if (!$id) { header("Location: painel-enfermagem.php"); exit; }

    $enfermagemService = new EnfermagemService($conexao);
    $t = $enfermagemService->buscarPorId($id);

    if (!$t) { echo "Triagem não encontrada."; exit; }

    // 1. Cálculo da idade em tempo real
    $nascimento = new DateTime($t['data_nascimento']);
    $hoje = new DateTime();
    $idade = $hoje->diff($nascimento)->y;

    // 2. Cálculo do IMC
    $imc = 0;
    $imc_texto = "N/A";
    $imc_cor = "text-muted";
    if ($t['peso'] > 0 && $t['altura'] > 0) {
        $imc = $t['peso'] / ($t['altura'] * $t['altura']);
        if ($imc < 18.5) { $imc_texto = "Abaixo do peso"; $imc_cor = "text-warning"; }
        elseif ($imc < 24.9) { $imc_texto = "Peso normal"; $imc_cor = "text-success"; }
        elseif ($imc < 29.9) { $imc_texto = "Sobrepeso"; $imc_cor = "text-warning"; }
        else { $imc_texto = "Obesidade"; $imc_cor = "text-danger"; }
    }

    // --- FUNÇÕES DE VALIDAÇÃO CLÍNICA ---
    
    // Atualizado para receber sistólica e diastólica separadamente
    function validarPressao($sis, $dia) {
        if ($sis >= 180 || $dia >= 110) return ['msg' => 'Crise Hipertensiva', 'cor' => 'text-danger fw-bold'];
        if ($sis >= 160 || $dia >= 100) return ['msg' => 'Hipertensão Estágio 2', 'cor' => 'text-danger fw-bold'];
        if ($sis >= 140 || $dia >= 90) return ['msg' => 'Hipertensão Estágio 1', 'cor' => 'text-warning fw-bold'];
        if ($sis >= 120 || $dia >= 80) return ['msg' => 'Pré-Hipertensão', 'cor' => 'text-warning fw-bold'];
        if ($sis < 90 || $dia < 60) return ['msg' => 'Hipotensão', 'cor' => 'text-danger fw-bold'];
        return ['msg' => 'Normal', 'cor' => 'text-success'];
    }

    function validarTemperatura($temp) {
        if ($temp < 35) return ['msg' => 'Hipotermia', 'cor' => 'text-danger fw-bold'];
        if ($temp >= 37.3 && $temp < 37.8) return ['msg' => 'Estado Febril', 'cor' => 'text-warning fw-bold'];
        if ($temp >= 37.8 && $temp < 39.0) return ['msg' => 'Febre', 'cor' => 'text-danger fw-bold'];
        if ($temp >= 39.0) return ['msg' => 'Febre', 'cor' => 'text-danger fw-bold'];
        return ['msg' => 'Normal', 'cor' => 'text-success'];
    }

    function validarSaturacao($sat) {
        if ($sat < 85) return ['msg' => 'Hipoxemia Grave', 'cor' => 'text-danger fw-bold'];
        if ($sat >= 85 && $sat < 90) return ['msg' => 'Hipoxemia Moderada', 'cor' => 'text-warning fw-bold'];
        if ($sat >= 90 && $sat < 95) return ['msg' => 'Hipoxemia Leve', 'cor' => 'text-warning fw-bold'];
        return ['msg' => 'Normal', 'cor' => 'text-success'];
    }

    function validarFrequencia($fc) {
        if ($fc < 60) return ['msg' => 'Bradicardia', 'cor' => 'text-danger fw-bold'];
        if ($fc > 100) return ['msg' => 'Taquicardia', 'cor' => 'text-danger fw-bold'];
        return ['msg' => 'Normal', 'cor' => 'text-success'];
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Triagem - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card-ficha { border-top: 12px solid; }
        .border-Azul { border-color: #0dcaf0; }
        .border-Verde { border-color: #198754; }
        .border-Amarelo { border-color: #ffc107; }
        .border-Laranja { border-color: #fd7e14; }
        .border-Vermelho { border-color: #dc3545; }
        
        .bg-Laranja { background-color: #fd7e14 !important; color: white; }
        .label-result { font-size: 0.75rem; display: block; margin-top: 5px; }
        
        .stat-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media print {
            .no-print { display: none; }
            body { background-color: white !important; padding: 0; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card shadow-sm card-ficha border-<?= $t['classificacao_risco']; ?>">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0 text-secondary">
                            <i class="fas fa-file-medical-alt me-2 text-primary"></i>Ficha de Triagem #<?= $t['id']; ?>
                        </h4>
                        <span class="badge rounded-pill p-2 px-4 bg-<?= ($t['classificacao_risco'] == 'Laranja') ? 'Laranja' : (($t['classificacao_risco'] == 'Azul') ? 'info' : (($t['classificacao_risco'] == 'Verde') ? 'success' : (($t['classificacao_risco'] == 'Amarelo') ? 'warning' : 'danger'))); ?>">
                            PRIORIDADE: <?= strtoupper($t['classificacao_risco']); ?>
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="text-muted small fw-bold text-uppercase">Paciente</label>
                                <h5 class="fw-bold text-primary"><?= $t['paciente_nome']; ?></h5>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <label class="text-muted small fw-bold text-uppercase">Idade</label>
                                <h5><?= $idade; ?> anos</h5>
                            </div>
                        </div>

                        <hr>

                        <div class="row row-cols-1 row-cols-md-5 g-3 text-center mb-4">
                            <?php $v_pres = validarPressao($t['pressao_sistolica'], $t['pressao_diastolica']); ?>
                            <div class="col">
                                <div class="p-3 border rounded bg-white stat-card">
                                    <i class="fas fa-heartbeat text-danger mb-2"></i>
                                    <div class="small text-muted">Pressão (mmHg)</div>
                                    <div class="fw-bold">
                                        <?= $t['pressao_sistolica'] / 10; ?> / <?= $t['pressao_diastolica'] / 10; ?>
                                    </div>
                                    <span class="label-result <?= $v_pres['cor']; ?>"><?= $v_pres['msg']; ?></span>
                                </div>
                            </div>
                            
                            <?php $v_temp = validarTemperatura($t['temperatura']); ?>
                            <div class="col">
                                <div class="p-3 border rounded bg-white stat-card">
                                    <i class="fas fa-thermometer-half text-warning mb-2"></i>
                                    <div class="small text-muted">Temperatura</div>
                                    <div class="fw-bold"><?= $t['temperatura']; ?>°C</div>
                                    <span class="label-result <?= $v_temp['cor']; ?>"><?= $v_temp['msg']; ?></span>
                                </div>
                            </div>

                            <?php $v_fc = validarFrequencia($t['frequencia_cardiaca']); ?>
                            <div class="col">
                                <div class="p-3 border rounded bg-white stat-card">
                                    <i class="fas fa-wave-square text-secondary mb-2"></i>
                                    <div class="small text-muted">F. Cardíaca</div>
                                    <div class="fw-bold"><?= $t['frequencia_cardiaca']; ?></div>
                                    <span class="label-result <?= $v_fc['cor']; ?>"><?= $v_fc['msg']; ?></span>
                                </div>
                            </div>

                            <?php $v_sat = validarSaturacao($t['saturacao']); ?>
                            <div class="col">
                                <div class="p-3 border rounded bg-white stat-card">
                                    <i class="fas fa-fingerprint text-primary mb-2"></i>
                                    <div class="small text-muted">Saturação</div>
                                    <div class="fw-bold"><?= $t['saturacao']; ?>%</div>
                                    <span class="label-result <?= $v_sat['cor']; ?>"><?= $v_sat['msg']; ?></span>
                                </div>
                            </div>

                            <div class="col">
                                <div class="p-3 border border-primary rounded bg-light stat-card">
                                    <i class="fas fa-calculator text-primary mb-2"></i>
                                    <div class="small text-muted">IMC (<?= $t['peso']; ?>kg/<?= $t['altura']; ?>m)</div>
                                    <div class="fw-bold"><?= number_format($imc, 1); ?></div>
                                    <span class="label-result <?= $imc_cor; ?> fw-bold"><?= $imc_texto; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fas fa-comment-medical text-primary me-2"></i>Queixa Principal</label>
                            <div class="p-3 bg-light border rounded" style="min-height: 80px;">
                                <?= nl2br($t['queixa_principal']); ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 p-3 bg-light rounded">
                            <div class="text-muted small">
                                <i class="fas fa-user-md me-1 text-primary"></i> Enf: <strong><?= $t['enfermeiro_nome']; ?></strong><br>
                                <i class="fas fa-clock me-1 text-primary"></i> Data: 
                                <strong><?= date('d/m/Y \à\s H:i', strtotime($t['data_hora'])); ?></strong>
                            </div>
                            <div class="no-print">
                                <a href="painel-enfermagem.php" class="btn btn-secondary me-2">Voltar</a>
                                <button onclick="window.print()" class="btn btn-dark"><i class="fas fa-print me-1"></i> Imprimir</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>