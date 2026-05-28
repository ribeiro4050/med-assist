<?php 
    require '../Model/conexao.php';
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuário - Visualizar Perfil</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  </head>
  <body class="bg-light">
    
    <?php include('navbar.php'); ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0"><i class="bi bi-person-bounding-box me-2"></i>Detalhes do Usuário</h4>
                        <a href="javascript:history.go(-1)" class="btn btn-light btn-sm"><i class="bi bi-arrow-left-short"></i> Voltar</a>
                    </div>
                    <div class="card-body p-4">
                        <?php 
                            if(isset($_GET['id'])){
                                $usuario_id = mysqli_real_escape_string($conexao, $_GET['id']);
                                $sql = "SELECT * FROM usuarios WHERE id = $usuario_id";
                                $query = mysqli_query($conexao, $sql);

                                if(mysqli_num_rows($query) > 0){
                                    $usuario = mysqli_fetch_array($query);
                        ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted small">Nome Completo</label>
                                        <p class="form-control bg-white py-2 shadow-sm border-light-subtle">
                                            <?= htmlspecialchars($usuario['nome']) ?>
                                        </p>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted small">E-mail Cadastrado</label>
                                        <p class="form-control bg-white py-2 shadow-sm border-light-subtle">
                                            <?= htmlspecialchars($usuario['email'] ?? 'Não informado') ?>
                                        </p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted small">CPF</label>
                                            <p class="form-control bg-white py-2 shadow-sm border-light-subtle">
                                                <?= htmlspecialchars($usuario['cpf'] ?? 'Não informado') ?>
                                            </p>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold text-muted small">Data de Nascimento</label>
                                            <p class="form-control bg-white py-2 shadow-sm border-light-subtle">
                                                <?= ($usuario['data_nascimento'] !== '0000-00-00') ? date('d/m/Y', strtotime($usuario['data_nascimento'])) : 'Não cadastrada' ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted small">Nível de Acesso (Tipo)</label>
                                        <div>
                                            <span class="badge py-2 px-3 fs-6 <?php
                                                if($usuario['role'] == 'admin') echo 'bg-danger';
                                                elseif($usuario['role'] == 'medico') echo 'bg-primary';
                                                elseif($usuario['role'] == 'enfermeiro') echo 'bg-info';
                                                elseif($usuario['role'] == 'paciente') echo 'bg-success';
                                                else echo 'bg-secondary';
                                            ?>">
                                                <?= ucfirst($usuario['role']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <?php if (!empty($usuario['crm_registro'])): ?>
                                        <div class="mb-3 mt-4 border-top pt-3">
                                            <label class="form-label fw-bold text-primary"><i class="bi bi-heart-pulse-fill me-1"></i> Registro Profissional (Médico)</label>
                                            <p class="form-control bg-blue-subtle text-primary-emphasis fw-bold py-2 shadow-sm">
                                                CRM: <?= htmlspecialchars($usuario['crm_registro']) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($usuario['coren_registro'])): ?>
                                        <div class="mb-3 mt-4 border-top pt-3">
                                            <label class="form-label fw-bold text-success"><i class="bi bi-shield-plus me-1"></i> Registro Profissional (Enfermagem)</label>
                                            <p class="form-control bg-blue-subtle text-success-emphasis fw-bold py-2 shadow-sm">
                                                COREN: <?= htmlspecialchars($usuario['coren_registro']) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>

                        <?php 
                                } else {
                                    echo "<div class='alert alert-warning text-center my-3'>Usuário não encontrado no banco de dados.</div>";
                                }
                            } else {
                                echo "<div class='alert alert-danger text-center my-3'>ID de usuário inválido ou ausente.</div>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>