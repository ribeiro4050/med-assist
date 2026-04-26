<?php 
    session_start();
    require '../Model/conexao.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuario - Editar</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
  <body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Usuario
                            <a href="javascript:history.go(-1)" class="btn btn-danger float-end">Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php 
                            if(isset($_GET['id'])) {//chave fechada na linha 
                                $usuario_id = mysqli_real_escape_string($conexao, $_GET['id']);

                                $sql = "SELECT * FROM usuarios WHERE id = $usuario_id";

                                $query = mysqli_query($conexao, $sql);

                                if(mysqli_num_rows($query) > 0){
                                    $usuario = mysqli_fetch_array($query);
                                
                        ?>
                        <form action="../controller/acoes.php" method="post">
                            <input type="hidden" name="usuario_id" value="<?= $usuario['id']?>">
                            <div class="mb-3">
                                <label for="">Nome</label>
                                <input type="text" name="nome" value="<?= $usuario['nome']?>" class="form-control" id="">
                            </div>
                            <div class="mb-3">
                                <label for="">Email</label>
                                <input type="text" name="email" value="<?= $usuario['email']?>" class="form-control" id="">
                            </div>
                            <div class="mb-3">
                                <label for="">Data de nascimento</label>
                                <input type="date" name="data_nascimento" value="<?= $usuario['data_nascimento']?>" class="form-control" id="">
                            </div>
                            <div class="mb-3">
                            <label>Nova Senha (deixe em branco para não alterar)</label>
                            <div class="input-group">
                                <input type="password" name="senha" id="senha" class="form-control">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('senha', 'eye-icon-1')">
                                    <i id="eye-icon-1" class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Confirmar Nova Senha</label>
                            <div class="input-group">
                                <input type="password" name="senha_confirmar" id="senha_confirmar" class="form-control">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('senha_confirmar', 'eye-icon-2')">
                                    <i id="eye-icon-2" class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" name="update_usuario" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                        </form>
                        <?php 
                                } else {
                                    echo "<h5> Usuário não encontrado. </h5>";
                                }
                            } else {
                                echo "<h5> ID não fornecido. </h5>";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    }
    </script>
</body>
</html>