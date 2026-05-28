<?php
session_start();
require_once '../Model/conexao.php';
require_once '../Model/UsuarioService.php';

$usuarioService = new UsuarioService($conexao);

// --- 1. LÓGICA DE CRIAÇÃO (CREATE) ---
if (isset($_POST['create_usuario'])) {
    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $cpf = mysqli_real_escape_string($conexao, $_POST['cpf']);
    $data_nascimento = mysqli_real_escape_string($conexao, $_POST['data_nascimento']);
    
    $senha_pura = trim($_POST['senha']);
    $senha_confirmar = trim($_POST['senha_confirmar']);

    if ($senha_pura !== $senha_confirmar) {
        $_SESSION['mensagem'] = "As senhas não conferem!";
        header('Location: ../view/usuario-create.php');
        exit;
    }

    $senha_hash = !empty($senha_pura) ? password_hash($senha_pura, PASSWORD_DEFAULT) : '';
    $role = mysqli_real_escape_string($conexao, $_POST['role'] ?? 'paciente');
    $crm_registro = mysqli_real_escape_string($conexao, $_POST['crm_registro'] ?? '');
    $coren_registro = mysqli_real_escape_string($conexao, $_POST['coren_registro'] ?? '');

    $resultado = $usuarioService->salvarUsuario($nome, $email, $cpf, $data_nascimento, $senha_hash, $role, $crm_registro, $coren_registro);

    if ($resultado['sucesso']) { 
        $_SESSION['mensagem'] = "Usuário criado com sucesso!";
        $location = (isset($_SESSION['logado']) && $_SESSION['role_usuario'] !== 'paciente') ? 'lista-de-usuarios.php' : 'login.php';
        header("Location: ../view/$location");
        exit;
    } else {
        $_SESSION['mensagem'] = ($resultado['codigo_erro'] === 1062) ? "Erro: CPF ou E-mail já cadastrado." : "Erro ao criar usuário.";
        header('Location: ../view/usuario-create.php');
        exit;
    }
}

// --- 2. CADASTRO DE PROFISSIONAL (ADMIN) ---
if (isset($_POST['cadastrar_profissional'])) {
    // CAMADA RIGOROSA DE SEGURANÇA: Verifica se está logado E se possui nível de Admin
    if (!isset($_SESSION['logado']) || !isset($_SESSION['role_usuario']) || $_SESSION['role_usuario'] !== 'admin') {
        $_SESSION['mensagem'] = "Acesso negado. Apenas administradores autorizados podem realizar esta ação.";
        header('Location: ../view/login.php');
        exit;
    }

    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $cpf = mysqli_real_escape_string($conexao, $_POST['cpf']);
    $data_nascimento = mysqli_real_escape_string($conexao, $_POST['data_nascimento']); // Injeção corrigida aqui!
    $role = mysqli_real_escape_string($conexao, $_POST['role_usuario']);
    $registro = mysqli_real_escape_string($conexao, $_POST['registro_profissional']);
    
    $senha_pura = trim($_POST['senha']);
    $senha_hash = password_hash($senha_pura, PASSWORD_DEFAULT);

    // Mapeia a coluna correta de acordo com a regra de negócio
    $coluna_registro = ($role === 'medico') ? 'crm_registro' : 'coren_registro';

    // Chama o método atualizado do Service injetando a data de nascimento
    if ($usuarioService->cadastrarProfissional($nome, $email, $cpf, $data_nascimento, $senha_hash, $role, $coluna_registro, $registro)) {
        $_SESSION['mensagem'] = "Profissional cadastrado com sucesso!";
        header('Location: ../view/admin-painel.php');
        exit;
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar profissional no sistema.";
        header('Location: ../view/admin-cadastrar-profissional.php');
        exit;
    }
}

// --- 3. ATUALIZAÇÃO (UPDATE) ---
if (isset($_POST['update_usuario'])) {
    $usuario_id = mysqli_real_escape_string($conexao, $_POST['usuario_id']);
    
    // Trava de segurança: impede que usuários alterem perfis alheios (autenticação mútua)
    $pode_editar = (isset($_SESSION['logado']) && ($_SESSION['role_usuario'] !== 'paciente' || $_SESSION['id_usuario'] == $usuario_id));

    if (!$pode_editar) {
        $_SESSION['mensagem'] = "Acesso negado.";
        header("Location: ../view/perfil.php");
        exit;
    }

    $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
    $email = mysqli_real_escape_string($conexao, $_POST['email']);
    $cpf = mysqli_real_escape_string($conexao, $_POST['cpf']);
    $data_nascimento = mysqli_real_escape_string($conexao, $_POST['data_nascimento']);
    $senha = $_POST['senha'];
    $senha_confirmar = $_POST['senha_confirmar'] ?? '';

    if (!empty($senha) && $senha !== $senha_confirmar) {
        $_SESSION['mensagem'] = "As senhas não conferem!";
        header("Location: ../view/usuario-edit.php?id=$usuario_id");
        exit;
    }

    if ($usuarioService->atualizarUsuario($usuario_id, $nome, $email, $cpf, $data_nascimento, $senha)) { 
        $_SESSION['mensagem'] = "Atualização feita com sucesso!";
        $location = ($_SESSION['id_usuario'] == $usuario_id) ? 'perfil.php' : 'lista-de-usuarios.php';
        header("Location: ../view/$location"); 
        exit;
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar.";
        header('Location: ../view/perfil.php');
        exit;
    }
}

// --- 4. EXCLUSÃO (DELETE) ---
if (isset($_POST['delete_usuario'])) {
    // Trava de segurança: Somente administradores logados podem usar este método
    if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'admin') {
        $_SESSION['mensagem'] = "Acesso negado. Apenas administradores podem excluir usuários.";
        header('Location: ../view/index.php');
        exit;
    }

    $usuario_id = mysqli_real_escape_string($conexao, $_POST['delete_usuario']);

    if ($usuarioService->deletarUsuario($usuario_id)) {
        $_SESSION['mensagem'] = "Usuário deletado com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao deletar usuário.";
    }
    header('Location: ../view/lista-de-usuarios.php');
    exit;
}