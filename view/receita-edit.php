<?php 
    session_start();
    require '../Model/conexao.php';

    // Garante que o usuário esteja logado e que o ID da receita foi passado
    if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_GET['id'])) {
        header('Location: login.php');
        exit;
    }

    $receita_id = mysqli_real_escape_string($conexao, $_GET['id']);
    $usuario_id = $_SESSION['id_usuario'];
    $role = $_SESSION['role_usuario'];
    
    // consulta para buscar os dados da receita
    $sql_receita = "SELECT * FROM receitas WHERE id = $receita_id";
    $query_receita = mysqli_query($conexao, $sql_receita);

    if(mysqli_num_rows($query_receita) == 0) {
        $_SESSION['mensagem'] = "Receita não encontrada.";
        header('Location: receitas.php');
        exit;
    }

    $receita = mysqli_fetch_array($query_receita);

    // por via de regra, um médico só pode editar suas receitas
    if ($role !== 'admin' && $receita['medico_id'] != $usuario_id) {
        $_SESSION['mensagem'] = "Acesso negado. Você só pode editar suas próprias receitas.";
        header('Location: receitas.php');
        exit;
    }
    
    // consulta para buscar todos os pacientes 
    $sql_pacientes = "SELECT id, nome FROM usuarios WHERE role = 'paciente' ORDER BY nome ASC";
    $pacientes = mysqli_query($conexao, $sql_pacientes);

    // consulta para buscar os itens da receita
    $sql_itens = "SELECT * FROM itens_receita WHERE receita_id = $receita_id";
    $query_itens = mysqli_query($conexao, $sql_itens);
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Receita #<?= $receita_id ?> - MedAssist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  </head>
  <body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5 mb-5">
      <?php include('../controller/mensagem.php')?>
      
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card shadow">
            <div class="card-header bg-success text-white">
              <h4 class="mb-0">
                <span class="bi-pencil-fill"></span> Editando Receita #<?= $receita_id ?>
                <a href="receitas.php" class="btn btn-light float-end">Voltar</a>
              </h4>
            </div>
            <div class="card-body">
              
              <form action="../controller/acoes.php" method="POST">
                
                <input type="hidden" name="receita_id" value="<?= $receita_id ?>">
                <input type="hidden" name="medico_id" value="<?= $receita['medico_id'] ?>">

                <h5 class="mt-3 border-bottom pb-2">Informações Gerais</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="paciente_id" class="form-label">Paciente</label>
                        <select name="paciente_id" id="paciente_id" class="form-select" required>
                            <option value="">-- Selecione o Paciente --</option>
                            <?php 
                            mysqli_data_seek($pacientes, 0); // Volta o ponteiro da consulta ao início para ler todos os dados novamente caso ja tenha sido lida na lista de pacientes
                               // o mysqli_fetch_assoc retorna um array associativo com os dados da consulta e pula para a próxima linha
                            while($paciente = mysqli_fetch_assoc($pacientes)): 
                                $selected = ($paciente['id'] == $receita['paciente_id']) ? 'selected' : '';
                            ?>
                                <option value="<?= $paciente['id'] ?>" <?= $selected ?>><?= $paciente['nome'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="tipo_receita" class="form-label">Tipo de Receita</label>
                        <select name="tipo_receita" id="tipo_receita" class="form-select" required>
                            <?php $tipos = ['Simples', 'Controle Especial', 'Amarela', 'Azul']; ?>
                            <?php foreach($tipos as $tipo): ?>
                                <option value="<?= $tipo ?>" <?= ($tipo == $receita['tipo_receita'] ? 'selected' : '') ?>>
                                    <?= $tipo ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="observacoes" class="form-label">Observações Médicas (Opcional)</label>
                    <textarea name="observacoes" id="observacoes" rows="3" class="form-control"><?= htmlspecialchars($receita['observacoes'] ?? '') ?></textarea>
                </div>
                
                <h5 class="mt-4 border-bottom pb-2">Medicamentos (Itens)</h5>

                <div id="itens-container">
                    <?php 
                    $item_count = mysqli_num_rows($query_itens);
                    
                    if($item_count > 0): 
                        while($item = mysqli_fetch_array($query_itens)):
                    ?>
                    <div class="item-receita border p-3 mb-3 rounded">
                        <input type="hidden" name="item_id[]" value="<?= $item['id'] ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="medicamento_nome" class="form-label">Nome do Medicamento</label>
                                <input type="text" name="medicamento_nome[]" value="<?= htmlspecialchars($item['medicamento_nome']) ?>" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="concentracao" class="form-label">Concentração e Forma</label>
                                <input type="text" name="concentracao[]" value="<?= htmlspecialchars($item['concentracao']) ?>" class="form-control" placeholder="Ex: 500mg Comprimido" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="quantidade_total" class="form-label">Quantidade Total</label>
                            <input type="text" name="quantidade_total[]" value="<?= htmlspecialchars($item['quantidade_total']) ?>" class="form-control" placeholder="Ex: 30 comprimidos" required>
                        </div>
                        <div class="mb-3">
                            <label for="posologia" class="form-label">Posologia (Modo de Uso)</label>
                            <textarea name="posologia[]" rows="2" class="form-control" placeholder="Ex: Tomar 1 comprimido a cada 8 horas por 7 dias." required><?= htmlspecialchars($item['posologia']) ?></textarea>
                        </div>
                        
                        <div class="remover-btn-espaco mt-3 clearfix">
                            <button type="button" class="btn btn-danger btn-sm remove-item-btn float-end" onclick="this.closest('.item-receita').remove()">
                                <span class="bi-trash3-fill"></span> Remover Item
                            </button>
                        </div> 
                    </div>
                    <?php 
                        endwhile;
                    else: 
                        // Se não houver itens, exibe um item vazio (q nem no CREATE)
                    ?>
                    <div class="item-receita border p-3 mb-3 rounded">
                         <input type="hidden" name="item_id[]" value=""> 
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
                    <?php endif; ?>
                </div>

                <button type="button" id="add-item-btn" class="btn btn-sm btn-outline-secondary mb-4">
                    <span class="bi-plus-circle"></span> Adicionar Outro Item
                </button>

                <div class="d-grid gap-2">
                    <button type="submit" name="update_receita" class="btn btn-success btn-lg">
                        <span class="bi-save"></span> Salvar Alterações
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
        // Cria um template limpo para novos itens
        const templateHTML = `
            <div class="item-receita border p-3 mb-3 rounded">
                <input type="hidden" name="item_id[]" value=""> <div class="row">
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
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn float-end" onclick="this.closest('.item-receita').remove()">
                        <span class="bi-trash3-fill"></span> Remover Item
                    </button>
                </div> 
            </div>
        `;
        container.insertAdjacentHTML('beforeend', templateHTML);
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>