<?php 
    session_start();
    require '../Model/conexao.php';

    // Redireciona se o usuário não estiver logado
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header('Location: tela-login.php');
        exit;
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perfil do Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Meu Perfil
                            <a href="index.php" class="btn btn-danger float-end">Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php 
                            $usuario_id = mysqli_real_escape_string($conexao, $_SESSION['id_usuario']);

                            $sql = "SELECT * FROM usuarios WHERE id = $usuario_id";
                            $query = mysqli_query($conexao, $sql);

                            if(mysqli_num_rows($query) > 0){
                                $usuario = mysqli_fetch_array($query);
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
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>