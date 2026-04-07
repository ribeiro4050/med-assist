<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Proteção básica: só enfermeiros, médicos ou admins entram aqui
    if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] === 'paciente') {
        header("Location: login.php");
        exit;
    }

    require_once '../Model/conexao.php';
    include('mensagem.php');

    // Busca todos os pacientes para o <select>
    $sql_pacientes = "SELECT id, nome FROM usuarios WHERE role = 'paciente' ORDER BY nome ASC";
    $query_pacientes = mysqli_query($conexao, $sql_pacientes);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Realizar Triagem - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> <!-- para buscas mais eficientes -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
    /* 1. Estado Normal: Apenas a borda e o texto laranja */
    .btn-outline-orange {
        color: #fd7e14;
        border-color: #fd7e14;
        background-color: transparent;
    }

    /* 2. Remove o efeito de hover (passar o mouse) */
    /* Forçamos o fundo a continuar transparente e o texto laranja */
    .btn-outline-orange:hover {
        background-color: transparent !important;
        color: #fd7e14 !important;
        border-color: #fd7e14 !important;
    }

    /* 3. Estado Selecionado (Clicado): Aqui sim ele ganha cor */
    .btn-check:checked + .btn-outline-orange {
        background-color: #fd7e14 !important;
        color: #fff !important;
        border-color: #fd7e14 !important;
    }
</style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-notes-medical me-2"></i>Nova Triagem</h4>
                    </div>
                    <div class="card-body">
                        <form action="../controller/acoes.php" method="POST">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Paciente</label>
                                <select name="paciente_id" class="form-select select2-paciente" required>
                                <option value="">Selecione o paciente...</option>
                                <?php while($paciente = mysqli_fetch_assoc($query_pacientes)): ?>
                                    <option value="<?= $paciente['id']; ?>"><?= $paciente['nome']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Queixa Principal / Sintomas</label>
                                <textarea name="queixa_principal" class="form-control" rows="3" placeholder="O que o paciente está sentindo?" required></textarea>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Pressão Arterial (Sist./Diast.)</label>
                                    <div class="input-group">
                                        <input type="number" name="pressao_sistolica" class="form-control" placeholder="120" min="40" max="300" required>
                                        <span class="input-group-text">/</span>
                                        <input type="number" name="pressao_diastolica" class="form-control" placeholder="80" min="20" max="200" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Temperatura (°C)</label>
                                    <input type="number" step="0.1" name="temperatura" class="form-control" placeholder="Ex: 36.5" min= "0" max="45" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Altura (m)</label>
                                    <input type="number" step="0.01" name="altura" class="form-control" placeholder="Ex: 1.75" min= "0.3" max="2.5" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Peso (kg)</label>
                                    <input type="number" step="0.01" name="peso" class="form-control" placeholder="Ex: 70.0" min= "2" max="632.5" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Freq. Cardíaca (BPM)</label>
                                    <input type="number" name="frequencia_cardiaca" class="form-control" placeholder="Ex: 80" min= "15" max="400" required> 
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Saturação (%)</label>
                                    <input type="number" name="saturacao" class="form-control" placeholder="Ex: 98" min= "20" max="100" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold d-block">Classificação de Risco</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="classificacao_risco" id="azul" value="Azul" checked>
                                    <label class="btn btn-outline-info" for="azul">Não Urgente</label>

                                    <input type="radio" class="btn-check" name="classificacao_risco" id="verde" value="Verde">
                                    <label class="btn btn-outline-success" for="verde">Pouco Urgente</label>

                                    <input type="radio" class="btn-check" name="classificacao_risco" id="amarelo" value="Amarelo">
                                    <label class="btn btn-outline-warning" for="amarelo">Urgente</label>

                                    <input type="radio" class="btn-check" name="classificacao_risco" id="laranja" value="Laranja">
                                    <label class="btn btn-outline-orange"
                                    for="laranja">Muito urgente</label>

                                    <input type="radio" class="btn-check" name="classificacao_risco" id="vermelho" value="Vermelho">
                                    <label class="btn btn-outline-danger" for="vermelho">Emergência</label>
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="painel-enfermagem.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" name="create_triagem" class="btn btn-primary px-5">Finalizar Triagem</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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