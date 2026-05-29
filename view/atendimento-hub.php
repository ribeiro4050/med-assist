<!-- mostrando pro vscode que as variaveis vem do Controller -->
<?php
/**
 * @var string $triagem_id
 * @var array $t
 * @var array $v_press 
 * @var array $v_temp 
 * @var array $v_freq 
 * @var array $v_sat 
 * @var array $v_imc
 * @var array|null $exame_recente
 * @var array|null $receita_recente
 * @var array|null $diag_recente
 * @var array|null $idade
 */
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Atendimento - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card-atendimento { transition: all 0.3s; text-decoration: none; border-radius: 15px; border: none; }
        .card-atendimento:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .border-indicador { border-left: 5px solid !important; }
    </style>
</head>
<body class="bg-light">
    <?php include('navbar.php'); ?>

    <div class="container py-4">
        <?php include('mensagem.php'); ?>

        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h2 class="h4 text-secondary">Atendimento: <span class="text-dark"><?= htmlspecialchars($t['nome']) ?></span></h2>
            <span class="badge bg-primary">ID Triagem: #<?= $triagem_id ?></span>
        </div>

        <div class="row g-3 mb-4 text-center">
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-primary">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-heartbeat"></i> Pressão</small>
                        <h5 class="mb-0 text-dark"><?= $t['pressao_sistolica'] ?>/<?= $t['pressao_diastolica'] ?> <small>mmHg</small></h5>
                        <small class="<?= $v_press['class'] ?> fw-bold"><?= $v_press['msg'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-warning">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-thermometer-half"></i> Temp.</small>
                        <h5 class="mb-0 text-dark"><?= $t['temperatura'] ?>°C</h5>
                        <small class="<?= $v_temp['class'] ?> fw-bold"><?= $v_temp['msg'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-danger">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-pulse"></i> Freq. Card.</small>
                        <h5 class="mb-0 text-dark"><?= $t['frequencia_cardiaca'] ?> <small>bpm</small></h5>
                        <small class="<?= $v_freq['class'] ?> fw-bold"><?= $v_freq['msg'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-success">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-lungs"></i> Saturação</small>
                        <h5 class="mb-0 text-dark"><?= $t['saturacao'] ?>%</h5>
                        <small class="<?= $v_sat['class'] ?> fw-bold"><?= $v_sat['msg'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm border-indicador border-info">
                    <div class="card-body p-2">
                        <small class="text-muted fw-bold d-block"><i class="fas fa-weight"></i> IMC</small>
                        <h5 class="mb-0 text-dark"><?= $v_imc['valor'] ?></h5>
                        <small class="<?= $v_imc['class'] ?> fw-bold"><?= $v_imc['msg'] ?></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body">
                <h6 class="card-title fw-bold text-primary"><i class="fas fa-comment-medical me-2"></i>Queixa Principal:</h6>
                <p class="card-text bg-light p-3 rounded border">"<?= nl2br(htmlspecialchars($t['queixa_principal'])) ?>"</p>
            </div>
        </div>
        
        <h3 class="mb-4 fw-bold text-dark"><i class="fas fa-th-large me-3 text-primary"></i>Ações Médicas</h3>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <a href="../view/diagnostico-create.php?triagem_id=<?= $triagem_id ?>&paciente_id=<?= $t['paciente_id'] ?>" class="card card-atendimento h-100 shadow bg-dark text-white p-4">
                    <div class="card-body text-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-stethoscope fa-2x"></i>
                        </div>
                        <h4 class="fw-bold">Diagnóstico</h4>
                        <p class="mb-0 opacity-75">Registrar laudo final e CID-10</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="../view/receita-create.php?paciente_id=<?= $t['paciente_id']; ?>&triagem_id=<?= $t['id']; ?>" class="card card-atendimento h-100 shadow bg-primary text-white p-4">
                    <div class="card-body text-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-pills fa-2x"></i>
                        </div>
                        <h4 class="fw-bold">Prescrever Receita</h4>
                        <p class="mb-0 opacity-75">Medicamentos e Orientações</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="../view/guia-exame-create.php?triagem_id=<?= $triagem_id ?>&paciente_id=<?= $t['paciente_id'] ?>" class="card card-atendimento h-100 shadow bg-info text-white p-4">
                    <div class="card-body text-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex p-3 mb-3 text-info">
                            <i class="fas fa-microscope fa-2x text-white"></i>
                        </div>
                        <h4 class="fw-bold text-white">Solicitar Exames</h4>
                        <p class="mb-0 opacity-75 text-white">Laboratoriais e Imagem</p>
                    </div>
                </a>
            </div>
            <div class="col-md-12 mt-4">
                <button type="button" class="btn btn-warning btn-lg w-100 shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalRiscoCardiaco">
                    <i class="fas fa-brain me-2"></i> Analisar Risco Cardíaco com Inteliência Artificial
                </button>
            </div>
        </div>

        <?php if ($exame_recente || $receita_recente): ?>
        <div class="alert alert-secondary bg-white border-0 shadow-sm rounded-4 p-4 mb-5">
            <h5 class="fw-bold mb-3"><i class="fas fa-file-alt me-2 text-primary"></i>Documentos Gerados Agora</h5>
            <div class="d-flex gap-3">
                <?php if ($receita_recente): ?>
                    <a href="../view/receita-view.php?id=<?= $receita_recente['id'] ?>" class="btn btn-outline-primary rounded-pill">
                        <i class="fas fa-print me-2"></i>Imprimir Receita
                    </a>
                <?php endif; ?>
                <?php if ($diag_recente): ?>
                    <a href="../view/diagnostico-view.php?id=<?= $diag_recente['id'] ?>" class="btn btn-outline-secondary rounded-pill">
                        <i class="fas fa-print me-2"></i>Imprimir Diagnóstico
                    </a>
                <?php endif; ?>
                <?php if ($exame_recente): ?>
                    <a href="../view/guia-exame-view.php?id=<?= $exame_recente['id'] ?>" class="btn btn-outline-info rounded-pill">
                        <i class="fas fa-print me-2"></i>Imprimir Guia de Exame
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$exame_recente && !$receita_recente && !$diag_recente): ?>
            <div class="text-center py-4">
                <p class="text-muted">Nenhum documento foi gerado para este atendimento ainda.</p>
            </div>
        <?php endif; ?>            

        <div class="text-center mt-5">
            <form action="../controller/ExameController.php" method="POST" id="formFinalizar">
                <input type="hidden" name="triagem_id" value="<?= $triagem_id ?>">
                
                <div class="d-flex justify-content-center mb-4">
                    <div class="form-check form-switch bg-white p-3 px-4 rounded-pill shadow-sm" style="max-width: 450px;">
                        <input class="form-check-input ms-0 me-2" type="checkbox" id="checkFinalizar" name="confirmacao_prescricao">
                        <label class="form-check-label fw-bold text-secondary" for="checkFinalizar">
                            Confirmo que finalizei todas as prescrições e exames necessários para este atendimento.
                        </label>
                    </div>
                </div>

                <button type="submit" name="concluir_atendimento" id="btnFinalizar" class="btn btn-danger btn-lg px-5 rounded-pill shadow" disabled>
                    <i class="fas fa-check-circle me-2"></i> Finalizar Atendimento
                </button>
            </form>
            <div class="mt-4">
                <a href="../view/painel-medico.php" class="text-decoration-none text-muted">
                    <i class="fas fa-arrow-left me-2"></i>Sair sem finalizar (O paciente continuará na fila)
                </a>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRiscoCardiaco" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-heartbeat me-2"></i>MedAssist IA - Risco Cardíaco</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formIA">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Idade</label>
                            <input type="number" id="ia_age" class="form-control bg-light" value="<?= $idade ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Pressão Arterial Repouso</label>
                            <input type="number" id="ia_trestbps" class="form-control bg-light" value="<?= $t['pressao_sistolica'] ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Sexo Biológico</label>
                            <select id="ia_sex" class="form-select" required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="1">Masculino</option>
                                <option value="0">Feminino</option>
                            </select> 
                        </div>

                        <hr class="my-4">
                        <h6 class="text-secondary mb-3"><i class="fas fa-notes-medical me-2"></i>Dados Clínicos Complementares</h6>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tipo de Dor no Peito</label>
                            <select id="ia_cp" class="form-select" required>
                                <option value="" disabled selected>Classifique a dor...</option>
                                <option value="0">Angina Típica</option>
                                <option value="1">Angina Atípica</option>
                                <option value="2">Dor Não Anginosa</option>
                                <option value="3">Assintomático</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">ECG em Repouso</label>
                            <select id="ia_restecg" class="form-select" required>
                                <option value="0">Normal</option>
                                <option value="1">Anormalidade na onda ST-T</option>
                                <option value="2">Hipertrofia Ventricular Esquerda</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Glicemia Jejum > 120 mg/dl?</label>
                            <select id="ia_fbs" class="form-select" required>
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Angina Induzida por Exercício?</label>
                            <select id="ia_exang" class="form-select" required>
                                <option value="0">Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Inclinação do Segmento ST</label>
                            <select id="ia_slope" class="form-select" required>
                                <option value="0">Ascendente (Up-sloping)</option>
                                <option value="1">Plano (Flat)</option>
                                <option value="2">Descendente (Down-sloping)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Colesterol Sérico (mg/dl)</label>
                            <input type="number" id="ia_chol" class="form-control" min="100" max="600" placeholder="Ex: 200" required>
                            <div class="invalid-feedback">O valor deve ser entre 100 e 600.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Freq. Cardíaca Máx. Alcançada</label>
                            <input type="number" id="ia_thalach" class="form-control" min="60" max="220" placeholder="Ex: 150" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Depressão ST (Oldpeak)</label>
                            <input type="number" step="0.1" min="0" max="7" id="ia_oldpeak" class="form-control" placeholder="Ex: 1.5" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">N° Vasos Coloridos (Fluoroscopia)</label>
                            <select id="ia_ca" class="form-select" required>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Talassemia</label>
                            <select id="ia_thal" class="form-select" required>
                                <option value="1">Normal</option>
                                <option value="2">Defeito Fixo</option>
                                <option value="3">Defeito Reversível</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div id="resultadoIA" class="mt-4 p-3 rounded d-none text-center">
                    <h4 id="textoDiagnostico" class="fw-bold"></h4>
                    <p class="mb-0 fs-5">Probabilidade: <span id="textoProbabilidade" class="fw-bold"></span>%</p>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-warning fw-bold" onclick="consultarIA()">Analisar Risco</button>
            </div>
        </div>
    </div>
</div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Habilita o botão apenas se o checkbox estiver marcado
        const checkbox = document.getElementById('checkFinalizar');
        const btn = document.getElementById('btnFinalizar');
        const form = document.getElementById('formFinalizar');

        checkbox.addEventListener('change', function() {
            btn.disabled = !this.checked;
        });

        // Janela de confirmação antes de enviar
        form.onsubmit = function() {
            return confirm("ATENÇÃO: Você tem certeza que deseja finalizar? \n\nApós a confirmação, o atendimento será encerrado e as informações não poderão mais ser alteradas.");
        };
    </script>

    <script>
        async function consultarIA() {
            // 1. Coleta os dados do formulário respeitando o nome esperado pela sua API Python
            const dadosPaciente = {
                age: parseFloat(document.getElementById('ia_age').value),
                sex: parseFloat(document.getElementById('ia_sex').value),
                chest_pain_type: parseFloat(document.getElementById('ia_cp').value),
                resting_blood_pressure: parseFloat(document.getElementById('ia_trestbps').value),
                cholesterol: parseFloat(document.getElementById('ia_chol').value),
                fasting_blood_sugar: parseFloat(document.getElementById('ia_fbs').value),
                resting_electrocardiogram: parseFloat(document.getElementById('ia_restecg').value),
                max_heart_rate_achieved: parseFloat(document.getElementById('ia_thalach').value),
                exercise_induced_angina: parseFloat(document.getElementById('ia_exang').value),
                st_depression: parseFloat(document.getElementById('ia_oldpeak').value),
                st_slope: parseFloat(document.getElementById('ia_slope').value),
                num_major_vessels: parseFloat(document.getElementById('ia_ca').value),
                thalassemia: parseFloat(document.getElementById('ia_thal').value)
            };

            try {
                // 2. Faz a requisição POST para a sua API FastAPI
                const response = await fetch('http://localhost:8000/predict', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dadosPaciente)
                });

                const data = await response.json();

                if (response.ok) {
                    // 3. Mostra o resultado na tela do médico
                    const divResultado = document.getElementById('resultadoIA');
                    const txtDiag = document.getElementById('textoDiagnostico');
                    const txtProb = document.getElementById('textoProbabilidade');

                    divResultado.classList.remove('d-none', 'bg-danger', 'bg-success', 'text-white');
                    
                    txtDiag.innerText = data.diagnostico;
                    txtProb.innerText = data.probabilidade_doenca;

                    if (data.previsao_binaria === 1) {
                        divResultado.classList.add('bg-danger', 'text-white'); // Vermelho = Alerta
                    } else {
                        divResultado.classList.add('bg-success', 'text-white'); // Verde = Seguro
                    }
                } else {
                    alert('Erro da IA: ' + (data.erro || 'Erro desconhecido.'));
                }

            } catch (error) {
                alert('Erro de conexão: Verifique se a API Python está rodando na porta 8000.');
                console.error(error);
            }
        }
    </script>


</body>
</html>