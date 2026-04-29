<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    
    // Proteção de acesso
    if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'medico') {
        header("Location: login.php"); exit;
    }

    require_once '../Model/conexao.php';
    
    // Pegamos os IDs da URL (vêm do painel-medico.php)
    $paciente_id = $_GET['paciente_id'] ?? '';
    $triagem_id = $_GET['triagem_id'] ?? '';

    // CORREÇÃO AQUI: A coluna no seu banco se chama 'role'
    $sql_pacientes = "SELECT id, nome FROM usuarios WHERE role = 'paciente' ORDER BY nome ASC";
    $res_pacientes = mysqli_query($conexao, $sql_pacientes);

    // Verifica se a consulta falhou antes de continuar
    if (!$res_pacientes) {
        die("Erro no banco de dados: " . mysqli_error($conexao));
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerar Guia de Exame - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <?php include('mensagem.php'); ?>

        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0"><i class="fas fa-file-medical me-2"></i> Nova Guia de Exame</h4>
            </div>
            <div class="card-body p-4">
                <form action="../controller/ExameController.php" method="POST">
                    
                    <input type="hidden" name="triagem_id" value="<?= htmlspecialchars($triagem_id) ?>">
                    <input type="hidden" name="medico_id" value="<?= $_SESSION['id_usuario'] ?>">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label ">Paciente</label>
                            <select name="paciente_id" class="form-select" required>
                                <option value="">Selecione o paciente</option>
                                <?php 
                                // O loop while percorre todos os pacientes encontrados
                                while($p = mysqli_fetch_assoc($res_pacientes)): 
                                ?>
                                    <option value="<?= $p['id'] ?>" <?= ($p['id'] == $paciente_id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nome']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label ">Caráter da Solicitação</label>
                            <select name="carater_solicitacao" class="form-select" required>
                                <option value="Eletiva">Eletiva</option>
                                <option value="Urgência">Urgência</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label ">CID-10</label>
                            <input type="text" name="cid_10" class="form-control" placeholder="Ex: E03">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label ">Indicação Clínica (Obrigatório se pequena cirurgia, terapia, consulta de referência e alto custo)</label>
                        <textarea name="indicacao_clinica" class="form-control" rows="2" placeholder="Motivo da investigação"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label ">
                            Descrição dos Exames </label>
                        <textarea name="descricao_exames" class="form-control" rows="4" required placeholder="Ex: Hemograma"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                                <a href="javascript:history.go(-1)" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" name="create_guia_exame" class="btn btn-primary px-5">Finalizar Guia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2-paciente').select2({
            theme: 'bootstrap-5',
            placeholder: 'Digite o nome do paciente...'
        });
    });
</script>
</body>
</html>