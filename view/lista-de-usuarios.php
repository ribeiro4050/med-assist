<?php 
session_start();
require '../Model/conexao.php';

// Trava de segurança
if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['role_usuario'] !== 'admin') {
    $_SESSION['mensagem'] = "Acesso negado. Esta área é restrita ao administrador.";
    header('Location: home.php');
    exit;
}

// Consulta todos os usuários
$sql = 'SELECT * FROM usuarios ORDER BY nome ASC';
$res_usuarios = mysqli_query($conexao, $sql);

// Arrays para organizar os usuários por categoria (Abas)
$todos = [];
$medicos = [];
$enfermeiros = [];
$pacientes = [];
$admins = [];

while ($user = mysqli_fetch_assoc($res_usuarios)) {
    $todos[] = $user;
    if ($user['role'] == 'medico') $medicos[] = $user;
    elseif ($user['role'] == 'enfermeiro') $enfermeiros[] = $user;
    elseif ($user['role'] == 'paciente') $pacientes[] = $user;
    elseif ($user['role'] == 'admin') $admins[] = $user;
}

// Função auxiliar para renderizar a tabela dentro de cada aba
function renderizarTabela($lista) {
    if (empty($lista)) {
        return "<tr><td colspan='6' class='text-center'>Nenhum usuário encontrado nesta categoria.</td></tr>";
    }
    $html = '';
    foreach ($lista as $usuario) {
        $role = $usuario['role'];
        $classe = 'bg-secondary';
        if($role == 'admin') $classe = 'bg-danger';
        if($role == 'medico') $classe = 'bg-primary';
        if($role == 'enfermeiro') $classe = 'bg-info';
        if($role == 'paciente') $classe = 'bg-success';

        $data_nasc = date('d/m/Y', strtotime($usuario['data_nascimento']));
        
        $html .= "<tr>
                    <td>{$usuario['id']}</td>
                    <td>{$usuario['nome']}</td>
                    <td>{$usuario['email']}</td>
                    <td><span class='badge {$classe}'>" . ucfirst($role) . "</span></td>
                    <td>{$data_nasc}</td>
                    <td>
                        <a href='usuario-view.php?id={$usuario['id']}' class='btn btn-secondary btn-sm'><span class='bi-eye-fill'></span></a>
                        <a href='usuario-edit.php?id={$usuario['id']}' class='btn btn-success btn-sm'><span class='bi-pencil-fill'></span></a>
                        <form action='../controller/UsuarioController.php' method='post' class='d-inline'>
                            <button onclick=\"return confirm('Tem certeza que deseja excluir?')\" type='submit' name='delete_usuario' value='{$usuario['id']}' class='btn btn-danger btn-sm'>
                                <span class='bi-trash3-fill'></span>
                            </button>
                        </form>
                    </td>
                  </tr>";
    }
    return $html;
}
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciamento de Usuários - MedAssist</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  </head>
  <body>
    <?php include('navbar.php'); ?>
    
    <div class="container mt-4">
      <?php include('../view/mensagem.php')?>

      <div class="card shadow">
        <div class="card-header">
          <h4> 
            <i class="bi bi-people-fill"></i> Lista de Usuários
            <div class="float-end">
                <a href="usuario-create.php" class="btn btn-primary"><i class="bi bi-person-plus-fill"></i> Novo Usuário</a>
            </div>
          </h4>
        </div>
        
        <div class="card-body">
          <ul class="nav nav-tabs" id="userTabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">Todos <span class="badge bg-dark"><?= count($todos) ?></span></button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="med-tab" data-bs-toggle="tab" data-bs-target="#med" type="button">Médicos <span class="badge bg-primary"><?= count($medicos) ?></span></button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="enf-tab" data-bs-toggle="tab" data-bs-target="#enf" type="button">Enfermeiros <span class="badge bg-info"><?= count($enfermeiros) ?></span></button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="pac-tab" data-bs-toggle="tab" data-bs-target="#pac" type="button">Pacientes <span class="badge bg-success"><?= count($pacientes) ?></span></button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="adm-tab" data-bs-toggle="tab" data-bs-target="#adm" type="button">Admins <span class="badge bg-danger"><?= count($admins) ?></span></button>
            </li>
          </ul>

          <div class="tab-content mt-3" id="userTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel">
              <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                  <tr>
                    <th>ID</th><th>Nome</th><th>Email</th><th>Cargo</th><th>Nascimento</th><th>Ações</th>
                  </tr>
                </thead>
                <tbody><?= renderizarTabela($todos) ?></tbody>
              </table>
            </div>

            <div class="tab-pane fade" id="med" role="tabpanel">
              <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                  <tr>
                    <th>ID</th><th>Nome</th><th>Email</th><th>Cargo</th><th>Nascimento</th><th>Ações</th>
                  </tr>
                </thead>
                <tbody><?= renderizarTabela($medicos) ?></tbody>
              </table>
            </div>

            <div class="tab-pane fade" id="enf" role="tabpanel">
              <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                  <tr>
                    <th>ID</th><th>Nome</th><th>Email</th><th>Cargo</th><th>Nascimento</th><th>Ações</th>
                  </tr>
                </thead>
                <tbody><?= renderizarTabela($enfermeiros) ?></tbody>
              </table>
            </div>

            <div class="tab-pane fade" id="pac" role="tabpanel">
              <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                  <tr>
                    <th>ID</th><th>Nome</th><th>Email</th><th>Cargo</th><th>Nascimento</th><th>Ações</th>
                  </tr>
                </thead>
                <tbody><?= renderizarTabela($pacientes) ?></tbody>
              </table>
            </div>

            <div class="tab-pane fade" id="adm" role="tabpanel">
              <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                  <tr>
                    <th>ID</th><th>Nome</th><th>Email</th><th>Cargo</th><th>Nascimento</th><th>Ações</th>
                  </tr>
                </thead>
                <tbody><?= renderizarTabela($admins) ?></tbody>
              </table>
            </div>

          </div> </div> </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>