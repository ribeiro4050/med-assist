<?php 
    session_start();
    require '../Model/conexao.php';

    // Verificação de acesso
    // Verifica se o usuário está logado (Comentar para manutenção)
    // if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    //     $_SESSION['mensagem'] = "É necessário estar logado para acessar esta página.";
    //     header('Location: login.php');
    //     exit;
    // }

    // Verifica se o usuário logado possui a role 'medico'(Comentar para manutenção)
    // if ($_SESSION['role_usuario'] !== 'medico') {
    //     $_SESSION['mensagem'] = "Acesso negado. Apenas médicos podem gerenciar receituários.";
    //     header('Location: index.php');
    //     exit;
    // }

    // O ID do médico logado será usado para filtrar as receitas
    $medico_id = $_SESSION['id_usuario'];
    ?>
    <!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receituário - MedAssist</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  </head>
  <body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
      <?php include('../view/mensagem.php')?>
      
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4> Minhas Receitas
                <a href="javascript:history.go(-1)"class="btn btn-danger float-end ms-2">Voltar</a>

                <a href="receita-create.php" class="btn btn-primary float-end">
                    <span class="bi-file-earmark-plus"></span> &nbsp; Nova Receita
                </a>
              </h4>
            </div>
            <div class="card-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID Receita</th>
                    <th>Paciente</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                 
                  // Busca apenas as receitas criadas pelo médico logado ($medico_id)
                  $sql = "SELECT r.*, p.nome AS nome_paciente 
                          FROM receitas r
                          JOIN usuarios p ON r.paciente_id = p.id
                          WHERE r.medico_id = '$medico_id'
                          ORDER BY r.data_prescricao DESC";

                  $receitas = mysqli_query($conexao, $sql);

                  if (mysqli_num_rows($receitas) > 0) {
                    foreach($receitas as $receita){
                  ?>
                  <tr>
                    <td><?= $receita['id']?></td>
                    <td><?= $receita['nome_paciente']?></td>
                    <td><?= date('d/m/Y', strtotime($receita['data_prescricao']))?></td>
                    <td><?= $receita['tipo_receita']?></td>
                    <td>
                      <a href="receita-view.php?id=<?= $receita['id']?>" class="btn btn-secondary btn-sm">
                      <span class="bi-eye-fill"></span></a>

                      <a href="receita-edit.php?id=<?= $receita['id']?>" class="btn btn-success btn-sm">
                      <span class="bi-pencil-fill"></span></a>

                      <form action="../controller/acoes.php" method="post" class="d-inline">
                        <button onclick="return confirm('Tem certeza que deseja excluir esta receita?')" type="submit" name="delete_receita" value="<?= $receita['id']?>" class="btn btn-danger btn-sm">
                          <span class="bi-trash3-fill"></span>
                        </button>
                      </form>
                    </td>
                  </tr>
                  <?php 
                    }
                  } else {
                    echo "<tr><td colspan='5'><h5> Você não prescreveu nenhuma receita ainda. </h5></td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>