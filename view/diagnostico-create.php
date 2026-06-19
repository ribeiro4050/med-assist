<?php require_once '../controller/DiagnosticoController.php'; ?>

<?php
/**
 * @var array $p
 * @var string $triagem_id
 * @var string $paciente_id
 */
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Prescrever Diagnóstico - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-header bg-dark text-white p-4 rounded-top-4">
                        <h4 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Novo Laudo Diagnóstico</h4>
                        <p class="mb-0 opacity-75">Paciente: <?= htmlspecialchars($p['nome']) ?></p>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Formulário apontando para o Novo Controller[cite: 8] -->
                        <form action="../controller/DiagnosticoController.php" method="POST">
                            <input type="hidden" name="triagem_id" value="<?= $triagem_id ?>">
                            <input type="hidden" name="paciente_id" value="<?= $paciente_id ?>">

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-primary">CID-10</label>
                                    <input type="text" name="cid_10" class="form-control border-2" 
                                           placeholder="Ex: I10" maxlength="10">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-primary">Descrição Detalhada do Laudo</label>
                                <textarea name="diagnostico_descricao" class="form-control border-2" rows="10" required 
                                          placeholder="Descreva aqui a evolução clínica e a conclusão do diagnóstico..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between border-top pt-4">
                                <a href="javascript:history.go(-1)" class="btn btn-outline-secondary px-4 rounded-pill">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" name="btn_salvar_diagnostico" class="btn btn-success btn-lg px-5 rounded-pill shadow">
                                    <i class="fas fa-save me-2"></i>Salvar Diagnóstico
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>