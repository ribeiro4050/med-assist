<?php
session_start();
// Caminhos ajustados para a estrutura do seu TCC (med-assist/controller/...)
require_once '../Model/conexao.php'; 
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['id'])) {
    // Usa sua função de segurança que já existe no conexao.php
    $id_receita = filtrar_sql($_GET['id']); 

    try {
        // 1. Busca dados do Paciente e da Receita no medassistdb
        $sql = "SELECT r.*, u.nome as paciente_nome, u.email as paciente_email 
                FROM receitas r 
                JOIN usuarios u ON r.paciente_id = u.id 
                WHERE r.id = '$id_receita'";
        
        $resultado = mysqli_query($conexao, $sql);
        $receita = mysqli_fetch_assoc($resultado);

        if (!$receita) {
            $_SESSION['mensagem'] = "Receita não encontrada ou dados do paciente incompletos.";
            header("Location: ../view/receitas.php");
            exit;
        }

        // 2. Busca os Itens (Medicamentos) dessa receita específica
        $sql_itens = "SELECT * FROM itens_receita WHERE receita_id = '$id_receita'";
        $itens_resultado = mysqli_query($conexao, $sql_itens);

        // 3. Monta o Corpo do E-mail em HTML (Didática visual limpa)
        $corpoHTML = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
            <div style='background-color: #0d6efd; color: white; padding: 20px; text-align: center;'>
                <h1 style='margin: 0;'>MedAssist</h1>
                <p style='margin: 0;'>Suporte à Decisão Clínica</p>
            </div>
            <div style='padding: 20px;'>
                <p>Olá, <b>{$receita['paciente_nome']}</b>,</p>
                <p>Sua receita médica digital já está disponível. Veja abaixo os detalhes da prescrição:</p>
                
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <thead>
                        <tr style='background-color: #f8f9fa;'>
                            <th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Medicamento</th>
                            <th style='border: 1px solid #dee2e6; padding: 12px; text-align: left;'>Posologia</th>
                        </tr>
                    </thead>
                    <tbody>";

        while ($item = mysqli_fetch_assoc($itens_resultado)) {
            $corpoHTML .= "
                <tr>
                    <td style='border: 1px solid #dee2e6; padding: 12px;'>
                        <b>{$item['medicamento_nome']}</b><br>
                        <small style='color: #666;'>{$item['concentracao']}</small>
                    </td>
                    <td style='border: 1px solid #dee2e6; padding: 12px;'>{$item['posologia']}</td>
                </tr>";
        }

        $corpoHTML .= "
                    </tbody>
                </table>
                
                <p><b>Tipo de Receita:</b> " . ucfirst($receita['tipo_receita']) . "</p>
                <p><b>Data de Emissão:</b> " . date('d/m/Y', strtotime($receita['data_prescricao'])) . "</p>
                
                <div style='margin-top: 30px; padding: 15px; background-color: #fff3cd; border-radius: 5px; color: #856404;'>
                    <small><b>Aviso:</b> Lembre-se de seguir as orientações médicas e não se automedicar.</small>
                </div>
            </div>
            <div style='background-color: #f1f1f1; color: #777; padding: 15px; text-align: center; font-size: 12px;'>
                Este é um e-mail automático do sistema MedAssist desenvolvido por Luiz Otávio e Gustavo Ribeiro.
            </div>
        </div>";

        // 4. Configuração do PHPMailer com as credenciais validadas
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'med.assist3501@gmail.com';
        $mail->Password   = 'nwug vfal occm ceba'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Destinatários
        $mail->setFrom('med.assist3501@gmail.com', 'Sistema MedAssist');
        $mail->addAddress($receita['paciente_email'], $receita['paciente_nome']);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = "Sua Prescrição Médica - MedAssist (#{$id_receita})";
        $mail->Body    = $corpoHTML;

        $mail->send();
        
        $_SESSION['mensagem'] = "Receita enviada com sucesso para {$receita['paciente_nome']}!";
        header("Location: ../view/receitas.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['mensagem'] = "Não foi possível enviar o e-mail. Erro: {$mail->ErrorInfo}";
        header("Location: ../view/receitas.php");
        exit;
    }
} else {
    header("Location: ../view/receitas.php");
    exit;
}