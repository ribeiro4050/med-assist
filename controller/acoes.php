<?php

    session_start();
    require '../Model/conexao.php'; // Inclui a conexão com o banco de dados
    require_once '../Model/AuthService.php'; // Inclui o serviço de autenticação para usar a função de login
    $auth = new AuthService($conexao);

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
        // Coleta e sanitização dos dados da Receita Principal
        $medico_id = filtrar_sql($_POST['medico_id']);
        $paciente_id = filtrar_sql($_POST['paciente_id']);
        $tipo_receita = filtrar_sql($_POST['tipo_receita']);
        $observacoes = filtrar_sql($_POST['observacoes'] ?? '');
        $data_prescricao = date('Y-m-d H:i:s'); // Define a data/hora atual da prescrição

        // Coleta dos arrays dos Itens de Receita
        $medicamento_nomes = $_POST['medicamento_nome'];
        $concentracaos = $_POST['concentracao'];
        $quantidade_totais = $_POST['quantidade_total'];
        $posologias = $_POST['posologia'];

        // Validação dos campos obrigatórios
        if (empty($paciente_id) || empty($tipo_receita) || empty($medicamento_nomes) || count($medicamento_nomes) == 0) {
            $_SESSION['mensagem'] = "Campos obrigatórios: Paciente e  Medicamento não foram preenchidos.";
            header('Location: ../view/receita-create.php');
            exit;
        }

        // inserção da receita
        // caso observações esteja vazio, insere NULL no banco
        $sql_receita = "INSERT INTO receitas 
                        (medico_id, paciente_id, data_prescricao, tipo_receita, observacoes) 
                        VALUES ('$medico_id', '$paciente_id', '$data_prescricao', '$tipo_receita', 
                        " . (empty($observacoes) ? "NULL" : "'$observacoes'") . ")";

        if (mysqli_query($conexao, $sql_receita)) {
            // obtem o id da receita criada
            $receita_id = mysqli_insert_id($conexao);
            $sucesso_itens = true;
            $erros_itens = 0;

            // loop para inserir cada item de receita
            foreach ($medicamento_nomes as $key => $nome) {
                $nome_seguro = filtrar_sql($nome);
                $concentracao_seguro = filtrar_sql($concentracaos[$key]);
                $quantidade_seguro = filtrar_sql($quantidade_totais[$key]);
                $posologia_seguro = filtrar_sql($posologias[$key]);

                // Validação de item (garantir que não há itens vazios se o usuário clonou mas não preencheu)
                if (empty($nome_seguro) || empty($concentracao_seguro) || empty($quantidade_seguro) || empty($posologia_seguro)) {
                    // Ignora itens incompletos, mas não interrompe o processo.
                    continue; 
                }

                $sql_item = "INSERT INTO itens_receita 
                             (receita_id, medicamento_nome, concentracao, quantidade_total, posologia) 
                             VALUES ('$receita_id', '$nome_seguro', '$concentracao_seguro', '$quantidade_seguro', '$posologia_seguro')";
                
                if (!mysqli_query($conexao, $sql_item)) {
                    $sucesso_itens = false;
                    $erros_itens++;
                }
            }

            // resultado da criação da receita
            if ($sucesso_itens) {
                $_SESSION['mensagem'] = "Receita e todos os itens prescritos com sucesso! ID: " . $receita_id;
                header('Location: ../view/receitas.php?id=' . $receita_id); // Redireciona para a visualização
                exit;
            } else {
                // Em caso de falha na inserção de itens, talvez seja necessário apagar a receita principal, 
                // mas por agora, um alerta ja ta bom.
                $_SESSION['mensagem'] = "Receita criada, mas falha ao inserir $erros_itens item(s) de medicamento. Erro: " . mysqli_error($conexao);
                header('Location: ../view/receitas.php');
                exit;
            }

        } else {
            // Falha na inserção da receita principal
            $_SESSION['mensagem'] = "Erro ao criar a Receita. Erro: " . mysqli_error($conexao);
            header('Location: ../view/receita-create.php');
            exit;
        }
    }

    // --- EXCLUSÃO DE RECEITA ---
    if(isset($_POST['delete_receita'])){
        $receita_id = filtrar_sql($_POST['delete_receita']);
        $medico_id_logado = $_SESSION['id_usuario'];
        $role = $_SESSION['role_usuario'];

        // Verificar se o médico é o dono da receita
        // Admin pode deletar qualquer coisa quando a gente criar um admin
        // Médico só pode deletar suas próprias.
        // mas no geral a logica é a mesma do usuer
        $sql_select = "SELECT medico_id FROM receitas WHERE id = '$receita_id'";
        $query_select = mysqli_query($conexao, $sql_select);

        // o mysqli_fetch_assoc retorna um array associativo com os dados da consulta e pula para a próxima linha
        // diferente do mysqli_fetch_array que retorna um array numerico e associativo, o mysqli_fectch_assoc associa com a chave ao invés do indice numerico
        $receita = mysqli_fetch_assoc($query_select);
        
        if ($receita && $receita['medico_id'] != $medico_id_logado && $role != 'admin') {
            $_SESSION['mensagem'] = "Você não tem permissão para excluir esta receita.";
            header('Location: ../view/receitas.php');
            exit;
        }

        // Devido ao ON DELETE CASCADE:
        // Deletar a receita principal
        // Os itens relacionados do itens_receita serão deletados automaticamente.
        $sql = "DELETE FROM receitas WHERE id = '$receita_id'";

        mysqli_query($conexao, $sql);

        if(mysqli_affected_rows($conexao) > 0) {
            $_SESSION['mensagem'] = "Receita e todos os itens deletados com sucesso!";
            header('Location: ../view/receitas.php');
            exit;
        } else {
            $_SESSION['mensagem'] = "Receita não foi deletada ou não foi encontrada. Erro: " . mysqli_error($conexao);
            header('Location: ../view/receitas.php');
            exit;
        }
    }

    // --- EDIÇÃO DE RECEITA ---
    if (isset($_POST['update_receita'])) {
        // coleta dos dados principais
        $receita_id = filtrar_sql($_POST['receita_id']);
        $medico_id = filtrar_sql($_POST['medico_id']); // Usado para validação de segurança
        $paciente_id = filtrar_sql($_POST['paciente_id']);
        $tipo_receita = filtrar_sql($_POST['tipo_receita']);
        $observacoes = filtrar_sql($_POST['observacoes'] ?? '');

        // Validação de acesso (Apenas o criador pode atualizar)
        if ($_SESSION['role_usuario'] !== 'admin' && $medico_id != $_SESSION['id_usuario']) {
            $_SESSION['mensagem'] = "Ação negada. Você não é o prescritor desta receita.";
            header('Location: ../view/receitas.php');
            exit;
        }

        // atualização da receita principal
        $sql_update_receita = "UPDATE receitas SET 
                                paciente_id = '$paciente_id', 
                                tipo_receita = '$tipo_receita', 
                                observacoes = " . (empty($observacoes) ? "NULL" : "'$observacoes'") . "
                                WHERE id = '$receita_id'";

        mysqli_query($conexao, $sql_update_receita);

        // exclui todos os itens antigos antes de inserir
        $sql_delete_itens = "DELETE FROM itens_receita WHERE receita_id = '$receita_id'";
        mysqli_query($conexao, $sql_delete_itens); 

        // processa os novos itens enviados
        
        $medicamento_nomes = $_POST['medicamento_nome'] ?? []; // Assume array vazio se nada for enviado
        $concentracaos = $_POST['concentracao'] ?? [];
        $quantidade_totais = $_POST['quantidade_total'] ?? [];
        $posologias = $_POST['posologia'] ?? [];
        
        $sucesso_itens = true;
        
        // Loop sobre os itens enviados
        foreach ($medicamento_nomes as $key => $nome) {
            
            $nome_seguro = filtrar_sql($nome);
            $concentracao_seguro = filtrar_sql($concentracaos[$key] ?? '');
            $quantidade_seguro = filtrar_sql($quantidade_totais[$key] ?? '');
            $posologia_seguro = filtrar_sql($posologias[$key] ?? '');

            // Ignora itens incompletos ou vazios
            if (empty($nome_seguro)) { continue; } 

            // Como excluímos tudo, agora fazemos apenas INSERT para os itens válidos
            $sql_item = "INSERT INTO itens_receita 
                         (receita_id, medicamento_nome, concentracao, quantidade_total, posologia) 
                         VALUES ('$receita_id', '$nome_seguro', '$concentracao_seguro', '$quantidade_seguro', '$posologia_seguro')";
            
            if (!mysqli_query($conexao, $sql_item)) {
                $sucesso_itens = false;
            }
        }
        
        //  Resultado Final
        if ($sucesso_itens) {
            $_SESSION['mensagem'] = "Receita #$receita_id atualizada com sucesso!";
            header('Location: ../view/receita-view.php?id=' . $receita_id);
            exit;
        } else {
            $_SESSION['mensagem'] = "Receita principal atualizada, mas houve um erro ao salvar os itens. Erro: " . mysqli_error($conexao);
            header('Location: ../view/receitas.php');
            exit;
        }
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