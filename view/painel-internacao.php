<?php
session_start();
require '../Model/conexao.php';

// 1. Verificação de Sessão
if (!isset($_SESSION['logado'])) {
    header("Location: login.php?erro=sessao_expirada");
    exit;
}

// 2. PERMISSÃO AMPLIADA: Aceita enfermeiro OU medico OU admin
$roles_permitidos = ['enfermeiro', 'medico', 'admin'];

if (!in_array($_SESSION['role_usuario'], $roles_permitidos)) {
    header("Location: login.php?erro=acesso_negado");
    exit;
}

// --- DETECÇÃO DA COLUNA DE ROLE ---
$check_columns = mysqli_query($conexao, "SHOW COLUMNS FROM usuarios LIKE 'role_usuario'");
$coluna_final = (mysqli_num_rows($check_columns) > 0) ? 'role_usuario' : 'role';

if ($coluna_final == 'role') {
    $check_role = mysqli_query($conexao, "SHOW COLUMNS FROM usuarios LIKE 'role'");
    if (mysqli_num_rows($check_role) == 0) {
        $coluna_final = 'tipo'; 
    }
}

$filtro_cpf = isset($_GET['busca_cpf']) ? mysqli_real_escape_string($conexao, $_GET['busca_cpf']) : '';

// 3. Query de Busca
$sql = "SELECT id, nome, cpf, data_nascimento FROM usuarios WHERE $coluna_final = 'paciente'";
if ($filtro_cpf != '') {
    $sql .= " AND cpf LIKE '%$filtro_cpf%'";
}

$query_pacientes = mysqli_query($conexao, $sql);

// 4. Lógica de Redirecionamento Dinâmico
// Define para qual página o usuário será enviado ao clicar no card
$destino_clique = ($_SESSION['role_usuario'] === 'medico') ? 'prontuario-medico.php' : 'gestao-paciente.php';
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel de Internação - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .card-paciente { transition: transform 0.2s; cursor: pointer; border-left: 5px solid #0d6efd; }
        .card-paciente:hover { transform: scale(1.02); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .bg-medico { border-left-color: #2776d1 !important; }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h3><i class="bi bi-hospital text-primary"></i> Painel de Internação</h3>
                <p class="text-muted">
                    Sessão atual: <span class="badge bg-secondary"><?= ucfirst($_SESSION['role_usuario']) ?></span>
                    <br>Selecione um paciente para gerenciar o prontuário.
                </p>
            </div>
            <div class="col-md-4">
                <form action="" method="GET" class="d-flex">
                    <input type="text" name="busca_cpf" class="form-control me-2" placeholder="Buscar por CPF..." value="<?= $filtro_cpf ?>">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <div class="row">
            <?php if($query_pacientes && mysqli_num_rows($query_pacientes) > 0): ?>
                <?php while($p = mysqli_fetch_assoc($query_pacientes)): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 card-paciente <?= ($_SESSION['role_usuario'] === 'medico') ? 'bg-medico' : '' ?>" 
                         onclick="location.href='<?= $destino_clique ?>?id=<?= $p['id'] ?>'">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-person-circle fs-1 text-secondary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-0 text-dark"><?= $p['nome'] ?></h5>
                                    <small class="text-muted">CPF: <?= $p['cpf'] ?? 'Não informado' ?></small><br>
                                    <small class="text-muted">Nasc: <?= date('d/m/Y', strtotime($p['data_nascimento'])) ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 text-end">
                            <span class="text-primary small">
                                <?= ($_SESSION['role_usuario'] === 'medico') ? 'Abrir Prontuário' : 'Gerenciar Medicamentos' ?> 
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center mt-5">
                    <i class="bi bi-person-exclamation fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Nenhum paciente internado encontrado.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>