<?php
session_start();
require '../Model/conexao.php';
require_once '../Model/MedicoService.php';

// 1. Verificação de Sessão
if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'medico') {
    header("Location: login.php?erro=acesso_negado");
    exit;
}

// 2. Pega o ID do paciente
$id_paciente = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 3. Busca dados do paciente (Usando a mesma lógica de detecção de coluna do painel)
$check_columns = mysqli_query($conexao, "SHOW COLUMNS FROM usuarios LIKE 'role_usuario'");
$coluna_final = (mysqli_num_rows($check_columns) > 0) ? 'role_usuario' : 'role';
if ($coluna_final == 'role') {
    $check_role = mysqli_query($conexao, "SHOW COLUMNS FROM usuarios LIKE 'role'");
    if (mysqli_num_rows($check_role) == 0) $coluna_final = 'tipo';
}

$sql_p = "SELECT * FROM usuarios WHERE id = $id_paciente AND $coluna_final = 'paciente'";
$res_p = mysqli_query($conexao, $sql_p);
$p = mysqli_fetch_assoc($res_p);

if (!$p) {
    header('Location: painel-internacao.php?erro=paciente_nao_encontrado');
    exit;
}

// 4. Busca histórico de receitas/medicamentos já passados
$medicoService = new MedicoService($conexao);
$historico_medicamentos = $medicoService->buscarPrescricoesAtuais($id_paciente);

$triagem = $medicoService->buscarUltimaTriagem($id_paciente);
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prontuário: <?= $p['nome'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .perfil-header { background: #fff; padding: 20px 0; border-bottom: 1px solid #dee2e6; }
        .sidebar-info { background: #f8f9fa; border-right: 1px solid #dee2e6; height: 100%; }
        .btn-prescrever { background-color: #6610f2; color: white; }
        .btn-prescrever:hover { background-color: #520dc2; color: white; }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container mt-3">
            <?php include('mensagem.php'); ?>
    </div>

    <div class="perfil-header shadow-sm mb-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0"><?= $p['nome'] ?></h2>
                    <p class="text-muted mb-0">ID: #<?= $p['id'] ?> | CPF: <?= $p['cpf'] ?> | Nasc: <?= date('d/m/Y', strtotime($p['data_nascimento'])) ?></p>
                </div>
                <a href="painel-internacao.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary"><i class="bi bi-journal-medical"></i> Prescrições Vigentes</h5>
                        
                        <?php if ($triagem): ?>
                            <a href="atendimento-hub.php?triagem_id=<?= $triagem['id'] ?>" class="btn btn-prescrever btn-sm">
                                <i class="bi bi-plus-lg"></i> Nova Prescrição
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled title="Paciente sem triagem ativa">
                                <i class="bi bi-plus-lg"></i> Nova Prescrição
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Posologia</th>
                                        <th>Período (Início/Fim)</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($historico_medicamentos && mysqli_num_rows($historico_medicamentos) > 0): ?>
                                        <?php while($item = mysqli_fetch_assoc($historico_medicamentos)): ?>
                                        <tr>
                                            <td><strong><?= $item['medicamento_nome'] ?></strong></td>
                                            <td><?= $item['posologia'] ?></td>
                                            <td>
                                                <small>
                                                    i: <?= date('d/m/y', strtotime($item['data_inicio'])) ?><br>
                                                    f: <?= date('d/m/y', strtotime($item['data_fim'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php 
                                                $hoje = date('Y-m-d');
                                                if (!empty($item['justificativa_cancelamento'])) {
                                                    echo '<span class="badge bg-danger" title="'.$item['justificativa_cancelamento'].'">Cancelado</span>';
                                                } elseif ($hoje <= $item['data_fim']) {
                                                    echo '<span class="badge bg-success">Ativo</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">Encerrado</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (empty($item['justificativa_cancelamento']) && $hoje <= $item['data_fim']): ?>
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="abrirModalCancelamento(<?= $item['id'] ?>, '<?= $item['medicamento_nome'] ?>')">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-warning mb-4">
                    <div class="card-header bg-warning text-dark fw-bold">
                        <i class="bi bi-exclamation-triangle"></i> Sinais Vitais (Triagem)
                    </div>
                    <div class="card-body">
                        <?php if($triagem): ?>
                            <p class="mb-1"><strong>Pressão:</strong> <?= $triagem['pressao_sistolica'] ?>/<?= $triagem['pressao_diastolica'] ?> mmHg</p>
                            <p class="mb-1"><strong>Temperatura:</strong> <?= $triagem['temperatura'] ?>°C</p>
                            <p class="mb-1"><strong>Freq. Cardíaca:</strong> <?= $triagem['frequencia_cardiaca'] ?> bpm</p>
                            <p class="mb-1"><strong>Saturação:</strong> <?= $triagem['saturacao'] ?>%</p>
                            <hr>
                            <p class="small text-muted">Queixa Principal: <br><em>"<?= $triagem['queixa_principal'] ?>"</em></p>
                        <?php else: ?>
                            <p class="text-muted small">Nenhuma triagem recente encontrada.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCancelamento" tabindex="-1">
    <div class="modal-dialog">
        <form action="../controller/ReceitaController.php" method="POST" class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Interromper Medicamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p>Você está interrompendo o uso de: <strong id="nomeMedModal"></strong></p>
            <input type="hidden" name="item_id" id="item_id_cancelar">
            <input type="hidden" name="paciente_id" value="<?= $id_paciente ?>">
            
            <label class="form-label fw-bold">Justificativa Médica:</label>
            <textarea name="justificativa" class="form-control" rows="3" required placeholder="Ex: Paciente apresentou alergia ou erro de prescrição..."></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
            <button type="submit" name="cancelar_item" class="btn btn-danger">Confirmar Interrupção</button>
        </div>
        </form>
    </div>
    </div>

    <script>
    function abrirModalCancelamento(id, nome) {
        document.getElementById('item_id_cancelar').value = id;
        document.getElementById('nomeMedModal').innerText = nome;
        new bootstrap.Modal(document.getElementById('modalCancelamento')).show();
    }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>