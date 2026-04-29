<?php

    session_start();
    require '../Model/conexao.php'; // Inclui a conexão com o banco de dados
    require_once '../Model/AuthService.php'; // Inclui o serviço de autenticação para usar a função de login
    require_once '../Model/ReceitaService.php'; // Inclui o serviço de receita para usar a função de criação de receita
    require_once '../Model/EnfermagemService.php'; // Inclui o serviço de enfermagem para usar a função de triagem

    // Instancias de serviços:
    $auth = new AuthService($conexao);
    $receitaService = new ReceitaService($conexao);
    $enfermagemService = new EnfermagemService($conexao);

    // --- LÓGICA DE CRIAÇÃO (CREATE) ---
    if(isset($_POST['create_usuario'])){
      
        $nome = filtrar_sql($_POST['nome']);
        $email = filtrar_sql($_POST['email']);
        $cpf = filtrar_sql($_POST['cpf']);
        $data_nascimento = filtrar_sql($_POST['data_nascimento']);
        
        // Validação de Confirmação de Senha
        $senha_pura = trim($_POST['senha']);
        $senha_confirmar = trim($_POST['senha_confirmar']);

        if ($senha_pura !== $senha_confirmar) {
            $_SESSION['mensagem'] = "As senhas não conferem!";
            header('Location: ../view/usuario-create.php');
            exit;
        }
        // se a senha for vazia, devolve string vazia e evita erro no password_hash
        $senha_hash = !empty($senha_pura) ? filtrar_sql(password_hash($senha_pura, PASSWORD_DEFAULT)) : '';
        
        // Novos campos para role e registro assumindo Paciente como default 
        $role = filtrar_sql($_POST['role'] ?? 'paciente');
        $crm_registro = filtrar_sql($_POST['crm_registro'] ?? '');
        $coren_registro = filtrar_sql($_POST['coren_registro'] ?? '');

        // Validação obrigatoria para logins de profissionais
        if (($role == 'medico' && empty($crm_registro)) || ($role == 'enfermeiro' && empty($coren_registro))) {
            $_SESSION['mensagem'] = "O registro (CRM/COREN) é obrigatório para o login selecionado.";
            header('Location: ../view/usuario-create.php'); 
            exit;
        }

        // inserção no banco
        // inserção no banco atualizada com CPF
        $sql = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, senha, role, crm_registro, coren_registro) 
                VALUES ('$nome', '$email', '$cpf', '$data_nascimento', '$senha_hash', '$role', ";
        
        // Adiciona valores de registro de forma segura, tratando NULL se vazio
        $sql .= empty($crm_registro) ? "NULL, " : "'$crm_registro', ";
        $sql .= empty($coren_registro) ? "NULL)" : "'$coren_registro')";

        try {
            $query = mysqli_query($conexao, $sql);

            if($query) { 
                $_SESSION['mensagem'] = "Usuário criado com sucesso!";
                
                if(isset($_SESSION['logado']) && $_SESSION['role_usuario'] !== 'paciente'){
                    header('Location: ../view/lista-de-usuarios.php'); 
                } else {
                    header('Location: ../view/login.php'); 
                }
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            // O código 1062 indica entrada duplicada no MySQL
            if ($e->getCode() === 1062) {
                $_SESSION['mensagem'] = "Erro: Este CPF ou E-mail já está cadastrado.";
            } else {
                $_SESSION['mensagem'] = "Erro ao criar usuário: " . $e->getMessage();
            }
            
            header('Location: ../view/usuario-create.php');
            exit;
        }
    }
    
    // --- LÓGICA DE CADASTRO INSTITUCIONAL (ADM CADASTRANDO PROFISSIONAL) ---
    if (isset($_POST['cadastrar_profissional'])) {
        // Apenas Admin pode executar essa ação (Segurança extra no back-end)
        if ($_SESSION['role_usuario'] !== 'admin') {
            $_SESSION['mensagem'] = "Erro: Você não tem permissão para realizar esta ação.";
            header('Location: ../view/login.php');
            exit;
        }

        $nome = filtrar_sql($_POST['nome']);
        $email = filtrar_sql($_POST['email']);
        $cpf = filtrar_sql($_POST['cpf']);
        $role = filtrar_sql($_POST['role_usuario']);
        $registro = filtrar_sql($_POST['registro_profissional']);
        $senha_pura = $_POST['senha'];
        $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);

        // Define qual coluna de registro usar com base na role
        $coluna_registro = ($role === 'medico') ? 'crm_registro' : 'coren_registro';

        // SQL de inserção rigorosa
        $sql = "INSERT INTO usuarios (nome, email, cpf, senha, role, $coluna_registro) 
                VALUES ('$nome', '$email', '$cpf', '$senha_hash', '$role', '$registro')";

        try {
            if (mysqli_query($conexao, $sql)) {
                $_SESSION['mensagem'] = "Profissional ($role) cadastrado com sucesso!";
                header('Location: ../view/admin-painel.php');
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $_SESSION['mensagem'] = "Erro: Este CPF, E-mail ou Registro já está cadastrado.";
            } else {
                $_SESSION['mensagem'] = "Erro ao cadastrar profissional: " . $e->getMessage();
            }
            header('Location: ../view/admin-cadastrar-profissional.php');
            exit;
        }
    }


    // ---  LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
// ---  LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
    if(isset($_POST['update_usuario'])){
        $usuario_id = filtrar_sql($_POST['usuario_id']);
        
        // --- INÍCIO DA CORREÇÃO DE PERMISSÃO ---
        // Permite se: For funcionário/admin (role != paciente) OU se o ID logado for o mesmo do perfil
        $pode_editar = ($_SESSION['role_usuario'] !== 'paciente' || $_SESSION['id_usuario'] == $usuario_id);

        if (!$pode_editar) {
            $_SESSION['mensagem'] = "Acesso negado. Você não tem permissão para editar este perfil.";
            header("Location: ../view/perfil.php");
            exit;
        }
        // --- FIM DA CORREÇÃO ---

        $nome = filtrar_sql($_POST['nome']);
        $email = filtrar_sql($_POST['email']);
        $cpf = filtrar_sql($_POST['cpf']); // <-- INSERIR ESTA LINHA
        $data_nascimento = filtrar_sql($_POST['data_nascimento']);
        
        // --- NOVO: Captura de Senha e Confirmação ---
        $senha = $_POST['senha']; 
        $senha_confirmar = $_POST['senha_confirmar'] ?? ''; 

        // Só valida se o usuário preencheu o campo de senha
        if (!empty($senha)) {
            if ($senha !== $senha_confirmar) {
                $_SESSION['mensagem'] = "As senhas não conferem!";
                header("Location: ../view/usuario-edit.php?id=$usuario_id");
                exit;
            }
        }

        $role = filtrar_sql($_POST['role'] ?? '');
        $crm_registro = filtrar_sql($_POST['crm_registro'] ?? '');
        $coren_registro = filtrar_sql($_POST['coren_registro'] ?? '');

        // Início da montagem do SQL incluindo o CPF
        $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', cpf = '$cpf', data_nascimento = '$data_nascimento'";
        
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
            // Se a senha for fornecida e passou na validação acima, cria um novo hash
            $sql .= ", senha = '". password_hash($senha, PASSWORD_DEFAULT) . "'";
         }
            
        $sql .= " WHERE id = $usuario_id";

        // --- TRECHO ATUALIZADO PARA MENSAGENS E REDIRECIONAMENTO ---
        if(mysqli_query($conexao, $sql)){ 
            // Usamos 'mensagem' pois é o padrão que você já usa no resto do arquivo
            $_SESSION['mensagem'] = "Atualização feita com sucesso!";
            
            // Redireciona para o perfil se for o próprio usuário, ou lista se for admin
            $location = ($_SESSION['role_usuario'] === 'paciente') ? '../view/perfil.php' : '../view/lista-de-usuarios.php';
            header("Location: $location"); 
            exit;
        } else {
            $_SESSION['mensagem'] = "Erro ao atualizar: " . mysqli_error($conexao);
            header('Location: ../view/perfil.php');
            exit;
        }
    }

    // ---  LÓGICA DE EXCLUSÃO (DELETE) ---
    if(isset($_POST['delete_usuario'])){
        $usuario_id = filtrar_sql($_POST['delete_usuario']);
        
        $sql = "DELETE FROM usuarios WHERE id = '$usuario_id'";

        mysqli_query($conexao, $sql);

        if(mysqli_affected_rows($conexao) > 0) {
            $_SESSION['mensagem'] = "Usuário deletado com sucesso";
            header('Location: ../view/lista-de-usuarios.php');
            exit;
        } else {
            $_SESSION['mensagem'] = "Usuário não foi deletado. Erro: " . mysqli_error($conexao);
            header('Location: ../view/lista-de-usuarios.php');
            exit;
        }
    }

    // --- CRIAÇÃO DE RECEITA ---
// --- CRIAÇÃO DE RECEITA COM TOKEN DE ASSINATURA ---
if (isset($_POST['create_receita'])) {
    // Geração do Token de Assinatura Digital (Simulado para o TCC)
    $medico_id = filtrar_sql($_POST['medico_id']);
    $paciente_id = filtrar_sql($_POST['paciente_id']);
    $timestamp = date('Y-m-d H:i:s');
    
    // O token é um hash único combinando IDs e o tempo exato da criação
    $token_assinatura = hash('sha256', $medico_id . $paciente_id . $timestamp);

    // Coleta dados da receita (Todos os campos originais mantidos + o novo Token)
    $dados_receita = [
        'medico_id' => $medico_id,
        'paciente_id' => $paciente_id,
        'tipo_receita' => filtrar_sql($_POST['tipo_receita']),
        'observacoes' => filtrar_sql($_POST['observacoes'] ?? ''),
        'token_assinatura' => $token_assinatura
    ];

    // Coleta arrays de itens (filtrando cada um)
    $itens_receita = [
        'nomes' => array_map('filtrar_sql', $_POST['medicamento_nome']),
        'concentracoes' => array_map('filtrar_sql', $_POST['concentracao']),
        'quantidades' => array_map('filtrar_sql', $_POST['quantidade_total']),
        'posologias' => array_map('filtrar_sql', $_POST['posologia'])
    ];

    // Delega para o serviço
    $resultado = $receitaService->criarReceita($dados_receita, $itens_receita);

    if ($resultado['sucesso']) {
        $_SESSION['mensagem'] = "Receita prescrita com sucesso!";
        header('Location: ../view/receitas.php?id=' . $resultado['id']);
    } else {
        $_SESSION['mensagem'] = "Erro ao criar receita: " . $resultado['erro'];
        header('Location: ../view/receita-create.php');
    }
    exit;
}


    // --- LÓGICA DE CRIAÇÃO DE TRIAGEM ---
    if (isset($_POST['create_triagem'])) {
        $dados_triagem = [
            'paciente_id'         => filtrar_sql($_POST['paciente_id']),
            'enfermeiro_id'       => $_SESSION['id_usuario'], // Pega o ID de quem está logado
            'queixa_principal'    => filtrar_sql($_POST['queixa_principal']),
            'pressao_sistolica'   => filtrar_sql($_POST['pressao_sistolica']),
            'pressao_diastolica'  => filtrar_sql($_POST['pressao_diastolica']),
            'temperatura'         => filtrar_sql($_POST['temperatura']),
            'peso'                => filtrar_sql($_POST['peso']),
            'altura'              => filtrar_sql($_POST['altura']),
            'frequencia_cardiaca' => filtrar_sql($_POST['frequencia_cardiaca']),
            'saturacao'           => filtrar_sql($_POST['saturacao']),
            'classificacao_risco' => filtrar_sql($_POST['classificacao_risco'])
        ];

        $resultado = $enfermagemService->salvarTriagem($dados_triagem);

        if ($resultado['sucesso']) {
            $_SESSION['mensagem'] = "Triagem realizada com sucesso!";
            header('Location: ../view/painel-enfermagem.php');
        } else {
            $_SESSION['mensagem'] = "Erro na triagem: " . $resultado['erro'];
            header('Location: ../view/triagem-create.php');
        }
        exit;
    }

    // --- LÓGICA DE LOGIN ---
    if (isset($_POST['login_usuario'])) {
        // Usamos a sua função filtrar_sql() para os campos de texto
        $email = filtrar_sql($_POST['email'] ?? '');
        $registro = filtrar_sql($_POST['registro'] ?? '');
        $senha = $_POST['senha']; // Senha pura para o password_verify interno

        // Chamamos o MÉTODO do nosso SERVIÇO
        $resultado = $auth->autenticar($email, $registro, $senha);

        if ($resultado['sucesso']) {
            $user = $resultado['dados'];
            
            // O Controller cuida apenas da SESSÃO e do REDIRECIONAMENTO
            $_SESSION['logado'] = true;
            $_SESSION['id_usuario'] = $user['id'];
            $_SESSION['nome_usuario'] = $user['nome'];
            $_SESSION['role_usuario'] = $user['role'];
            $_SESSION['mensagem'] = "Bem-vindo(a), " . $user['nome'] . "!";

            if ($user['role'] === 'paciente') {
                $url = '../view/home.php';
            } elseif ($user['role'] === 'enfermeiro') {
                $url = '../view/home-enfermeiro.php'; 
            } elseif ($user['role'] === 'medico') {
                $url = '../view/painel-medico.php'; 
            } else {
                // Admins ou outros cargos 
                $url = '../view/lista-de-usuarios.php'; 
            }
            header("Location: $url");
            exit;
        } else {
            $_SESSION['mensagem'] = $resultado['erro'];
            header('Location: ../view/login.php');
            exit;
        }
    }


    // --- LÓGICA DE LOGOUT ---
    if(isset($_GET['logout'])){
        // Não precisa de session_start() aqui pq já ta no topo, mas seria uma boa prática se fosse um arquivo isolado
        session_unset(); // Limpa todas as variáveis de sessão
        session_destroy(); // Destrói a sessão
        $_SESSION['mensagem'] = "Sessão encerrada com sucesso.";
        // Redireciona para a tela home
        header('Location: ../view/index.php');
        exit;
    }

    // --- LÓGICA DE SOLICITAÇÃO DE RECUPERAÇÃO DE SENHA ---
    // --- LÓGICA DE SOLICITAÇÃO DE RECUPERAÇÃO DE SENHA ---
    if (isset($_POST['esqueci_senha'])) {
        $email = filtrar_sql($_POST['email']);
        
        $query_usuario = mysqli_query($conexao, "SELECT id FROM usuarios WHERE email = '$email'");
        
        if (mysqli_num_rows($query_usuario) > 0) {
            $codigo = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $expiracao = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            mysqli_query($conexao, "UPDATE recuperacao_senha SET usado = 1 WHERE email = '$email'");

            $sql = "INSERT INTO recuperacao_senha (email, codigo, data_expiracao) 
                    VALUES ('$email', '$codigo', '$expiracao')";
            
            if (mysqli_query($conexao, $sql)) {
                
                // --- NOVA LOGICA DE ENVIO REAL ---
                require_once 'RecuperacaoEmailController.php';
                $enviou = RecuperacaoEmailController::enviarCodigo($email, $codigo);

                if ($enviou) {
                    $_SESSION['mensagem'] = "Código enviado com sucesso para o e-mail cadastrado!";
                } else {
                    $_SESSION['mensagem'] = "Código gerado, mas houve um erro ao enviar o e-mail. Verifique o banco de dados (Teste).";
                }
                
                $_SESSION['email_recuperacao'] = $email;
                header('Location: ../view/verificar-codigo.php');
                exit;
            }
        } else {
            $_SESSION['mensagem'] = "E-mail não encontrado em nossa base.";
            header('Location: ../view/login.php');
            exit;
        }
    }

    // --- LÓGICA DE VALIDAÇÃO DO CÓDIGO ---
    if (isset($_POST['validar_codigo'])) {
        $email = $_SESSION['email_recuperacao'];
        $codigo_digitado = filtrar_sql($_POST['codigo_verificacao']);
        $agora = date('Y-m-d H:i:s');

        // Busca o código no banco que pertença a esse email, não tenha sido usado e não tenha expirado
        $sql = "SELECT * FROM recuperacao_senha 
                WHERE email = '$email' 
                AND codigo = '$codigo_digitado' 
                AND usado = 0 
                AND data_expiracao > '$agora'
                LIMIT 1";

        $result = mysqli_query($conexao, $sql);

        if (mysqli_num_rows($result) > 0) {
            // Código válido! 
            $_SESSION['pode_mudar_senha'] = true; // Libera o acesso à tela de nova senha
            header('Location: ../view/nova-senha.php');
            exit;
        } else {
            $_SESSION['mensagem'] = "Código inválido ou expirado. Tente novamente.";
            header('Location: ../view/verificar-codigo.php');
            exit;
        }
    }

    // --- LÓGICA DE DEFINIÇÃO DE NOVA SENHA ---
    if (isset($_POST['atualizar_senha_esquecida'])) {
        $email = $_SESSION['email_recuperacao'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        if ($nova_senha !== $confirmar_senha) {
            $_SESSION['mensagem'] = "As senhas não conferem!";
            header('Location: ../view/nova-senha.php');
            exit;
        }

        // Criptografa a nova senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // 1. Atualiza a senha do usuário
        $sql_update = "UPDATE usuarios SET senha = '$senha_hash' WHERE email = '$email'";
        
        if (mysqli_query($conexao, $sql_update)) {
            // 2. Invalida o código para ele não ser usado de novo
            mysqli_query($conexao, "UPDATE recuperacao_senha SET usado = 1 WHERE email = '$email'");
            
            // 3. Limpa a sessão de recuperação
            unset($_SESSION['email_recuperacao']);
            unset($_SESSION['pode_mudar_senha']);

            $_SESSION['mensagem'] = "Senha atualizada com sucesso! Faça login agora.";
            header('Location: ../view/login.php');
            exit;
        } else {
            $_SESSION['mensagem'] = "Erro ao atualizar senha. Tente novamente.";
            header('Location: ../view/nova-senha.php');
            exit;
        }
    }
?>