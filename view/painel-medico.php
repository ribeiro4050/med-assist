<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Proteção de Acesso: Só médicos e admins
    if (!isset($_SESSION['logado']) || ($_SESSION['role_usuario'] !== 'medico' && $_SESSION['role_usuario'] !== 'admin')) {
        header("Location: login.php"); 
        exit;
    }

    require_once '../Model/conexao.php';
    require_once '../Model/MedicoService.php';

    $medicoService = new MedicoService($conexao);
    $fila = $medicoService->listarFilaEspera();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Médico - MedAssist</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .card-paciente { border-left: 5px solid #dee2e6; transition: 0.3s; }
        .border-Vermelho { border-left-color: #dc3545 !important; }
        .border-Laranja { border-left-color: #fd7e14 !important; }
        .border-Amarelo { border-left-color: #ffc107 !important; }
        .border-Verde { border-left-color: #198754 !important; }
        .border-Azul { border-left-color: #0dcaf0 !important; }
        .bg-laranja { background-color: #fd7e14 !important; color: white !important; }
        .badge-prioridade { width: 120px; display: inline-block; text-align: center; padding: 8px 0; font-weight: bold; }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-4">
        <?php include('mensagem.php'); ?>

        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold"><i class="fas fa-user-md me-2 text-primary"></i>Fila de Atendimento</h2>
            </div>
            <a href="painel-internacao.php" class="btn btn-outline-primary shadow-sm mb-3">
                <i class="fas fa-bed me-2"></i> Ver Pacientes Internados
            </a>
            <div class="col-md-4 text-end">
                <div class="p-3 bg-white shadow-sm rounded border">
                    <span class="h4 d-block mb-0 text-primary">
                        <?= ($fila instanceof mysqli_result) ? mysqli_num_rows($fila) : '0'; ?>
                    </span>
                    <small class="text-uppercase text-muted fw-bold">Pacientes em espera</small>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Paciente</th>
                                <th>Sinais Vitais</th>
                                <th>Prioridade</th>
                                <th>Queixa</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($fila && mysqli_num_rows($fila) > 0): ?>
                                <?php while($p = mysqli_fetch_assoc($fila)): 
                                    $cor = $p['classificacao_risco'];
                                    $classe_cor = match($cor) {
                                        'Vermelho' => 'bg-danger',
                                        'Laranja'  => 'bg-laranja',
                                        'Amarelo'  => 'bg-warning text-dark',
                                        'Verde'    => 'bg-success',
                                        'Azul'     => 'bg-info text-dark',
                                        default    => 'bg-secondary',
                                    };
                                ?>
                                    <tr class="card-paciente border-<?= $cor; ?>">
                                        <td class="ps-4">
                                            <span class="fw-bold d-block text-dark"><?= $p['paciente_nome']; ?></span>
                                            <small class="text-muted">ID: #<?= $p['paciente_id']; ?></small>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <i class="fas fa-heartbeat text-danger"></i> <?= $p['pressao_sistolica'] ?>/<?= $p['pressao_diastolica'] ?> PA<br>
                                                <i class="fas fa-thermometer-half text-warning"></i> <?= $p['temperatura'] ?>°C
                                            </div>
                                        </td>
                                        <td><span class="badge <?= $classe_cor; ?> badge-prioridade"><?= strtoupper($cor); ?></span></td>
                                        <td><small class="text-muted text-truncate d-inline-block" style="max-width: 150px;"><?= $p['queixa_principal']; ?></small></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="triagem-view.php?id=<?= $p['triagem_id']; ?>" 
                                                   class="btn btn-outline-secondary btn-sm rounded-pill" 
                                                   title="Ver Triagem Completa">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="../controller/AtendimentoHubController.php?paciente_id=<?= $p['paciente_id']; ?>&triagem_id=<?= $p['triagem_id']; ?>" 
                                                class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm">
                                                    <i class="fas fa-stethoscope me-1"></i> Atender
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <p>Nenhum paciente aguardando atendimento.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>