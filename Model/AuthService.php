<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class AuthService {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function autenticar($email, $registro, $senha) {
        $sql = "";

        if (!empty($email)) {
            $email_limpo = mysqli_real_escape_string($this->db, trim($email));
            $sql = "SELECT * FROM usuarios WHERE email = '$email_limpo'";
        } else if (!empty($registro)) {
            $reg_limpo = mysqli_real_escape_string($this->db, $registro);
            $sql = "SELECT * FROM usuarios WHERE crm_registro = '$reg_limpo' OR coren_registro = '$reg_limpo'";
        } else {
            return ['sucesso' => false, 'erro' => "Preencha o Email ou o Registro."];
        }

        $query = mysqli_query($this->db, $sql);

        if (mysqli_num_rows($query) == 1) {
            $usuario = mysqli_fetch_assoc($query);

            if (password_verify($senha, $usuario['senha'])) {
                if (!empty($registro) && !in_array($usuario['role'], ['medico', 'enfermeiro', 'admin'])) {
                    return ['sucesso' => false, 'erro' => "Acesso negado para este registro."];
                }
                return ['sucesso' => true, 'dados' => $usuario];
            }
        }

        return ['sucesso' => false, 'erro' => "Usuário e/ou senha inválidos."];
    }

    public function solicitarRecuperacao($email) {
        $email_seguro = mysqli_real_escape_string($this->db, trim($email));
        $query = mysqli_query($this->db, "SELECT id FROM usuarios WHERE email = '$email_seguro'");
        
        if (mysqli_num_rows($query) > 0) {
            $codigo = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $expiracao = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            mysqli_query($this->db, "UPDATE recuperacao_senha SET usado = 1 WHERE email = '$email_seguro'");
            $sql = "INSERT INTO recuperacao_senha (email, codigo, data_expiracao) VALUES ('$email_seguro', '$codigo', '$expiracao')";
            
            if (mysqli_query($this->db, $sql)) {
                // CORREÇÃO: Chama o próprio método interno da classe, sem passar pelo Controller
                if ($this->enviarEmailRecuperacao($email_seguro, $codigo)) {
                    $_SESSION['mensagem'] = "Código enviado com sucesso!";
                    $_SESSION['email_recuperacao'] = $email_seguro;
                    return true;
                } else {
                    $_SESSION['mensagem'] = "Erro ao enviar e-mail. Verifique suas credenciais.";
                    return false;
                }
            }
        } else {
            $_SESSION['mensagem'] = "E-mail não encontrado.";
            return false;
        }
        return false;
    }

    public function validarCodigoRecuperacao($email, $codigo_digitado) {
        $email_seguro = mysqli_real_escape_string($this->db, trim($email));
        $codigo_seguro = mysqli_real_escape_string($this->db, trim($codigo_digitado));
        $agora = date('Y-m-d H:i:s');
        
        $sql = "SELECT * FROM recuperacao_senha 
                WHERE email = '$email_seguro' AND codigo = '$codigo_seguro' 
                AND usado = 0 AND data_expiracao > '$agora' LIMIT 1";

        if (mysqli_num_rows(mysqli_query($this->db, $sql)) > 0) {
            $_SESSION['pode_mudar_senha'] = true;
            return true;
        } else {
            $_SESSION['mensagem'] = "Código inválido ou expirado.";
            return false;
        }
    }

    public function atualizarSenhaEsquecida($email, $nova_senha) {
        $email_seguro = mysqli_real_escape_string($this->db, trim($email));
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        if (mysqli_query($this->db, "UPDATE usuarios SET senha = '$hash' WHERE email = '$email_seguro'")) {
            mysqli_query($this->db, "UPDATE recuperacao_senha SET usado = 1 WHERE email = '$email_seguro'");
            unset($_SESSION['email_recuperacao'], $_SESSION['pode_mudar_senha']);
            $_SESSION['mensagem'] = "Senha Atualizada! Faça login.";
            return true;
        }
        return false;
    }

    public function enviarEmailRecuperacao($email, $codigo) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'med.assist3501@gmail.com';
            $mail->Password   = 'nwug vfal occm ceba'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setLanguage('pt_br');
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('med.assist3501@gmail.com', 'MedAssist Support');
            $mail->addAddress($email);

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
            return false;
        }
    }
}