<?php 
    session_start();
    require '../Model/conexao.php';

    // Verificação de acesso
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['role_usuario'] !== 'medico') {
        $_SESSION['mensagem'] = "Acesso negado. Apenas médicos podem criar receituários.";
        header('Location: index.php');
        exit;
    }

      // O ID do médico logado será usado para filtrar as receitas
    $medico_id = $_SESSION['id_usuario'];

    // --- LÓGICA DE CAPTURA DO HUB ---
    // Captura os IDs enviados via URL pelo Hub de Atendimento
    $paciente_selecionado = $_GET['paciente_id'] ?? '';
    $triagem_id = $_GET['triagem_id'] ?? '';

    // BUSCAR LISTA DE PACIENTES
    $sql_pacientes = "SELECT id, nome FROM usuarios WHERE role = 'paciente' ORDER BY nome ASC";
    $pacientes = mysqli_query($conexao, $sql_pacientes);

    if (mysqli_num_rows($pacientes) == 0) {
        // Alerta se não houver pacientes cadastrados
        $_SESSION['mensagem'] = "Não há pacientes cadastrados no sistema para emitir uma receita.";
        header('Location: receitas.php');
        exit;
    }
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nova Receita - MedAssist</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; }
        .card-header { border-radius: 15px 15px 0 0 !important; }
        .item-receita { background-color: #fff; transition: all 0.2s; }
        .item-receita:hover { border-color: #0d6efd !important; }
    </style>
  </head>
  <body>
    <?php include('navbar.php'); ?>
    
    <div class="container mt-5 mb-5">
      <?php include('../view/mensagem.php')?>
      
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card shadow border-0">
            <div class="card-header bg-primary text-white py-3">
              <h4 class="mb-0 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-file-earmark-medical me-2"></i>Emitir Nova Receita</span>
                <a href="javascript:history.go(-1)" class="btn btn-light btn-sm fw-bold px-3 rounded-pill">Voltar</a>
              </h4>
            </div>
            <div class="card-body p-4">
              
              <form action="../controller/ReceitaController.php" method="POST">
                
                <h5 class="mb-4 text-primary fw-bold"><i class="bi bi-person-lines-fill me-2"></i>Informações Gerais</h5>
                
                <input type="hidden" name="medico_id" value="<?= $medico_id ?>">
                <input type="hidden" name="triagem_id" value="<?= $triagem_id ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="paciente_id" class="form-label fw-bold">Paciente</label>
                        <select name="paciente_id" id="paciente_id" class="form-select form-select-lg shadow-sm" required>
                            <option value="">-- Selecione o Paciente --</option>
                            <!-- percorre todos os resultados da consulta -->
                            <?php while($paciente = mysqli_fetch_assoc($pacientes)): ?>
                                <option value="<?= $paciente['id'] ?>" <?= ($paciente['id'] == $paciente_selecionado) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($paciente['nome']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tipo_receita" class="form-label fw-bold">Tipo de Receita</label>
                        <select name="tipo_receita" id="tipo_receita" class="form-select form-select-lg shadow-sm" required>
                            <option value="Simples">Branca Simples</option>
                            <option value="Controle Especial">Branca Especial </option>
                            <option value="Amarela">Amarela (Entorpecentes)</option>
                            <option value="Azul">Azul (Psicotrópicos)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="observacoes" class="form-label fw-bold">Observações Médicas (Opcional)</label>
                    <textarea name="observacoes" id="observacoes" rows="3" class="form-control shadow-sm" placeholder="Ex: Repouso relativo, dieta leve..."></textarea>
                </div>
                
                <h5 class="mt-5 mb-4 text-primary fw-bold border-bottom pb-2"><i class="bi bi-capsule me-2"></i>Medicamentos (Itens)</h5>



                <div id="itens-container">
                    <div class="item-receita border p-4 mb-4 rounded-4 shadow-sm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nome do Medicamento</label>
                                <input type="text" name="medicamento_nome[]" class="form-control" placeholder="Ex: Paracetamol" required> 
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Concentração e Forma</label>
                                <input type="text" name="concentracao[]" class="form-control" placeholder="Ex: 500mg Comprimido" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-success">Início do Tratamento</label>
                                <input type="date" name="data_inicio[]" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-danger">Fim do Tratamento (Previsão)</label>
                                <input type="date" name="data_fim[]" class="form-control" required>
                                <small class="text-muted">Para uso contínuo, coloque uma data distante (ex: 1 ano).</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantidade Total</label>
                            <input type="text" name="quantidade_total[]" class="form-control" placeholder="Ex: 30 comprimidos" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Posologia (Modo de Uso)</label>
                            <textarea name="posologia[]" rows="2" class="form-control" placeholder="Ex: Tomar 1 comprimido a cada 8 horas." required></textarea>
                        </div>
                        <div class="remover-btn-espaco mt-2">
                        </div>
                    </div>
                </div>



                <button type="button" id="add-item-btn" class="btn btn-outline-primary mb-5 rounded-pill px-4">
                    <i class="bi bi-plus-circle me-2"></i>Adicionar Outro Medicamento
                </button>

                <div class="d-grid">
                    <button type="submit" name="create_receita" class="btn btn-success btn-lg py-3 rounded-pill shadow">
                        <i class="bi bi-check2-circle me-2"></i>Prescrever e Salvar Receituário
                    </button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <script>
    document.getElementById('add-item-btn').addEventListener('click', function() {
        const container = document.getElementById('itens-container');

        // configura o primeiro item para que não tenha o botao de remover
        const firstItem = container.querySelector('.item-receita');
        
        // Clona o primeiro template.
        const newItem = firstItem.cloneNode(true);
        
        // Limpa os valores dos campos clonados
        newItem.querySelectorAll('input, textarea').forEach(input => {
            input.value = '';
        });

        // Limpa o espaço reservado e adiciona o botão de remover
        let removeBtnEspaco = newItem.querySelector('.remover-btn-espaco');
        removeBtnEspaco.innerHTML = ''; 
        
        // Cria o botão de remover
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm float-end'; 
        removeBtn.innerHTML = '<i class="bi bi-trash3 me-1"></i> Remover Item';
        
        // Adiciona a funcionalidade de remover
        removeBtn.addEventListener('click', function() {
            // Remove o item pai (item-receita)
            newItem.remove(); 
        });

        // Adiciona o botão dentro do espaço reservado
        removeBtnEspaco.appendChild(removeBtn);
        
        // Adiciona o novo item ao container
        container.appendChild(newItem);
    });
    </script>
    </script>
  </body>
</html>r