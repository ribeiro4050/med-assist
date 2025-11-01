<?php

    session_start();
    require '../Model/conexao.php'; // Inclui a conexão com o banco de dados

    // --- 1. LÓGICA DE CRIAÇÃO (CREATE) ---
    if(isset($_POST['create_usuario'])){
        // mysqli_real_escape_string e trim previnem SQL Injection e removem espaços
        $nome = mysqli_real_escape_string($conexao, trim($_POST['nome']));
        $email = mysqli_real_escape_string($conexao, trim($_POST['email']));
        $data_nascimento = mysqli_real_escape_string($conexao, trim($_POST['data_nascimento']));
        
        // Criptografia da Senha com um hash forte
        $senha_pura = trim($_POST['senha']);
        $senha_hash = !empty($senha_pura) ? mysqli_real_escape_string($conexao, password_hash($senha_pura, PASSWORD_DEFAULT)) : '';
        
        // Novos campos para role e registro (assumindo 'paciente' como default se não for especificado)
        $role = mysqli_real_escape_string($conexao, trim($_POST['role'] ?? 'paciente'));
        $crm_registro = mysqli_real_escape_string($conexao, trim($_POST['crm_registro'] ?? ''));
        $coren_registro = mysqli_real_escape_string($conexao, trim($_POST['coren_registro'] ?? ''));

        // Validação básica para roles de profissionais
        if (($role == 'medico' && empty($crm_registro)) || ($role == 'enfermeiro' && empty($coren_registro))) {
            $_SESSION['mensagem'] = "O registro (CRM/COREN) é obrigatório para o role selecionado.";
            header('Location: ../view/usuario-create.php'); 
            exit;
        }

        $sql = "INSERT INTO usuarios (nome, email, data_nascimento, senha, role, crm_registro, coren_registro) 
                VALUES ('$nome', '$email', '$data_nascimento', '$senha_hash', '$role', ";
        
        // Adiciona valores de registro de forma segura, tratando NULL se vazio
        $sql .= empty($crm_registro) ? "NULL, " : "'$crm_registro', ";
        $sql .= empty($coren_registro) ? "NULL)" : "'$coren_registro')";

        $query = mysqli_query($conexao, $sql);

        if($query) { 
            $_SESSION['mensagem'] = "Usuário criado com sucesso";
            header('Location: ../view/index.php'); 
            exit;
        } else {
            $_SESSION['mensagem'] = "Usuário não foi criado. Erro: " . mysqli_error($conexao);
            header('Location: ../view/usuario-create.php');
            exit;
        }
    }


    // --- 2. LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
    if(isset($_POST['update_usuario'])){
        $usuario_id = mysqli_real_escape_string($conexao, $_POST['usuario_id']);
        $nome = mysqli_real_escape_string($conexao, trim($_POST['nome']));
        $email = mysqli_real_escape_string($conexao, trim($_POST['email']));
        $data_nascimento = mysqli_real_escape_string($conexao, trim($_POST['data_nascimento']));
        $senha = mysqli_real_escape_string($conexao, trim($_POST['senha']));
        $role = mysqli_real_escape_string($conexao, trim($_POST['role'] ?? ''));
        $crm_registro = mysqli_real_escape_string($conexao, trim($_POST['crm_registro'] ?? ''));
        $coren_registro = mysqli_real_escape_string($conexao, trim($_POST['coren_registro'] ?? ''));


        $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', data_nascimento = '$data_nascimento'";
        
        if(!empty($role)) {
            $sql .= ", role = '$role'";
        }
        
        // Atualiza campos de registro se o role ou o campo for alterado (trata NULL se vazio)
        if ($role == 'medico' || $role == 'enfermeiro' || !empty($crm_registro) || !empty($coren_registro)) {
             $crm_value = empty($crm_registro) ? "NULL" : "'$crm_registro'";
             $coren_value = empty($coren_registro) ? "NULL" : "'$coren_registro'";
             $sql .= ", crm_registro = $crm_value, coren_registro = $coren_value";
        }

         if(!empty($senha)){
            // Se a senha for fornecida, cria um novo hash
            $sql .= ", senha = '". password_hash($senha, PASSWORD_DEFAULT) . "'";
         }
           
        $sql .= " WHERE id = $usuario_id";

        mysqli_query($conexao, $sql);

        if(mysqli_affected_rows($conexao) > 0){ 
            $_SESSION['mensagem'] = "Usuário atualizado com sucesso";
            header('Location: ../view/index.php'); 
            exit;
        } else {
            $_SESSION['mensagem'] = "Nenhuma alteração feita ou Usuário não foi encontrado/atualizado. Erro: " . mysqli_error($conexao);
            header('Location: ../view/index.php');
            exit;
        }
    }

    // --- 3. LÓGICA DE EXCLUSÃO (DELETE) ---
    if(isset($_POST['delete_usuario'])){
        $usuario_id = mysqli_real_escape_string($conexao, $_POST['delete_usuario']);
        
        $sql = "DELETE FROM usuarios WHERE id = '$usuario_id'";

        mysqli_query($conexao, $sql);

        if(mysqli_affected_rows($conexao) > 0) {
            $_SESSION['mensagem'] = "Usuário deletado com sucesso";
            header('Location: ../view/index.php');
            exit;
        } else {
            $_SESSION['mensagem'] = "Usuário não foi deletado. Erro: " . mysqli_error($conexao);
            header('Location: ../view/index.php');
            exit;
        }
    }


    // --- 4. LÓGICA DE LOGIN (NOVA) ---
    if(isset($_POST['login_usuario'])){
        $email = mysqli_real_escape_string($conexao, $_POST['email'] ?? '');
        $registro = mysqli_real_escape_string($conexao, $_POST['registro'] ?? '');
        $senha_digitada = mysqli_real_escape_string($conexao, $_POST['senha']);

        $sql = "";

        // 1. Tenta logar usando Email (login padrão)
        if (!empty($email)) {
            $sql = "SELECT * FROM usuarios WHERE email = '$email'";
        } 
        // 2. Tenta logar usando CRM/COREN (login de profissional/admin)
        else if (!empty($registro)) {
            $sql = "SELECT * FROM usuarios WHERE crm_registro = '$registro' OR coren_registro = '$registro'";
        } else {
            $_SESSION['mensagem'] = "Preencha o Email ou o Registro (CRM/COREN).";
            header('Location: ../view/login.php');
            exit;
        }
        
        $query = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($query) == 1){
            $usuario = mysqli_fetch_array($query);
            $hash_armazenado = $usuario['senha'];

            // Verifica a senha usando o hash
            if (password_verify($senha_digitada, $hash_armazenado)) {
                
                // Validação para login de profissional: se usou o campo registro, a role deve ser de profissional
                if (!empty($registro) && !in_array($usuario['role'], ['medico', 'enfermeiro', 'admin'])) {
                    $_SESSION['mensagem'] = "Acesso de profissional negado para este registro.";
                    header('Location: ../view/login.php');
                    exit;
                }

                // Login bem-sucedido: Armazena dados na sessão
                $_SESSION['logado'] = true;
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['nome_usuario'] = $usuario['nome'];
                $_SESSION['role_usuario'] = $usuario['role']; // Salva o role do usuário

                $_SESSION['mensagem'] = "Bem-vindo(a), " . $usuario['nome'] . "! Seu nível de acesso é: " . strtoupper($usuario['role']);

                // Redireciona para a página inicial (../view/index.php)
                header('Location: ../view/index.php');
                exit;
                
            } else {
                // Senha incorreta
                $_SESSION['mensagem'] = "Senha incorreta.";
                header('Location: ../view/login.php');
                exit;
            }
        } else {
            // Usuário não encontrado
            $_SESSION['mensagem'] = "Credenciais não encontradas. Verifique o Email ou o Registro.";
            header('Location: ../view/login.php');
            exit;
        }
    }


    // --- 5. LÓGICA DE LOGOUT (NOVA) ---
    if(isset($_GET['logout'])){
        // Não precisa de session_start() aqui pois já está no topo, mas é boa prática se fosse um arquivo isolado
        session_unset(); // Limpa todas as variáveis de sessão
        session_destroy(); // Destrói a sessão
        $_SESSION['mensagem'] = "Sessão encerrada com sucesso.";
        // Redireciona para a tela de login
        header('Location: ../view/login.php');
        exit;
    }

?>