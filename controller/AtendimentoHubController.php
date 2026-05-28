<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// (Mantendo a barreira de segurança como está por enquanto, conforme solicitado)
if (!isset($_SESSION['logado']) || ($_SESSION['role_usuario'] !== 'medico' && $_SESSION['role_usuario'] !== 'admin')) {
    header("Location: login.php"); 
    exit;
}

require_once '../Model/conexao.php';

$triagem_id = $_GET['triagem_id'] ?? '';
if (empty($triagem_id)) { 
    header("Location: painel-medico.php"); 
    exit; 
}

// Execução das Queries (Moveremos para o Service em um segundo momento, mantendo a arquitetura de transição)
$sql = "SELECT t.*, u.nome, u.data_nascimento FROM triagens t 
        JOIN usuarios u ON t.paciente_id = u.id 
        WHERE t.id = '$triagem_id'";
$res = mysqli_query($conexao, $sql);
$t = mysqli_fetch_assoc($res);

if (!$t) { 
    header("Location: painel-medico.php"); 
    exit; 
}

$sql_exame = "SELECT id FROM guia_exames 
              WHERE triagem_id = '$triagem_id' 
              AND DATE(data_solicitacao) = CURDATE() 
              ORDER BY data_solicitacao DESC LIMIT 1";
$res_exame = mysqli_query($conexao, $sql_exame);
$exame_recente = mysqli_fetch_assoc($res_exame);

$sql_receita = "SELECT id FROM receitas 
            WHERE paciente_id = '{$t['paciente_id']}' 
            AND DATE(data_prescricao) = CURDATE() 
            ORDER BY data_prescricao DESC LIMIT 1";
$res_receita = mysqli_query($conexao, $sql_receita);
$receita_recente = mysqli_fetch_assoc($res_receita);

$sql_diag_recente = "SELECT id, data FROM diagnostico 
                    WHERE triagem_id = '$triagem_id' 
                    AND DATE(data) = CURDATE() 
                    ORDER BY data DESC LIMIT 1";
$res_diag_recente = mysqli_query($conexao, $sql_diag_recente);
$diag_recente = mysqli_fetch_assoc($res_diag_recente);

$nascimento = new DateTime($t['data_nascimento']);
$hoje = new DateTime();
$idade = $hoje->diff($nascimento)->y;

// Funções Clínicas isoladas no escopo do Controller
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

$v_temp  = validarTemperatura($t['temperatura']);
$v_freq  = validarFrequencia($t['frequencia_cardiaca']);
$v_sat   = validarSaturacao($t['saturacao']);
$v_press = validarPressao($t['pressao_sistolica'], $t['pressao_diastolica']);
$v_imc   = calcularIMC($t['peso'], $t['altura']);