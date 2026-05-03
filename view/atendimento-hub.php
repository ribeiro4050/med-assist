<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'medico') {
        header("Location: login.php"); exit;
    }

    require_once '../Model/conexao.php';
    /** @var mysqli $conexao */
    
    $triagem_id = $_GET['triagem_id'] ?? '';
    if (empty($triagem_id)) { header("Location: painel-medico.php"); exit; }

    $sql = "SELECT t.*, u.nome, u.data_nascimento FROM triagens t 
            JOIN usuarios u ON t.paciente_id = u.id 
            WHERE t.id = '$triagem_id'";
    $res = mysqli_query($conexao, $sql);
    $t = mysqli_fetch_assoc($res);

    if (!$t) { header("Location: painel-medico.php"); exit; }

    // --- BUSCAR GUIA DE EXAME NA TABELA guia_exames GERADA HOJE ---
    $sql_exame = "SELECT id FROM guia_exames 
                  WHERE triagem_id = '$triagem_id' 
                  AND DATE(data_solicitacao) = CURDATE() 
                  ORDER BY data_solicitacao DESC LIMIT 1";
    $res_exame = mysqli_query($conexao, $sql_exame);
    $exame_recente = mysqli_fetch_assoc($res_exame);

    // --- FUNÇÕES DE VALIDAÇÃO ---
    function validarTemperatura($temp) {
        if ($temp < 35) return ['msg' => 'Hipotermia', 'class' => 'text-primary'];
        if ($temp >= 37.3 && $temp < 37.8) return ['msg' => 'Estado Febril', 'class' => 'text-warning fw-bold'];
        if ($temp >= 37.8 && $temp < 39.0) return ['msg' => 'Febre', 'class' => 'text-danger fw-bold'];
        if ($temp >= 39.0) return ['msg' => 'Febre Alta', 'class' => 'text-danger fw-bold'];
        return ['msg' => 'Normal', 'class' => 'text-success'];
    }

    function validarFrequencia($freq) {
        if ($freq < 60) return ['msg' => 'Bradicardia', 'class' => 'text-danger fw-bold'];
        if ($freq > 100) return ['msg' => 'Taquicardia', 'class' => 'text-danger fw-bold'];
        return ['msg' => 'Normal', 'class' => 'text-success'];
    }

    function validarSaturacao($sat) {
        if ($sat < 85) return ['msg' => 'Hipoxemia Grave', 'class' => 'text-danger fw-bold'];
        if ($sat >= 85 && $sat < 90) return ['msg' => 'Hipoxemia Moderada', 'class' => 'text-warning fw-bold'];
        if ($sat >= 90 && $sat < 95) return ['msg' => 'Hipoxemia Leve', 'class' => 'text-warning fw-bold'];
        return ['msg' => 'Normal', 'class' => 'text-success'];
    }

    function validarPressao($sis, $dia) {
        if ($sis >= 180 || $dia >= 110) return ['msg' => 'Crise Hipertensiva', 'class' => 'text-danger fw-bold'];
        if ($sis >= 160 || $dia >= 100) return ['msg' => 'Hipertensão Estágio 2', 'class' => 'text-danger fw-bold'];
        if ($sis >= 140 || $dia >= 90) return ['msg' => 'Hipertensão Estágio 1', 'class' => 'text-warning fw-bold'];
        if ($sis >= 120 || $dia >= 80) return ['msg' => 'Pré-Hipertensão', 'class' => 'text-warning fw-bold'];
        if ($sis < 90 || $dia < 60) return ['msg' => 'Hipotensão', 'class' => 'text-danger fw-bold'];
        return ['msg' => 'Normal', 'class' => 'text-success'];
    }

    function calcularIMC($peso, $altura) {
        if ($peso <= 0 || $altura <= 0) return ['valor' => '0', 'msg' => 'N/A', 'class' => 'text-muted'];
        $imc = $peso / ($altura * $altura);
        $res = ['valor' => number_format($imc, 1)];
        if ($imc < 18.5) { $res['msg'] = 'Abaixo do peso'; $res['class'] = 'text-warning'; }
        elseif ($imc < 24.9) { $res['msg'] = 'Normal'; $res['class'] = 'text-success'; }
        elseif ($imc < 29.9) { $res['msg'] = 'Sobrepeso'; $res['class'] = 'text-warning'; }
        else { $res['msg'] = 'Obesidade'; $res['class'] = 'text-danger'; }
        return $res;
    }

    // Atribuição das funções às variáveis para usar no HTML
    $v_temp  = validarTemperatura($t['temperatura']);
    $v_freq  = validarFrequencia($t['frequencia_cardiaca']);
    $v_sat   = validarSaturacao($t['saturacao']);
    $v_press = validarPressao($t['pressao_sistolica'], $t['pressao_diastolica']);
    $v_imc   = calcularIMC($t['peso'], $t['altura']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Atendimento - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card-atendimento { transition: all 0.3s; text-decoration: none; border-radius: 15px; border: none; }
        .card-atendimento:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .border-indicador { border-left: 5px solid !important; }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-4">
        <?php include('mensagem.php'); ?>

        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h2 class="h4 text-secondary">Atendimento: <span class="text-dark"><?= htmlspecialchars($t['nome']) ?></span></h2>
            <span class="badge bg-primary">ID Triagem: #<?= $triagem_id ?></span>
        </div>

        <?php if ($exame_recente): ?>
            <div class="alert alert-success d-flex justify-content-between align-items-center shadow-sm mb-4 border-0 border-start border-success border-4">
                <span><i class="fas fa-file-medical me-2"></i> Existem guias de exame geradas para este atendimento hoje.</span>
                <a href="guia-exame-view.php?id=<?= $exame_recente['id'] ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-eye me-1"></i> Visualizar Última Guia
                </a>
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4 text-center">
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-primary">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-heartbeat"></i> Pressão</small>
                        <h5 class="mb-0 text-dark"><?= $t['pressao_sistolica'] ?>/<?= $t['pressao_diastolica'] ?> <small>mmHg</small></h5>
                        <small class="<?= $v_press['class'] ?> fw-bold"><?= $v_press['msg'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-warning">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-thermometer-half"></i> Temp.</small>
                        <h5 class="mb-0 text-dark"><?= $t['temperatura'] ?>°C</h5>
                        <small class="<?= $v_temp['class'] ?> fw-bold"><?= $v_temp['msg'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-danger">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-pulse"></i> Freq. Card.</small>
                        <h5 class="mb-0 text-dark"><?= $t['frequencia_cardiaca'] ?> <small>bpm</small></h5>
                        <small class="<?= $v_freq['class'] ?> fw-bold"><?= $v_freq['msg'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-success">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-lungs"></i> Saturação</small>
                        <h5 class="mb-0 text-dark"><?= $t['saturacao'] ?>%</h5>
                        <small class="<?= $v_sat['class'] ?> fw-bold"><?= $v_sat['msg'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-info">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-weight"></i> IMC</small>
                        <h5 class="mb-0 text-dark"><?= $v_imc['valor'] ?></h5>
                        <small class="<?= $v_imc['class'] ?> fw-bold"><?= $v_imc['msg'] ?></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body">
                <h6 class="card-title fw-bold text-primary"><i class="fas fa-comment-medical me-2"></i>Queixa Principal:</h6>
                <p class="card-text bg-light p-3 rounded border">"<?= nl2br(htmlspecialchars($t['queixa_principal'])) ?>"</p>
            </div>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-6">
                <a href="receita-create.php?triagem_id=<?= $triagem_id ?>&paciente_id=<?= $t['paciente_id'] ?>" 
                   class="card card-atendimento h-100 shadow bg-primary text-white p-4">
                    <i class="fas fa-pills fa-3x mb-3"></i>
                    <h5>Prescrever Receita</h5>
                </a>
            </div>
            <div class="col-md-6">
                <a href="guia-exame-create.php?triagem_id=<?= $triagem_id ?>&paciente_id=<?= $t['paciente_id'] ?>" 
                   class="card card-atendimento h-100 shadow bg-info text-white p-4">
                    <i class="fas fa-microscope fa-3x mb-3"></i>
                    <h5>Solicitar Guia de Exame</h5>
                </a>
            </div>
        </div>

        <!-- ÁREA DE FINALIZAÇÃO COM TRAVA DE SEGURANÇA -->
        <div class="mt-5 text-center pt-4 border-top">
            <form action="../controller/ExameController.php" method="POST" id="formFinalizar">
                <input type="hidden" name="triagem_id" value="<?= $triagem_id ?>">
                
                <div class="mb-3 d-flex justify-content-center">
                    <div class="form-check text-start p-3 border rounded bg-white shadow-sm" style="max-width: 450px;">
                        <input class="form-check-input ms-0 me-2" type="checkbox" id="checkFinalizar" name="confirmacao_prescricao">
                        <label class="form-check-label fw-bold text-secondary" for="checkFinalizar">
                            Confirmo que finalizei todas as prescrições e exames necessários para este atendimento.
                        </label>
                    </div>
                </div>

                <button type="submit" name="concluir_atendimento" id="btnFinalizar" class="btn btn-danger btn-lg px-5 rounded-pill shadow" disabled>
                    <i class="fas fa-check-circle me-2"></i> Finalizar Atendimento
                </button>
            </form>
        </div>
    </div>

    <script>
        // Habilita o botão apenas se o checkbox estiver marcado
        const checkbox = document.getElementById('checkFinalizar');
        const btn = document.getElementById('btnFinalizar');
        const form = document.getElementById('formFinalizar');

        checkbox.addEventListener('change', function() {
            btn.disabled = !this.checked;
        });

        // Janela de confirmação antes de enviar
        form.onsubmit = function() {
            return confirm("ATENÇÃO: Você tem certeza que deseja finalizar? \n\nApós a confirmação, o atendimento será encerrado e as informações não poderão mais ser alteradas.");
        };
    </script>
</body>
</html>