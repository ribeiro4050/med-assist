<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Proteção de Acesso
    if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] === 'paciente') {
        header("Location: login.php");
        exit;
    }

    // 2. Importações Necessárias
    require_once '../Model/conexao.php';
    require_once '../Model/EnfermagemService.php';
    include('mensagem.php');

    // 3. Inicialização do Serviço e Busca de Dados
    $enfermagemService = new EnfermagemService($conexao);
    $query = $enfermagemService->listarTriagensRecentes(15); // Buscando as últimas 15
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Enfermagem - MedAssist</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Estilização das bordas coloridas baseadas na classificação */
        .border-Azul { border-left: 8px solid #0dcaf0 !important; }
        .border-Verde { border-left: 8px solid #198754 !important; }
        .border-Amarelo { border-left: 8px solid #ffc107 !important; }
        .border-Laranja { border-left: 8px solid #fd7e14 !important; }
        .border-Vermelho { border-left: 8px solid #dc3545 !important; }

        /* Badges personalizadas para a tabela */
        .badge-manchester {
            width: 110px;
            padding: 8px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transition: 0.2s;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-heartbeat me-2"></i>MedAssist - Enfermagem
            </span>
            <div class="d-flex">
                <span class="text-white me-3 d-none d-md-inline">Olá, <?= $_SESSION['nome_usuario']; ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-secondary">Fila de Triagens Realizadas</h3>
                    <a href="triagem-create.php" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus-circle me-2"></i>Nova Triagem
                    </a>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Paciente</th>
                                        <th>Sinais Vitais (PA / T / SpO2)</th>
                                        <th>Classificação</th>
                                        <th>Data/Hora</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($query) > 0): ?>
                                        <?php while($triagem = mysqli_fetch_assoc($query)): 
                                            $cor = $triagem['classificacao_risco'];
                                            // Lógica simples para cor do badge (Bootstrap nativo + nossa cor laranja)
                                            $badge_bg = ($cor == 'Laranja') ? 'style="background-color: #fd7e14; color: white;"' : '';
                                            $badge_class = 'badge ';
                                            if($cor == 'Azul') $badge_class .= 'bg-info text-dark';
                                            if($cor == 'Verde') $badge_class .= 'bg-success';
                                            if($cor == 'Amarelo') $badge_class .= 'bg-warning text-dark';
                                            if($cor == 'Vermelho') $badge_class .= 'bg-danger';
                                        ?>
                                            <tr class="border-<?= $cor; ?>">
                                                <td class="ps-4">
                                                    <div class="fw-bold text-primary"><?= $triagem['paciente_nome']; ?></div>
                                                    <small class="text-muted">ID: #<?= $triagem['paciente_id']; ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-tachometer-alt me-1 text-secondary"></i> <?= $triagem['pressao_arterial']; ?>
                                                    </span>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-thermometer-half me-1 text-danger"></i> <?= $triagem['temperatura']; ?>°C
                                                    </span>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fas fa-fingerprint me-1 text-primary"></i> <?= $triagem['saturacao']; ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge-manchester <?= $badge_class; ?>" <?= $badge_bg; ?>>
                                                        <?= $cor; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="small"><?= date('d/m/Y', strtotime($triagem['data_criacao'] ?? 'now')); ?></div>
                                                    <div class="small text-muted"><?= date('H:i', strtotime($triagem['data_criacao'] ?? 'now')); ?></div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="triagem-view.php?id=<?= $triagem['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Ver Detalhes">
                                                        <i class="fas fa-search-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3"></i>
                                                <p>Nenhuma triagem realizada até o momento.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>