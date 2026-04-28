<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Segurança: Se não houver email na sessão, volta para o login
if (!isset($_SESSION['email_recuperacao'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Código - MedAssist</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-5">
            
            <?php include('mensagem.php'); ?>

            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Verificação de Segurança</h4>
                </div>
                <div class="card-body p-4 text-center">
                    <p class="mb-4">
                        Enviamos um código para:<br>
                        <strong><?php echo $_SESSION['email_recuperacao']; ?></strong>
                    </p>

                    <form action="../controller/acoes.php" method="POST">
                        <div class="mb-4">
                            <label class="form-label d-block text-muted">Digite o código de 4 dígitos:</label>
                            <input type="text" name="codigo_verificacao" maxlength="4" placeholder="0000" required 
                                   class="form-control form-control-lg text-center fw-bold"
                                   style="font-size: 2rem; letter-spacing: 0.8rem; border: 2px solid #0d6efd;">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="validar_codigo" class="btn btn-primary btn-lg">Verificar Código</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="small text-muted mb-1">Não recebeu o código?</p>
                        <a href="esqueci-senha.php" class="text-decoration-none">Tentar enviar novamente</a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="login.php" class="text-secondary text-decoration-none small">Voltar ao login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>