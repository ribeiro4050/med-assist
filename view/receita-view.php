<?php 
    session_start();
    require '../Model/conexao.php';

    // Garante que o usuário esteja logado
    // if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    //     header('Location: login.php');
    //     exit;
    // }
    
    // Verifica se o ID da receita foi passado na URL
    if(!isset($_GET['id'])) {
        header('Location: receitas.php');
        exit;
    }

    $receita_id = mysqli_real_escape_string($conexao, $_GET['id']);
    $usuario_id = $_SESSION['id_usuario'];
    $role = $_SESSION['role_usuario'];
    
    // Consulta SQL para buscar a receita e os dados do paciente e médico
    $sql_receita = "SELECT r.*, p.nome AS nome_paciente, p.data_nascimento AS nasc_paciente, 
                           m.nome AS nome_medico, m.crm_registro
                    FROM receitas r
                    JOIN usuarios p ON r.paciente_id = p.id
                    JOIN usuarios m ON r.medico_id = m.id
                    WHERE r.id = $receita_id";
    
    $query_receita = mysqli_query($conexao, $sql_receita);

    if(mysqli_num_rows($query_receita) == 0) {
        $_SESSION['mensagem'] = "Receita não encontrada.";
        header('Location: receitas.php');
        exit;
    }

    $receita = mysqli_fetch_array($query_receita);

    $header_color = 'bg-success'; // Cor de fallback, pode ser Simples
    $text_color = 'text-white'; // Cor do texto padrão

    switch ($receita['tipo_receita']) {
        case 'Amarela':
            // receita amarela
            $header_color = 'bg-warning'; 
            $text_color = 'text-white';
            break;
        case 'Azul':
            // receita azul
            $header_color = 'bg-primary'; 
            $text_color = 'text-white';
            break;
        default:
            // receita simples )
            $header_color = 'bg-light'; 
            $text_color = 'text-dark'; // Usa texto escuro no fundo claro
            break;
        }
    // Médico só pode ver SUAS receitas, Paciente pode ver SÓ as dele.
    if ($role === 'medico' && $receita['medico_id'] != $usuario_id) {
        $_SESSION['mensagem'] = "Acesso negado. Esta receita foi prescrita por outro profissional.";
        header('Location: receitas.php');
        exit;
    }
    if ($role === 'paciente' && $receita['paciente_id'] != $usuario_id) {
         $_SESSION['mensagem'] = "Acesso negado. Esta receita não pertence a você.";
        header('Location: index.php'); // Paciente volta para a index
        exit;
    }

    // Consulta para buscar os itens (medicamentos) desta receita
    $sql_itens = "SELECT * FROM itens_receita WHERE receita_id = $receita_id";
    $query_itens = mysqli_query($conexao, $sql_itens);
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Receita #<?= $receita_id ?></title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  </head>
  <body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header <?= $header_color ?> <?= $text_color ?>">
                        <h4 class="mb-0">
                            Receituário #<?= $receita_id ?> (<?= $receita['tipo_receita'] ?>)
                            <a href="receitas.php" class="btn btn-light float-end">Voltar</a>
                        </h4>
                    </div>
                    <div class="card-body p-5">
                        
                        <div class="text-center mb-4 border-bottom pb-3">
                            <h5 class="text-primary">Dr. <?= $receita['nome_medico'] ?></h5>
                            <p class="mb-1">CRM: <?= $receita['crm_registro'] ?? 'N/D' ?></p>
                            <p class="mb-0">Data da Prescrição: <?= date('d/m/Y H:i', strtotime($receita['data_prescricao'])) ?></p>
                        </div>

                        <div class="mb-4">
                            <h6>Paciente: <?= $receita['nome_paciente'] ?></h6>
                            <p class="small mb-0">Nascimento: <?= date('d/m/Y', strtotime($receita['nasc_paciente'])) ?></p>
                        </div>
                        
                        <h5 class="border-bottom pb-2 mt-4 text-success">Prescrição</h5>
                        
                        <?php 
                        if(mysqli_num_rows($query_itens) > 0): 
                            while($item = mysqli_fetch_array($query_itens)):
                        ?>
                            <div class="mb-4 p-3 border rounded">
                                <h6 class="text-dark"><?= $item['medicamento_nome'] ?> (<?= $item['concentracao'] ?>)</h6>
                                <p class="mb-1">Quantidade: <?= $item['quantidade_total'] ?></p>
                                <p class="mb-0">Posologia: <?= $item['posologia'] ?></p>
                            </div>
                        <?php 
                            endwhile;
                        else:
                            echo "<p class='text-danger'>Nenhum medicamento encontrado para esta receita.</p>";
                        endif;
                        ?>

                        <?php if(!empty($receita['observacoes'])): ?>
                        <h5 class="border-bottom pb-2 mt-4 text-secondary">Observações</h5>
                        <p><?= $receita['observacoes'] ?></p>
                        <?php endif; ?>
                        
                        <?php if($role === 'medico' && $receita['medico_id'] == $usuario_id): ?>
                        <div class="mt-4 pt-3 border-top">
                            <a href="receita-edit.php?id=<?= $receita_id ?>" class="btn btn-success">
                                <span class="bi-pencil-fill"></span> Editar
                            </a>
                            <!-- comando window print chama o dialogo de impressao do navegador, bem legal -->
                            <button onclick="window.print()" class="btn btn-secondary">
                                <span class="bi-printer"></span> Imprimir
                            </button>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>