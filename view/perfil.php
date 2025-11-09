<?php 
    session_start();
    require '../Model/conexao.php'; // Caminho corrigido para o arquivo de conexão

    // Redireciona se o usuário não estiver logado
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        // Redireciona para login.php 
        header('Location: login.php'); 
        exit;
    }

    // Pega as informações de sessão do usuário logado
    $usuario_id = $_SESSION['id_usuario']; 
    $role_usuario = $_SESSION['role_usuario'];
    
    // Busca dos dados do Perfil
    $sql_perfil = "SELECT * FROM usuarios WHERE id = $usuario_id";
    $query_perfil = mysqli_query($conexao, $sql_perfil);
    $usuario = mysqli_fetch_array($query_perfil);

    // variável da consulta de receitas
    $query_receitas = null;

    // Busca das receitas do paciente logado
    if ($role_usuario === 'paciente') {
        $sql_receitas = "SELECT r.id, r.data_prescricao, r.tipo_receita, m.nome AS nome_medico, m.crm_registro
                         FROM receitas r
                         JOIN usuarios m ON r.medico_id = m.id
                         WHERE r.paciente_id = $usuario_id
                         ORDER BY r.data_prescricao DESC"; // em ordem descrescente para pegar as receitas mais recentes primeiro
                         
        $query_receitas = mysqli_query($conexao, $sql_receitas);
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil do Usuário</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"></head>
  <body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        
                        <h4>Meu Perfil
                            <a href="javascript:history.go(-1)"class="btn btn-danger float-end ms-2">Voltar</a>

                            <a href="../controller/historico-paciente-controller.php?id=<?= $usuario['id']?>" class="btn btn-secondary float-end ms-2">
                            <span class="bi bi-clipboard-data"></span> &nbsp;
                            <!--icone de papel-->
                            Histórico</a>
                            
                            <a href="usuario-edit.php?id=<?= $usuario['id']?>" class="btn btn-success float-end ms-2">
                            <span class="bi-pencil-fill"></span> &nbsp;
                            <!--icone de lapis-->
                            Editar</a>

                            
                            
                            
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php 
                            if($usuario){ // Verifica se o array do usuário foi carregado
                        ?>
                            <div class="mb-3">
                                <label for="">Nome</label>
                                <p class="form-control">
                                    <?= $usuario['nome']?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label for="">Email</label>
                                <p class="form-control">
                                    <?= $usuario['email']?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label for="">Nascimento</label>
                                <p class="form-control">
                                    <?= date('d/m/Y', strtotime($usuario['data_nascimento']))?>
                                </p>
                            </div>
                        <?php 
                            } else {
                                echo "<h5>Usuário não encontrado.</h5>";
                            }
                        ?>
                    </div>
                </div>

                <?php 
                    // Verifica se o usuário é um paciente e se a consulta foi executada
                   if ($role_usuario === 'paciente' && $query_receitas !== false): 
                ?>
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4>Histórico de Receituários</h4>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($query_receitas) > 0): ?>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data de Emissão</th>
                                    <th>Tipo</th>
                                    <th>Prescrito por</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($receita = mysqli_fetch_array($query_receitas)): ?>
                                <tr>
                                    <td><?= $receita['id']?></td>
                                    <td><?= date('d/m/Y', strtotime($receita['data_prescricao']))?></td>
                                    <td><?= $receita['tipo_receita']?></td>
                                    <td><?= $receita['nome_medico']?> (CRM: <?= $receita['crm_registro']?>)</td>
                                    <td>
                                        <a href="receita-view.php?id=<?= $receita['id']?>" class="btn btn-secondary btn-sm">
                                            <span class="bi-eye-fill"></span> Visualizar
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Nenhuma receita encontrada no seu histórico.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php endif; ?>
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>