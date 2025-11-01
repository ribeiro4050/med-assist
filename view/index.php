<?php 
session_start();
// inicia a sessão para usar variáveis de sessão, tanto para salvar quanto para ler, como no caso de exibir mensagens para o usuario, sem esse comando a sessão não funciona logo as mensagens de erro ou sucesso não aparecem  
require '../Model/conexao.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"><!--link dos icones-->
  </head>
  <body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
      <?php include('../controller/mensagem.php')?>
      <!-- exibe a mensagem de sucesso erro ou alerta para o usuario, usando dados salvos na sessão, no caso $_SESSION['mensagem']-->
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4> Lista de Usuarios
                <a href="usuario-create.php" class="btn btn-primary float-end">Adicionar Usuario</a>
                <!-- href deve ser do mesmo nome do arquivo de criar usuario -->
                <!-- float-end serve para colocar o botao do lado direito -->
              </h4>
            </div>
            <div class="card-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Data de nascimento</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $sql = 'SELECT * FROM usuarios';
                  // $sql guarda a consulta que será feita no banco de dados

                  $usuarios = mysqli_query($conexao, $sql);
                  // $usuarios faz a consulta no banco de dados, usando a conexao e a consulta sql que nesse caso é para selecionar (select) todos (*) os usuarios (FROM usuarios)

                  //print_r($usuarios); 
                  // print_r exibe o conteudo de uma variavel, nesse caso $usuarios
                  //exit;
                  // e o exit para a execução do script, ou seja, nada abaixo do exit é executado
                  if (mysqli_num_rows($usuarios) > 0) {//chave fechada na linha 69
                    // mysqli_num_rows conta o numero de linhas retornadas pela consulta, ou seja, quantos usuarios foram encontrados
                    foreach($usuarios as $usuario){

                  ?>
                  <tr>
                    <td><?= $usuario['id']?></td>
                    <td><?= $usuario['nome']?></td>
                    <td><?= $usuario['email']?></td>
                    <td><?= date('d/m/Y', strtotime($usuario['data_nascimento']))
                    // formata a data de nascimento para o formato brasileiro dia/mes/ano
                    ?></td>
                    <td>
                      <a href="usuario-view.php?id=<?= $usuario['id']
                      // passa o id do usuario pela url para o arquivo usuario-view.php, fazendo com que ao clicar em "visualizar" voce seja levado para a pagina daquele usuario específico
                      ?>" class="btn btn-secondary btn-sm">
                      <span class="bi-eye-fill"></span> &nbsp;
                      <!--icone de olho, acessar aba icons e cdn do bootstrap para mais icones-->
                      Visualizar</a>

                      <a href="usuario-edit.php?id=<?= $usuario['id']?>" class="btn btn-success btn-sm">
                      <span class="bi-pencil-fill"></span> &nbsp;
                      <!--icone de lapis-->
                      Editar</a>

                      <form action="../controller/acoes.php" method="post" class="d-inline">
                        <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="delete_usuario" value="<?= $usuario['id']?>" class="btn btn-danger btn-sm">
                          <span class="bi-trash3-fill"></span> &nbsp;
                          <!--icone de lixeira-->
                          Excluir
                        </button>
                      </form>
                    </td>
                  </tr>
                  <?php 
                  }// fechamento do foreach
                 }// fechamento do if mysqli_num_rows
                  else {
                    echo "<h5> Nenhum usuario encontrado </h5>";
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