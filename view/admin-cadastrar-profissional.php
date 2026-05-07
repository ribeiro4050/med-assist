<?php
session_start();
require '../Model/conexao.php';

// Proteção: apenas administradores entram aqui
if (!isset($_SESSION['logado']) || $_SESSION['role_usuario'] !== 'admin') {
    $_SESSION['mensagem'] = "Acesso negado. Área restrita ao administrador.";
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar Profissional - MedAssist</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">

    <?php include('navbar.php'); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <?php include('mensagem.php'); ?>

                <div class="card shadow border-0">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Novo Cadastro Profissional</h5>
                        <a href="admin-painel.php" class="btn btn-outline-light btn-sm">Voltar ao Painel</a>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="../controller/UsuarioController.php" method="POST">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nome Completo</label>
                                    <input type="text" name="nome" class="form-control" placeholder="Ex: Dr. Luiz Oliveira" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">E-mail Corporativo</label>
                                    <input type="email" name="email" class="form-control" placeholder="nome.sobrenome@hospital.com" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Cargo / Função</label>
                                    <select name="role_usuario" id="role_select" class="form-select" required onchange="atualizarLabel()">
                                        <option value="" selected disabled>Selecione...</option>
                                        <option value="medico">Médico(a)</option>
                                        <option value="enfermeiro">Enfermeiro(a)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label id="label_registro" class="form-label fw-bold">Registro Profissional (CRM/COREN)</label>
                                    <input type="text" name="registro_profissional" class="form-control" placeholder="000000-SP" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">CPF</label>
                                    <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Senha Provisória</label>
                                    <input type="password" name="senha" class="form-control" required>
                                    <div class="form-text text-danger small">Informe ao funcionário para trocar no primeiro acesso.</div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 border-top pt-4">
                                <button type="submit" name="cadastrar_profissional" class="btn btn-dark btn-lg">
                                    Confirmar Cadastro Institucional
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
                
                <p class="text-center mt-4 text-muted small">
                    O MedAssist rastreia todos os cadastros realizados por este painel administrativo.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Pequeno script para mudar o texto conforme o cargo selecionado
        function atualizarLabel() {
            const select = document.getElementById('role_select');
            const label = document.getElementById('label_registro');
            
            if (select.value === 'medico') {
                label.innerHTML = 'CRM (Conselho Regional de Medicina)';
            } else if (select.value === 'enfermeiro') {
                label.innerHTML = 'COREN (Conselho Regional de Enfermagem)';
            }
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>