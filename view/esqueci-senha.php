<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Senha - MedAssist</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-5">
            
            <?php include('mensagem.php'); ?>

            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Recuperar Senha</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted text-center mb-4">
                        Informe seu e-mail cadastrado para receber o código de verificação de 4 dígitos.
                    </p>

                    <form action="../controller/AuthController.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail cadastrado</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="exemplo@email.com" required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="esqueci_senha" class="btn btn-primary">Enviar Código</button>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="login.php" class="text-decoration-none">Voltar ao início da tela de login</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-4 text-secondary small">
                &copy; 2026 MedAssist - Sistema de Apoio Clínico
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>