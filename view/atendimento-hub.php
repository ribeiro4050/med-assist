<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'medico') {
        header("Location: login.php"); exit;
    }

    require_once '../Model/conexao.php';
    
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

    // --- BUSCAR RECEITA NA TABELA receitas GERADA HOJE ---
    $sql_receita = "SELECT id FROM receitas 
                WHERE paciente_id = '{$t['paciente_id']}' 
                AND DATE(data_prescricao) = CURDATE() 
                ORDER BY data_prescricao DESC LIMIT 1";
    $res_receita = mysqli_query($conexao, $sql_receita);
    $receita_recente = mysqli_fetch_assoc($res_receita);

    // --- BUSCAR DIAGNÓSTICO RECENTE NA TABELA diagnostico ---
    $sql_diag_recente = "SELECT id, data FROM diagnostico 
                        WHERE triagem_id = '$triagem_id' 
                        AND DATE(data) = CURDATE() 
                        ORDER BY data DESC LIMIT 1";
    $res_diag_recente = mysqli_query($conexao, $sql_diag_recente);
    $diag_recente = mysqli_fetch_assoc($res_diag_recente);

    // Cálculos de idade
    $nascimento = new DateTime($t['data_nascimento']);
    $hoje = new DateTime();
    $idade = $hoje->diff($nascimento)->y;

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
        
        <h3 class="mb-4 fw-bold text-dark"><i class="fas fa-th-large me-3 text-primary"></i>Ações Médicas</h3>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <a href="diagnostico-create.php?triagem_id=<?= $triagem_id ?>&paciente_id=<?= $t['paciente_id'] ?>" class="card card-atendimento h-100 shadow bg-dark text-white p-4">
                    <div class="card-body text-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-stethoscope fa-2x"></i>
                        </div>
                        <h4 class="fw-bold">Diagnóstico</h4>
                        <p class="mb-0 opacity-75">Registrar laudo final e CID-10</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="receita-create.php?paciente_id=<?= $t['paciente_id']; ?>&triagem_id=<?= $t['id']; ?>" class="card card-atendimento h-100 shadow bg-primary text-white p-4">
                    <div class="card-body text-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-pills fa-2x"></i>
                        </div>
                        <h4 class="fw-bold">Prescrever Receita</h4>
                        <p class="mb-0 opacity-75">Medicamentos e Orientações</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="guia-exame-create.php?triagem_id=<?= $triagem_id ?>&paciente_id=<?= $t['paciente_id'] ?>" class="card card-atendimento h-100 shadow bg-info text-white p-4">
                    <div class="card-body text-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex p-3 mb-3 text-info">
                            <i class="fas fa-microscope fa-2x text-white"></i>
                        </div>
                        <h4 class="fw-bold text-white">Solicitar Exames</h4>
                        <p class="mb-0 opacity-75 text-white">Laboratoriais e Imagem</p>
                    </div>
                </a>
            </div>
        </div>

        <?php if ($exame_recente || $receita_recente): ?>
        <div class="alert alert-secondary bg-white border-0 shadow-sm rounded-4 p-4 mb-5">
            <h5 class="fw-bold mb-3"><i class="fas fa-file-alt me-2 text-primary"></i>Documentos Gerados Agora</h5>
            <div class="d-flex gap-3">
                <?php if ($receita_recente): ?>
                    <a href="receita-view.php?id=<?= $receita_recente['id'] ?>" class="btn btn-outline-primary rounded-pill">
                        <i class="fas fa-print me-2"></i>Imprimir Receita
                    </a>
                <?php endif; ?>
                <?php if ($diag_recente): ?>
                    <a href="diagnostico-view.php?id=<?= $diag_recente['id'] ?>" class="btn btn-outline-secondary rounded-pill">
                        <i class="fas fa-print me-2"></i>Imprimir Diagnóstico
                    </a>
                <?php endif; ?>
                <?php if ($exame_recente): ?>
                    <a href="guia-exame-view.php?id=<?= $exame_recente['id'] ?>" class="btn btn-outline-info rounded-pill">
                        <i class="fas fa-print me-2"></i>Imprimir Guia de Exame
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$exame_recente && !$receita_recente && !$diag_recente): ?>
            <div class="text-center py-4">
                <p class="text-muted">Nenhum documento foi gerado para este atendimento ainda.</p>
            </div>
        <?php endif; ?>            

        <div class="text-center mt-5">
            <form action="../controller/ExameController.php" method="POST" id="formFinalizar">
                <input type="hidden" name="triagem_id" value="<?= $triagem_id ?>">
                
                <div class="d-flex justify-content-center mb-4">
                    <div class="form-check form-switch bg-white p-3 px-4 rounded-pill shadow-sm" style="max-width: 450px;">
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
            <div class="mt-4">
                <a href="painel-medico.php" class="text-decoration-none text-muted">
                    <i class="fas fa-arrow-left me-2"></i>Sair sem finalizar (O paciente continuará na fila)
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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