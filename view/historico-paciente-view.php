<?php
/**
 * Variáveis injetadas pelo historico-paciente-controller.php
 * @var string $nome_paciente
 * @var array $historico
 */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de <?= htmlspecialchars($nome_paciente) ?> - MedAssist</title> 
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        .timeline { border-left: 3px solid #dee2e6; background: #fff; margin: 0 auto; letter-spacing: 0.2px; position: relative; padding: 50px 0; list-style: none; }
        .timeline li { padding-left: 20px; padding-right: 15px; margin-bottom: 20px; position: relative; }
        .timeline li:before { content: ""; width: 15px; height: 15px; background: #007bff; position: absolute; left: -9px; border-radius: 50%; top: 5px; z-index: 1; border: 2px solid #fff; }
        .evento-exame:before { background: #28a745 !important; } 
        .evento-diagnostico:before { background: #dc3545 !important; } 
        .evento-receita:before { background: #ffc107 !important; } 
    </style>
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h2 class="mb-4">Histórico Clínico de <span class="text-primary"><?= htmlspecialchars($nome_paciente) ?></span></h2>
            <hr>
            
            <?php if (empty($historico)): ?>
                <div class="alert alert-info">Nenhum registro de atendimento, exame ou diagnostico encontrado para este paciente.</div>
            <?php else: ?>  
                <ul class="timeline">
                <?php foreach ($historico as $evento): 
                        $classe_css = strtolower(str_replace(' ', '-', $evento['tipo_evento']));
                        $data_formatada = date('d/m/Y', strtotime($evento['data'])); 
                        $titulo_evento = $evento['tipo_evento']; 
                        $detalhes = "";

                        if ($evento['tipo_evento'] == 'Exame') {
                            $detalhes = "Tipo: " . htmlspecialchars($evento['tipo']) . "<br>Resultado: " . nl2br(htmlspecialchars($evento['resultado']));
                        } elseif ($evento['tipo_evento'] == 'diagnostico') {
                            $cid = htmlspecialchars($evento['cid_10'] ?? 'N/A');
                            $desc = nl2br(htmlspecialchars($evento['descricao'] ?? 'Sem descrição'));
                            $detalhes = "<strong>CID-10:</strong> " . $cid . "<br><strong>Descrição:</strong> " . $desc;
                        } elseif ($evento['tipo_evento'] == 'Receita') {
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
                                    <h5 class="card-title d-flex justify-content-between align-items-center text-<?= ($evento['tipo_evento'] == 'diagnostico' ? 'danger' : 'primary') ?>">
                                        <?= $titulo_evento ?> 
                                        <span class="badge bg-secondary text-white" style="font-size: 0.8rem;">
                                            <i class="bi bi-calendar3"></i> <?= $data_formatada ?>
                                        </span>
                                    </h5>
                                    <p class="card-text mb-0"><?= $detalhes ?></p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="javascript:history.go(-1)" class="btn btn-secondary mb-5">Voltar</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>