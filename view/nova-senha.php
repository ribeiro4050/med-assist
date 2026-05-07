<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Segurança: se não validou o código, não entra aqui
if (!isset($_SESSION['pode_mudar_senha']) || !isset($_SESSION['email_recuperacao'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nova Senha - MedAssist</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-5">
            
            <?php include('mensagem.php'); ?>

            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Redefinir Senha</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">
                        Crie uma nova senha segura para acessar sua conta.
                    </p>

                    <form action="../controller/AuthController.php" method="POST">
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <div class="input-group">
                                <input type="password" name="nova_senha" id="nova_senha" class="form-control" placeholder="No mínimo 6 caracteres" required>
                                <button class="btn btn-outline-secondary" type="button" id="btn-senha">
                                    <i class="fa fa-eye" id="toggleSenha"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirmar_senha" class="form-label">Confirme a Nova Senha</label>
                            <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control" placeholder="Repita a senha" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="atualizar_senha_esquecida" class="btn btn-primary btn-lg">Atualizar Senha</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="login.php" class="text-secondary text-decoration-none small">Cancelar e voltar ao login</a>
            </div>
        </div>
    </div>

    <script>
        // Lógica do Olhinho (Melhorada para Bootstrap)
        const btnSenha = document.querySelector('#btn-senha');
        const iconSenha = document.querySelector('#toggleSenha');
        const inputSenha = document.querySelector('#nova_senha');
        const inputConfirmar = document.querySelector('#confirmar_senha');

        btnSenha.addEventListener('click', function () {
            // Alterna o tipo do input
            const type = inputSenha.getAttribute('type') === 'password' ? 'text' : 'password';
            inputSenha.setAttribute('type', type);
            inputConfirmar.setAttribute('type', type); // Opcional: mostra em ambos
            
            // Alterna o ícone
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>