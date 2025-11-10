<?php 
    session_start();
    require '../Model/conexao.php';

    // Verificação de acesso
    // Verifica se o usuário está logado e se é médico (Comentar para manutenção)
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['role_usuario'] !== 'medico') {
        $_SESSION['mensagem'] = "Acesso negado. Apenas médicos podem criar receituários.";
        header('Location: index.php');
        exit;
    }

      // O ID do médico logado será usado para filtrar as receitas
    $medico_id = $_SESSION['id_usuario'];

    //BUSCAR LISTA DE PACIENTES

    // O médico precisa de uma lista de usuários com a role 'paciente' para selecionar.

    // sql_pacientes busca os pacientes do banco de dados por nome e id e $pacientes armazena o resultado da consulta em ordem alfabética..
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  </head>
  <body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5 mb-5">
      <?php include('../view/mensagem.php')?>
      
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card shadow">
            <div class="card-header bg-primary text-white">
              <h4 class="mb-0">
                <span class="bi-file-earmark-medical"></span> Emitir Nova Receita
                <a href="receitas.php" class="btn btn-light float-end">Voltar</a>
              </h4>
            </div>
            <div class="card-body">
              
              <form action="../controller/acoes.php" method="POST">
                
                <h5 class="mt-3 border-bottom pb-2">Informações Gerais</h5>
                
                <input type="hidden" name="medico_id" value="<?= $medico_id ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="paciente_id" class="form-label">Paciente</label>

                        <!-- o name = "paciente_id" sera enviado ao acoes.php  -->
                        <select name="paciente_id" id="paciente_id" class="form-select" required>

                            <option value="">-- Selecione o Paciente --</option>
                            <!-- percorre todos os resultados da consulta -->
                            <?php while($paciente = mysqli_fetch_assoc($pacientes)): ?>
                                <!-- Dentro do loop, este código gera uma opção para cada paciente, o value é o id do paciente, e o texto visivel o nome -->
                                <option value="<?= $paciente['id'] ?>"><?= $paciente['nome'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tipo_receita" class="form-label">Tipo de Receita</label>
                        <select name="tipo_receita" id="tipo_receita" class="form-select" required>
                            <option value="Simples">Branca Simples</option>
                            <option value="Controle Especial">Branca Especial </option>
                            <option value="Amarela">Amarela (Entorpecentes)</option>
                            <option value="Azul">Azul (Psicotrópicos)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="observacoes" class="form-label">Observações Médicas (Opcional)</label>
                    <textarea name="observacoes" id="observacoes" rows="3" class="form-control"></textarea>
                </div>
                
                <h5 class="mt-4 border-bottom pb-2">Medicamentos (Itens)</h5>

                <div id="itens-container">
                    <div class="item-receita border p-3 mb-3 rounded">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="medicamento_nome" class="form-label">Nome do Medicamento</label>
                                <input type="text" name="medicamento_nome[]" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="concentracao" class="form-label">Concentração e Forma</label>
                                <input type="text" name="concentracao[]" class="form-control" placeholder="Ex: 500mg Comprimido, 10mg/ml Gotas" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="quantidade_total" class="form-label">Quantidade Total</label>
                            <input type="text" name="quantidade_total[]" class="form-control" placeholder="Ex: 30 comprimidos, 1 frasco de 20ml" required>
                        </div>
                        <div class="mb-3">
                            <label for="posologia" class="form-label">Posologia (Modo de Uso)</label>
                            <textarea name="posologia[]" rows="2" class="form-control" placeholder="Ex: Tomar 1 comprimido a cada 8 horas por 7 dias." required></textarea>
                        </div>
                        <div class="remover-btn-espaco mt-3 clearfix">
                            </div>
                    </div>
                </div>

                <button type="button" id="add-item-btn" class="btn btn-sm btn-outline-secondary mb-4">
                    <span class="bi-plus-circle"></span> Adicionar Outro Item
                </button>

                <div class="d-grid gap-2">
                    <button type="submit" name="create_receita" class="btn btn-success btn-lg">
                        <span class="bi-save"></span> Prescrever e Salvar
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
        removeBtnEspaco.innerHTML = ''; // Garante que o espaço está limpo antes de adicionar o botão
        
        // Cria o botão de remover
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        // float-end para alinhar à direita dentro da div 'remover-btn-espaco'
        removeBtn.className = 'btn btn-danger btn-sm remove-item-btn float-end'; 
        removeBtn.innerHTML = '<span class="bi-trash3-fill"></span> Remover Item';
        
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>