<?php 
    session_start();
    require '../Model/conexao.php'; 

    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header('Location: login.php'); 
        exit;
    }

    $usuario_id = $_SESSION['id_usuario']; 
    $role_usuario = $_SESSION['role_usuario'];
    
    $sql_perfil = "SELECT * FROM usuarios WHERE id = $usuario_id";
    $query_perfil = mysqli_query($conexao, $sql_perfil);
    $usuario = mysqli_fetch_array($query_perfil);

    $query_receitas = null;
    $query_exames = null;
    $query_diagnosticos = null;

    if ($role_usuario === 'paciente') {
        // Busca Receitas (Tabela: receitas)
        $sql_receitas = "SELECT r.id, r.data_prescricao, r.tipo_receita, m.nome AS nome_medico
                         FROM receitas r JOIN usuarios m ON r.medico_id = m.id
                         WHERE r.paciente_id = $usuario_id ORDER BY r.data_prescricao DESC";
        $query_receitas = mysqli_query($conexao, $sql_receitas);

        // Busca Exames (No seu dump a tabela é 'exame' ou 'guia_exames')
        // Vou usar 'guia_exames' pois é a que tem os campos que você exibiu anteriormente
        $sql_exames = "SELECT g.id, g.data_solicitacao, m.nome AS nome_medico 
                       FROM guia_exames g JOIN usuarios m ON g.medico_id = m.id
                       WHERE g.paciente_id = $usuario_id ORDER BY g.data_solicitacao DESC";
        $query_exames = mysqli_query($conexao, $sql_exames);

        // CORREÇÃO AQUI: De 'diagnosticos' para 'diagnostico'
        // E de 'data_diagnostico' para 'data' (conforme seu dump)
        $sql_diag = "SELECT d.id, d.data as data_diagnostico, d.cid_10, m.nome AS nome_medico 
                     FROM diagnostico d JOIN usuarios m ON d.medico_id = m.id
                     WHERE d.paciente_id = $usuario_id ORDER BY d.data DESC";
        $query_diagnosticos = mysqli_query($conexao, $sql_diag);
    }
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil do Usuário</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  </head>
  <body>
    <?php include('navbar.php'); ?>
    
    <div class="container mt-3">
        <?php include('mensagem.php'); ?>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h4>Meu Perfil
                            <a href="javascript:history.go(-1)" class="btn btn-danger float-end ms-2">Voltar</a>
                            
                            <?php if ($role_usuario === 'paciente'): ?>
                            <a href="../controller/historico-paciente-controller.php?id=<?= $usuario['id']?>" class="btn btn-secondary float-end ms-2">
                                <span class="bi bi-clipboard-data"></span> Histórico
                            </a>
                            <?php endif; ?>

                            <a href="usuario-edit.php?id=<?= $usuario['id']?>" class="btn btn-success float-end ms-2">
                                <span class="bi bi-pencil-fill"></span> Editar
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if($usuario): ?>
                            <div class="mb-3">
                                <label>Nome</label>
                                <p class="form-control"><?= $usuario['nome']?></p>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <p class="form-control"><?= $usuario['email']?></p>
                            </div>
                            <div class="mb-3">
                                <label>CPF</label>
                                <p class="form-control bg-light"><?= $usuario['cpf']?></p>
                            </div>
                            <div class="mb-3">
                                <label>Data de Nascimento</label>
                                <p class="form-control"><?= date('d/m/Y', strtotime($usuario['data_nascimento']))?></p>
                            </div>

                            <?php if ($role_usuario === 'paciente'): ?>
                            <div class="card mt-4 shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <button class="nav-link active" id="receitas-tab" data-bs-toggle="tab" data-bs-target="#receitas" type="button">Receitas</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" id="exames-tab" data-bs-toggle="tab" data-bs-target="#exames" type="button">Exames</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link" id="diag-tab" data-bs-toggle="tab" data-bs-target="#diag" type="button">Diagnósticos</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body tab-content" id="myTabContent">
                                    
                                    <div class="tab-pane fade show active" id="receitas" role="tabpanel">
                                        <table class="table table-hover">
                                            <thead><tr><th>Data</th><th>Tipo</th><th>Médico</th><th>Ação</th></tr></thead>
                                            <tbody>
                                                <?php while($r = mysqli_fetch_array($query_receitas)): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y', strtotime($r['data_prescricao'])) ?></td>
                                                    <td><?= $r['tipo_rece_ita'] ?? $r['tipo_receita'] ?></td>
                                                    <td>Dr. <?= $r['nome_medico'] ?></td>
                                                    <td><a href="receita-view.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Ver</a></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="exames" role="tabpanel">
                                        <table class="table table-hover">
                                            <thead><tr><th>Data</th><th>Médico</th><th>Ação</th></tr></thead>
                                            <tbody>
                                                <?php while($e = mysqli_fetch_array($query_exames)): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y', strtotime($e['data_solicitacao'])) ?></td>
                                                    <td>Dr. <?= $e['nome_medico'] ?></td>
                                                    <td><a href="guia-exame-view.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-printer"></i> Guia</a></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane fade" id="diag" role="tabpanel">
                                        <table class="table table-hover">
                                            <thead><tr><th>Data</th><th>CID-10</th><th>Médico</th><th>Ação</th></tr></thead>
                                            <tbody>
                                                <?php while($d = mysqli_fetch_array($query_diagnosticos)): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y', strtotime($d['data_diagnostico'])) ?></td>
                                                    <td><span class="badge bg-danger"><?= $d['cid_10'] ?></span></td>
                                                    <td>Dr. <?= $d['nome_medico'] ?></td>
                                                    <td><a href="diagnostico-view.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-dark"><i class="bi bi-file-text"></i> Detalhes</a></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="alert alert-warning">Dados do perfil não encontrados.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>