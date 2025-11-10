<?php 
include('navbar.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Histórico de <?= htmlspecialchars($nome_paciente) ?> - MedAssist</title> 
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- jogar no arquivo style.css -->
    <style>
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
        .evento-diagnostico:before { background: #dc3545 !important; } 
        .evento-receita:before { background: #ffc107 !important; } 
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h2 class="mb-4">Histórico Clínico de <span class="text-primary"><?= htmlspecialchars($nome_paciente) ?></span>
            </h2>
            <hr>
            
            <!-- Verifica se existe o historico/ se a variavel está vazia, printa a mensagem -->
            <?php if (empty($historico)): ?>
                <div class="alert alert-info">
                    Nenhum registro de atendimento, exame ou diagnostico encontrado para este paciente.
                </div>

            <?php else: ?>  
                <ul class="timeline">

                <!-- looping PRINCIPAL: itera cada item/$evento dentro do array "$historico" -->
                    <?php foreach ($historico as $evento): 

                        // Adiciona um sufixo a evento ex: "evento-exame" ou "evento-diagnostico"
                        // strtolower para deixar minusculo
                        // str_replace para trocar espaço por hífene e adicionar o tipo de evento no final, ele acha o espaço em branco e adcione o "-" dai depois coloca o tipo_evento no final sintaxe: str_replace( $search, $replace, $subject );
                        $classe_css = strtolower(str_replace(' ', '-', $evento['tipo_evento']));


                        // Formata a data para o padrão brasileiro
                        $data_formatada = date('d/m/Y', strtotime($evento['data'])); //"$evento['data']" pega o valor de 'data' que vem lá do model e está no formato do Banco de dados. "strtotime" converte a string data do data base para segundos. E date('d/m/Y',...) converte para o formato brasileiro
                        $titulo_evento = $evento['tipo_evento']; 
                        $detalhes = "";

                        // Define os detalhes a serem exibidos com base no tipo
                        if ($evento['tipo_evento'] == 'Exame') {

                            // htmlspecialchars -> para evitar XSS (Cross site Scripting), que é uma vulnerabilidade de segurança. É Basicamente uma proteção para que outros usarios não insiram scripts maliciosos na pag de outros usuarios.
                            // n12br -> é responsavel pela formatação de quebras de linha, transforma enter em <br>
                            $detalhes = "Tipo: " . htmlspecialchars($evento['tipo']) . "<br>Resultado: " . nl2br(htmlspecialchars($evento['resultado']));
                        
                        } 
                        
                        elseif ($evento['tipo_evento'] == 'diagnostico') {
                            $detalhes = "CID-10: " . htmlspecialchars($evento['cid_10']) . 
                                        "<br>Descrição: " . nl2br(htmlspecialchars($evento['descricao'])) .
                                        "<br>Previsão: " . htmlspecialchars($evento['resultadoPrevisto']) . 
                                        " (Probabilidade: " . htmlspecialchars($evento['probabilidade']) . "%)";
                        
                        }


                        // Itera sobre a nova chave 'itens' para mostrar os medicamentos e suas especificações
                        elseif ($evento['tipo_evento'] == 'Receita') {
                            
                            // $detalhes agora é uma lista não ordenada
                            $detalhes = "<ul class='list-unstyled mb-0'>";

                            if (!empty($evento['itens'])) {
                                foreach ($evento['itens'] as $item) { // Percorre o array de itens da receita
                                    // ".=" faz uma concatenação, vai adcionando mais informação a variavel $detalhes
                                    $detalhes .= "<li><strong>Medicamento:</strong> " . htmlspecialchars($item['medicamento_nome']) . "</li>";
                                    $detalhes .= "<li><strong>Concentração:</strong> " . htmlspecialchars($item['concentracao']) . "</li>";
                                    $detalhes .= "<li><strong>Posologia:</strong> " . nl2br(htmlspecialchars($item['posologia'])) . "</li>";
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
                                    
                                <!--Usa Operado ternario(if-else) pra dar um destaque visual ao Diagnostico mudano a cor dependedo se for danger(verdadeiro) ou primary(falso)    -->
                                    <h5 class="card-title text-<?= ($evento['tipo_evento'] == 'diagnostico' ? 'danger' : 'primary') ?>">
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
                <a href="javascript:history.go(-1)" class="btn btn-secondary mb-5">Voltar para Pacientes</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>