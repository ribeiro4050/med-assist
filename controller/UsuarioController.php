<?php
session_start();
require_once '../Model/conexao.php';

// --- 1. LÓGICA DE CRIAÇÃO (CREATE) ---
if(isset($_POST['create_usuario'])){
    $nome = filtrar_sql($_POST['nome']);
    $email = filtrar_sql($_POST['email']);
    $cpf = filtrar_sql($_POST['cpf']);
    $data_nascimento = filtrar_sql($_POST['data_nascimento']);
    
    $senha_pura = trim($_POST['senha']);
    $senha_confirmar = trim($_POST['senha_confirmar']);

    if ($senha_pura !== $senha_confirmar) {
        $_SESSION['mensagem'] = "As senhas não conferem!";
        header('Location: ../view/usuario-create.php');
        exit;
    }

    $senha_hash = !empty($senha_pura) ? password_hash($senha_pura, PASSWORD_DEFAULT) : '';
    $role = filtrar_sql($_POST['role'] ?? 'paciente');
    $crm_registro = filtrar_sql($_POST['crm_registro'] ?? '');
    $coren_registro = filtrar_sql($_POST['coren_registro'] ?? '');

    $sql = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, senha, role, crm_registro, coren_registro) 
            VALUES ('$nome', '$email', '$cpf', '$data_nascimento', '$senha_hash', '$role', ";
    $sql .= empty($crm_registro) ? "NULL, " : "'$crm_registro', ";
    $sql .= empty($coren_registro) ? "NULL)" : "'$coren_registro')";

    try {
        if(mysqli_query($conexao, $sql)) { 
            $_SESSION['mensagem'] = "Usuário criado com sucesso!";
            $location = (isset($_SESSION['logado']) && $_SESSION['role_usuario'] !== 'paciente') ? 'lista-de-usuarios.php' : 'login.php';
            header("Location: ../view/$location");
            exit;
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['mensagem'] = ($e->getCode() === 1062) ? "Erro: CPF ou E-mail já cadastrado." : "Erro ao criar usuário.";
        header('Location: ../view/usuario-create.php');
        exit;
    }
}

// --- 2. CADASTRO DE PROFISSIONAL (ADMIN) ---
if (isset($_POST['cadastrar_profissional'])) {
    if ($_SESSION['role_usuario'] !== 'admin') {
        $_SESSION['mensagem'] = "Acesso negado.";
        header('Location: ../view/login.php');
        exit;
    }

    $nome = filtrar_sql($_POST['nome']);
    $email = filtrar_sql($_POST['email']);
    $cpf = filtrar_sql($_POST['cpf']);
    $role = filtrar_sql($_POST['role_usuario']);
    $registro = filtrar_sql($_POST['registro_profissional']);
    $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $coluna_registro = ($role === 'medico') ? 'crm_registro' : 'coren_registro';

    $sql = "INSERT INTO usuarios (nome, email, cpf, senha, role, $coluna_registro) 
            VALUES ('$nome', '$email', '$cpf', '$senha_hash', '$role', '$registro')";

    try {
        if (mysqli_query($conexao, $sql)) {
            $_SESSION['mensagem'] = "Profissional cadastrado com sucesso!";
            header('Location: ../view/admin-painel.php');
            exit;
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['mensagem'] = "Erro ao cadastrar profissional.";
        header('Location: ../view/admin-cadastrar-profissional.php');
        exit;
    }
}

// --- 3. ATUALIZAÇÃO (UPDATE) ---
if(isset($_POST['update_usuario'])){
    $usuario_id = filtrar_sql($_POST['usuario_id']);
    $pode_editar = ($_SESSION['role_usuario'] !== 'paciente' || $_SESSION['id_usuario'] == $usuario_id);

    if (!$pode_editar) {
        $_SESSION['mensagem'] = "Acesso negado.";
        header("Location: ../view/perfil.php");
        exit;
    }

    $nome = filtrar_sql($_POST['nome']);
    $email = filtrar_sql($_POST['email']);
    $cpf = filtrar_sql($_POST['cpf']);
    $data_nascimento = filtrar_sql($_POST['data_nascimento']);
    $senha = $_POST['senha'];
    $senha_confirmar = $_POST['senha_confirmar'] ?? '';

    if (!empty($senha) && $senha !== $senha_confirmar) {
        $_SESSION['mensagem'] = "As senhas não conferem!";
        header("Location: ../view/usuario-edit.php?id=$usuario_id");
        exit;
    }

    $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', cpf = '$cpf', data_nascimento = '$data_nascimento'";
    
    if(!empty($senha)){
        $sql .= ", senha = '". password_hash($senha, PASSWORD_DEFAULT) . "'";
    }
    $sql .= " WHERE id = $usuario_id";

    if(mysqli_query($conexao, $sql)){ 
        $_SESSION['mensagem'] = "Atualização feita com sucesso!";

        if ($_SESSION['id_usuario'] == $usuario_id) {
            $location = 'perfil.php';
        } else {
            $location = 'lista-de-usuarios.php';
        }

        header("Location: ../view/$location"); 
        exit;
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar.";
        header('Location: ../view/perfil.php');
        exit;
    }
}

// --- 4. EXCLUSÃO (DELETE) ---
if(isset($_POST['delete_usuario'])){
    if ($_SESSION['role_usuario'] !== 'admin') {
        $_SESSION['mensagem'] = "Acesso negado. Apenas administradores podem excluir usuários.";
        header('Location: ../view/index.php');
        exit;
    }

    $usuario_id = filtrar_sql($_POST['delete_usuario']);
    $sql = "DELETE FROM usuarios WHERE id = '$usuario_id'";

    if(mysqli_query($conexao, $sql)) {
        $_SESSION['mensagem'] = "Usuário deletado com sucesso";
    } else {
        $_SESSION['mensagem'] = "Erro ao deletar usuário.";
    }
    header('Location: ../view/lista-de-usuarios.php');
    exit;
}

// --- LÓGICA DE CRIAÇÃO DE TRIAGEM ---
if (isset($_POST['create_triagem'])) {
    require_once '../Model/EnfermagemService.php';
    $enfermagemService = new EnfermagemService($conexao);

    $dados_triagem = [
        'paciente_id'         => filtrar_sql($_POST['paciente_id']),
        'enfermeiro_id'       => $_SESSION['id_usuario'], 
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