<?php
session_start();
// Verifica se está logado e se é ADMIN
if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'admin') {
    $_SESSION['mensagem'] = "Acesso restrito a administradores.";
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Painel Administrativo - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h2 class="border-bottom pb-2">Painel de Gestão Institucional</h2>
            </div>
            
            <!-- Card: Cadastrar Profissional -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge-fill text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Cadastrar Funcionário</h5>
                        <p class="card-text text-muted">Registro rigoroso de Médicos (CRM) e Enfermeiros (COREN).</p>
                        <a href="admin-cadastrar-profissional.php" class="btn btn-primary w-100">Acessar Cadastro</a>
                    </div>
                </div>
            </div>

            <!-- Card: Gerenciar Usuários -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill text-success" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Listar Todos os Usuários</h5>
                        <p class="card-text text-muted">Visualizar, editar ou suspender acessos de pacientes e funcionários.</p>
                        <a href="lista-de-usuarios.php" class="btn btn-success w-100">Gerenciar Lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>