<?php 
require_once '../controller/AuditoriaController.php'; 
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auditoria de Medicação | MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="admin-painel.php">Painel Admin</a></li>
                <li class="breadcrumb-item active">Auditoria de Medicação</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold text-dark"><i class="bi bi-clipboard2-pulse"></i> Auditoria de Medicação</h2>
                <p class="text-muted mb-0">Controle de conformidade das ministrações diárias.</p>
            </div>
            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir Relatório
            </button>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Paciente</th>
                                <th>Status de Hoje</th>
                                <th>Progresso da Escala</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($pacientes && mysqli_num_rows($pacientes) > 0): ?>
                                <?php while($p = mysqli_fetch_assoc($pacientes)): 
                                    $total = $p['total_medicamentos'];
                                    $aplicadas = $p['doses_aplicadas'];
                                    $percentual = ($total > 0) ? round(($aplicadas / $total) * 100) : 0;
                                    
                                    // Cores dinâmicas para a barra
                                    $bar_class = ($percentual == 100) ? 'bg-success' : ($percentual > 50 ? 'bg-primary' : 'bg-warning');
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($p['nome'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <span class="fw-bold d-block"><?= $p['nome'] ?></span>
                                                <small class="text-muted">CPF: <?= $p['cpf'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill <?= $percentual == 100 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark' ?> border">
                                            <?= $aplicadas ?> de <?= $total ?> medicamentos
                                        </span>
                                    </td>
                                    <td style="min-width: 200px;">
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar <?= $bar_class ?>" role="progressbar" style="width: <?= $percentual ?>%"></div>
                                            </div>
                                            <span class="ms-2 small fw-bold"><?= $percentual ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="historico-paciente-view.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="../controller/GerarPdfController.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" title="Gerar PDF">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Nenhum paciente com medicação ativa no momento.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>