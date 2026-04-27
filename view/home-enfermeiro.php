<?php
session_start();
require '../Model/conexao.php';

// Proteção: Só enfermeiros entram aqui
if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'enfermeiro') {
    header("Location: login.php");
    exit;
}

// Consultas rápidas para o Dashboard
$data_hoje = date('Y-m-d');
$id_enfermeiro = $_SESSION['id_usuario'];

// 1. Total de triagens que ESTE enfermeiro fez hoje
$sql_hoje = "SELECT COUNT(id) as total FROM triagens WHERE enfermeiro_id = '$id_enfermeiro' AND DATE(data_hora) = '$data_hoje'";
$res_hoje = mysqli_query($conexao, $sql_hoje);
$total_hoje = mysqli_fetch_assoc($res_hoje)['total'];

// 2. Buscar nome de quem está logado
$nome_user = explode(" ", $_SESSION['nome_usuario'])[0]; 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Enfermagem - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        .card-menu {
            transition: transform 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .card-menu:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .icon-box {
            font-size: 3rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="bg-light">

    <?php include('navbar.php'); ?>
    <div class="container py-4">
        <?php include('mensagem.php'); ?>
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold">Olá, Enfermeiro(a) <?php echo $nome_user; ?>! 👋</h2>
                <p class="text-muted">Bem-vindo ao seu painel de controle clínico.</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white h-100 p-3 text-center">
                    <h5>Suas Triagens Hoje</h5>
                    <h1 class="display-4 fw-bold"><?php echo $total_hoje; ?></h1>
                    <small>Pacientes triados por você hoje</small>
                </div>
            </div>
            
            <div class="col-md-4">
                <a href="painel-enfermagem.php" class="card h-100 p-4 text-center card-menu border-0 shadow-sm">
                    <div class="icon-box text-primary">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <h5>Ver Fila de Espera</h5>
                    <p class="small text-muted">Acompanhe quem já foi triado e aguarda o médico.</p>
                </a>
            </div>

            <div class="col-md-4">
                <a href="triagem-create.php" class="card h-100 p-4 text-center card-menu border-0 shadow-sm bg-success text-white">
                    <div class="icon-box">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <h5>Iniciar Nova Triagem</h5>
                    <p class="small opacity-75">Coletar sinais vitais de um novo paciente.</p>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <a href="lista-de-usuarios.php" class="card p-3 d-flex flex-row align-items-center card-menu border-0 shadow-sm h-100">
                    <div class="icon-box text-secondary me-3 mb-0">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold">Gerenciar Pacientes</h6>
                        <p class="small text-muted mb-0">Cadastrar, editar ou localizar prontuários.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <div class="card p-3 d-flex flex-row align-items-center border-0 shadow-sm h-100 opacity-75 bg-light">
                    <div class="icon-box text-warning me-3 mb-0">
                        <i class="bi bi-journal-medical" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold">Evolução Clínica (Em breve)</h6>
                        <p class="small text-muted mb-0">Notas sobre procedimentos e administração de medicação.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>