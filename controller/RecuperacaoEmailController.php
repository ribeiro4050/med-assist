<?php
// Importa as classes do PHPMailer (ajuste o caminho se sua pasta vendor estiver em outro lugar)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Caminho padrão se você instalou via Composer

class RecuperacaoEmailController {
    public static function enviarCodigo($email, $codigo) {
        $mail = new PHPMailer(true);

        try {
            // --- CONFIGURAÇÃO DO SERVIDOR (Igual ao seu EnviarReceitaController) ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Ou o host do seu provedor
            $mail->SMTPAuth   = true;
            $mail->Username   = 'med.assist3501@gmail.com'; // Seu e-mail
            $mail->Password   = 'nwug vfal occm ceba'; // Aquela senha de 16 dígitos do Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setLanguage('pt_br');
            $mail->CharSet = 'UTF-8';

            // --- REMETENTE E DESTINATÁRIO ---
            $mail->setFrom('med.assist3501@gmail.com', 'MedAssist Support');
            $mail->addAddress($email);

            // --- CONTEÚDO DO E-MAIL ---
            $mail->isHTML(true);
            $mail->Subject = "Código de Recuperação - MedAssist";
            
            $mail->Body = "
                <html>
                <body style='font-family: Arial, sans-serif; color: #333;'>
                    <div style='max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px;'>
                        <h2 style='color: #0d6efd; text-align: center;'>MedAssist</h2>
                        <p>Olá,</p>
                        <p>Recebemos uma solicitação para redefinir a senha da sua conta no <strong>MedAssist</strong>.</p>
                        <p>Utilize o código abaixo para validar o seu acesso:</p>
                        <div style='background: #f8f9fa; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; border: 1px solid #ddd; letter-spacing: 5px; color: #0d6efd;'>
                            $codigo
                        </div>
                        <p style='margin-top: 20px;'>Este código é válido por <strong>15 minutos</strong>.</p>
                        <hr style='border: 0; border-top: 1px solid #eee;'>
                        <p style='font-size: 12px; color: #777;'>Se você não solicitou a alteração de senha, ignore este e-mail por segurança.</p>
                    </div>
                </body>
                </html>
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Você pode registrar o erro em um log aqui se quiser: $e->getMessage()
            return false;
        }
    }
}