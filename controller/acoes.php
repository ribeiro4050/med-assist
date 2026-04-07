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
        $data_nascimento = filtrar_sql($_POST['data_nascimento']);
        
        // Criptografia da Senha com um hash forte
        $senha_pura = trim($_POST['senha']);
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
        $sql = "INSERT INTO usuarios (nome, email, data_nascimento, senha, role, crm_registro, coren_registro) 
                VALUES ('$nome', '$email', '$data_nascimento', '$senha_hash', '$role', ";
        
        // Adiciona valores de registro de forma segura, tratando NULL se vazio
        $sql .= empty($crm_registro) ? "NULL, " : "'$crm_registro', ";
        $sql .= empty($coren_registro) ? "NULL)" : "'$coren_registro')";

        $query = mysqli_query($conexao, $sql);

        if($query) { 
            $_SESSION['mensagem'] = "Usuário criado com sucesso";
            header('Location: ../view/lista-de-usuarios.php'); 
            exit;
        } else {
            $_SESSION['mensagem'] = "Usuário não foi criado. Erro: " . mysqli_error($conexao);
            header('Location: ../view/usuario-create.php');
            exit;
        }
    }


    // ---  LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
    if(isset($_POST['update_usuario'])){
        $usuario_id = filtrar_sql($_POST['usuario_id']);
        $nome = filtrar_sql($_POST['nome']);
        $email = filtrar_sql($_POST['email']);
        $data_nascimento = filtrar_sql($_POST['data_nascimento']);
        $senha = filtrar_sql($_POST['senha']);
        $role = filtrar_sql($_POST['role'] ?? '');
        $crm_registro = filtrar_sql($_POST['crm_registro'] ?? '');
        $coren_registro = filtrar_sql($_POST['coren_registro'] ?? '');


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
            header('Location: ../view/lista-de-usuarios.php'); 
            exit;
        } else {
            $_SESSION['mensagem'] = "Nenhuma alteração feita ou Usuário não foi encontrado/atualizado. Erro: " . mysqli_error($conexao);
            header('Location: ../view/lista-de-usuarios.php');
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
if (isset($_POST['create_receita'])) {
    // Coleta dados da receita
    $dados_receita = [
        'medico_id' => filtrar_sql($_POST['medico_id']),
        'paciente_id' => filtrar_sql($_POST['paciente_id']),
        'tipo_receita' => filtrar_sql($_POST['tipo_receita']),
        'observacoes' => filtrar_sql($_POST['observacoes'] ?? '')
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

            $url = ($user['role'] === 'paciente') ? '../view/home.php' : '../view/lista-de-usuarios.php';
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

?>