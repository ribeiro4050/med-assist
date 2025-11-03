<?php 
require_once 'navbar.php'; 

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de <?= htmlspecialchars($nome_paciente) ?> - MedAssist</title> 
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* ... Seu CSS da Timeline ... */
        .timeline {
            border-left: 3px solid #dee2e6;
            background: #fff;
            margin: 0 auto;
            letter-spacing: 0.2px;
            position: relative;
            padding: 50px 0;
            list-style: none;
        }
        .timeline li {
            padding-left: 20px;
            padding-right: 15px;
            margin-bottom: 20px;
            position: relative;
        }
        .timeline li:before {
            content: "";
            width: 15px;
            height: 15px;
            background: #007bff; 
            position: absolute;
            left: -9px;
            border-radius: 50%;
            top: 5px;
            z-index: 1;
            border: 2px solid #fff;
        }
        .evento-exame:before { background: #28a745 !important; } 
        .evento-diagnóstico:before { background: #dc3545 !important; } /* Atenção: use 'diagnóstico' com acento */
        .evento-receita:before { background: #ffc107 !important; } 
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h2 class="mb-4">Histórico Clínico de <span class="text-primary"><?= htmlspecialchars($nome_paciente) ?></span></h2>
            <hr>

            <?php if (empty($historico)): ?>
                <div class="alert alert-info">
                    Nenhum registro de atendimento, exame ou diagnóstico encontrado para este paciente.
                </div>
            <?php else: ?>
                <ul class="timeline">
                    <?php foreach ($historico as $evento): 
                        $classe_css = strtolower(str_replace(' ', '-', $evento['tipo_evento']));
                        // Use 'data' do Model (que é um alias)
                        $data_formatada = date('d/m/Y', strtotime($evento['data'])); 
                        $titulo_evento = $evento['tipo_evento'];
                        $detalhes = "";

                        // Define os detalhes a serem exibidos com base no tipo
                        if ($evento['tipo_evento'] == 'Exame') {
                            $detalhes = "Tipo: " . htmlspecialchars($evento['tipo']) . "<br>Resultado: " . nl2br(htmlspecialchars($evento['resultado']));
                        
                        } elseif ($evento['tipo_evento'] == 'Diagnóstico') {
                            $detalhes = "CID-10: " . htmlspecialchars($evento['cid_10']) . 
                                        "<br>Descrição: " . nl2br(htmlspecialchars($evento['descricao'])) .
                                        "<br>Previsão: " . htmlspecialchars($evento['resultadoPrevisto']) . 
                                        " (Probabilidade: " . htmlspecialchars($evento['probabilidade']) . "%)";
                        
                        } elseif ($evento['tipo_evento'] == 'Receita') {
                            // CORREÇÃO: Itera sobre a nova chave 'itens' para mostrar os medicamentos
                            $detalhes = "<ul class='list-unstyled mb-0'>";
                            if (!empty($evento['itens'])) {
                                foreach ($evento['itens'] as $item) {
                                    $detalhes .= "<li><strong>Medicamento:</strong> " . htmlspecialchars($item['medicamento_nome']) . "</li>";
                                    $detalhes .= "<li><strong>Concentração:</strong> " . htmlspecialchars($item['concentracao']) . "</li>";
                                    $detalhes .= "<li><strong>Posologia:</strong> " . nl2br(htmlspecialchars($item['posologia'])) . "</li><hr class='my-1'>";
                                }
                            } else {
                                $detalhes .= "<li>Nenhum item encontrado para esta receita.</li>";
                            }
                            $detalhes .= "</ul>";
                        }
                    ?>
                        <li class="timeline-item evento-<?= $classe_css ?>">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-<?= ($evento['tipo_evento'] == 'Diagnóstico' ? 'danger' : 'primary') ?>">
                                        <?= $titulo_evento ?> 
                                        <small class="text-muted float-right">
                                            <span class="badge badge-secondary"><?= $data_formatada ?></span>
                                        </small>
                                    </h5>
                                    <p class="card-text mb-0">
                                        <?= $detalhes ?>
                                    </p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="lista-pacientes.php" class="btn btn-secondary">Voltar para Pacientes</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>