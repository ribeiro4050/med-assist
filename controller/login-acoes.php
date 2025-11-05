<?php 
    session_start();
    require '../Model/conexao.php';

    // --- Função de Login Geral para Reutilização ---
    function realizarLogin($conexao, $email, $senha_digitada, $registro_profissional = null) {
        // Prepara a consulta SQL
        $email_seguro = mysqli_real_escape_string($conexao, trim($email));
        $sql = "SELECT * FROM usuarios WHERE email = '$email_seguro'";

        // Adiciona a verificação de registro profissional para o login de Admin
        if ($registro_profissional !== null) {
            $registro_seguro = mysqli_real_escape_string($conexao, trim($registro_profissional));
            // A consulta verifica se o registro está na coluna crm_registro OU coren_registro
            $sql .= " AND (crm_registro = '$registro_seguro' OR coren_registro = '$registro_seguro')";
        }
        
        $resultado = mysqli_query($conexao, $sql);

        if (mysqli_num_rows($resultado) == 1) {
            $usuario = mysqli_fetch_assoc($resultado);
            $hash_senha = $usuario['senha']; // Hash da senha armazenado no banco

            // Verifica a senha usando password_verify()
            if (password_verify($senha_digitada, $hash_senha)) {
                // Login bem-sucedido
                
                // Armazena dados importantes na sessão
                $_SESSION['logado'] = true;
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_role'] = $usuario['role']; // Papel (paciente, medico, admin, etc.)

                // Mensagem de sucesso e redirecionamento
                $_SESSION['mensagem'] = "Login realizado com sucesso! Bem-vindo(a), " . $usuario['nome'];
                header('Location: index.php');
                exit;
            } else {
                // Senha incorreta
                return "Email/Registro ou senha incorretos.";
            }
        } else {
            // Usuário não encontrado
            return "Email/Registro ou senha incorretos.";
        }
    }
    // --- Fim da Função de Login Geral ---

    // --- Ação de Login para Usuário/Paciente ---
    if(isset($_POST['login_user'])){
        $email_user = $_POST['email_user'];
        $senha_user = $_POST['senha_user'];

        $mensagem_erro = realizarLogin($conexao, $email_user, $senha_user);
        
        if ($mensagem_erro !== null) {
            // Se a função retornar uma mensagem, significa que houve erro
            $_SESSION['mensagem'] = $mensagem_erro;
            header('Location: tela-login.php');
            exit;
        }
    }

    // --- Ação de Login para Profissional/Admin ---
    if(isset($_POST['login_admin'])){
        $email_admin = $_POST['email_admin'];
        $registro_profissional_admin = $_POST['registro_profissional_admin'];
        $senha_admin = $_POST['senha_admin'];

        $mensagem_erro = realizarLogin($conexao, $email_admin, $senha_admin, $registro_profissional_admin);

        if ($mensagem_erro !== null) {
            // Se a função retornar uma mensagem, significa que houve erro
            $_SESSION['mensagem'] = $mensagem_erro;
            header('Location: tela-login.php');
            exit;
        }
    }

    // Se a página for acessada sem um botão de login ser clicado
    $_SESSION['mensagem'] = "Acesso inválido.";
    header('Location: tela-login.php');
    exit;

?>  