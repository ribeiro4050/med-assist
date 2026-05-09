<?php
session_start();
require '../Model/conexao.php';
require_once '../Model/EnfermagemService.php';

// 1. Verificação de Sessão (Mesma lógica do home-enfermeiro)
if (!isset($_SESSION['logado'])) {
    header("Location: login.php?erro=sessao_expirada");
    exit;
}

if ($_SESSION['role_usuario'] !== 'enfermeiro') {
    header("Location: login.php?erro=acesso_negado");
    exit;
}

// 2. Pega o ID do paciente da URL
$id_paciente = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 3. Detecção Automática da Coluna de Role (Para evitar o Fatal Error)
$check_columns = mysqli_query($conexao, "SHOW COLUMNS FROM usuarios LIKE 'role_usuario'");
$coluna_final = (mysqli_num_rows($check_columns) > 0) ? 'role_usuario' : 'role';

if ($coluna_final == 'role') {
    $check_role = mysqli_query($conexao, "SHOW COLUMNS FROM usuarios LIKE 'role'");
    if (mysqli_num_rows($check_role) == 0) {
        $coluna_final = 'tipo'; 
    }
}

// 4. Busca dados do paciente específico
$sql_p = "SELECT * FROM usuarios WHERE id = $id_paciente AND $coluna_final = 'paciente'";
$res_p = mysqli_query($conexao, $sql_p);
$p = mysqli_fetch_assoc($res_p);

if (!$p) { 
    // Se o ID for inválido ou não for paciente, volta para o painel em vez de deslogar
    header('Location: painel-internacao.php?erro=paciente_nao_encontrado'); 
    exit; 
}

// 5. Busca o checklist de medicação
$enfermagemService = new EnfermagemService($conexao);
$dados_checklist = $enfermagemService->buscarChecklistPaciente($id_paciente);
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão: <?= $p['nome'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .perfil-header { background: #fff; padding: 30px 0; border-bottom: 1px solid #dee2e6; margin-bottom: 30px; }
        .foto-perfil { width: 120px; height: 120px; background: #f8f9fa; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px auto; }
        .card-header.bg-primary { background-color: #0d6efd !important; }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="perfil-header text-center shadow-sm">
        <div class="container">
            <div class="foto-perfil">
                <i class="bi bi-person-fill text-secondary" style="font-size: 4rem;"></i>
            </div>
            <h2 class="mb-1"><?= $p['nome'] ?></h2>
            <p class="text-muted mb-0">
                <strong>CPF:</strong> <?= $p['cpf'] ?? 'Não cadastrado' ?> | 
                <strong>Nasc:</strong> <?= date('d/m/Y', strtotime($p['data_nascimento'])) ?>
            </p>
            <a href="painel-internacao.php" class="btn btn-outline-secondary btn-sm mt-3"><i class="bi bi-arrow-left"></i> Voltar ao Painel</a>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-pills"></i> Administração de Medicamentos</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Medicamento</th>
                                        <th>Posologia</th>
                                        <th>Médico Prescritor</th>
                                        <th class="text-center">Ação / Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($dados_checklist && mysqli_num_rows($dados_checklist) > 0): ?>
                                        <?php while($item = mysqli_fetch_assoc($dados_checklist)): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <strong><?= $item['medicamento_nome'] ?></strong><br>
                                                <small class="text-muted"><?= $item['concentracao'] ?></small>
                                            </td>
                                            <td class="align-middle"><?= $item['posologia'] ?></td>
                                            <td class="align-middle">Dr(a). <?= $item['nome_medico'] ?? 'Não informado' ?></td>
                                            <td class="align-middle text-center">
                                                <?php if (isset($item['ja_administrado']) && $item['ja_administrado']): ?>
                                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Administrado</span>
                                                <?php else: ?>
                                                    <button class="btn btn-success btn-sm shadow-sm" onclick="abrirModalAdministracao(<?= $item['id'] ?>, '<?= $item['medicamento_nome'] ?>')">
                                                        Confirmar Dose
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center p-5 text-muted">
                                                <i class="bi bi-info-circle fs-2"></i><br>
                                                Nenhum medicamento pendente para este paciente no momento.
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

    <!-- Modal de Confirmação -->
    <div class="modal fade" id="modalAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../controller/administracao-controller.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Administração</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="item_id" id="modal_item_id">
                        <p>Deseja confirmar a aplicação do medicamento: <strong id="modal_medicamento_nome"></strong>?</p>
                        <div class="mb-3">
                            <label class="form-label">Observações:</label>
                            <textarea name="observacao" class="form-control" rows="3" placeholder="Ex: Paciente aceitou bem a medicação..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="confirmar_dose" class="btn btn-primary">Confirmar Aplicação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function abrirModalAdministracao(id, nome) {
        document.getElementById('modal_item_id').value = id;
        document.getElementById('modal_medicamento_nome').innerText = nome;
        var modalElement = document.getElementById('modalAdmin');
        var meuModal = new bootstrap.Modal(modalElement);
        meuModal.show();
    }
    </script>
</body>
</html>