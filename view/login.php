<?php
    // Inclui o arquivo de mensagem para exibir alertas de erro ou sucesso
    include('../view/mensagem.php'); 
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MedAssist</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    </head>
  <body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Acesso ao Sistema</h4>
                </div>
                <div class="card-body">
                    <form action="../controller/acoes.php" method="post">
                        <div id="login-padrao">
                            <h5 class="text-center text-primary mb-3">Login de Usuário</h5>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                        </div>

                        <div id="login-admin" style="display:none;">
                            <h5 class="text-center text-danger mb-3">Login de Profissional (CRM/COREN)</h5>
                            <div class="mb-3">
                                <label for="registro" class="form-label">CRM/COREN</label>
                                <input type="text" name="registro" id="registro" class="form-control" placeholder="Seu registro CRM ou COREN">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" name="senha" id="senha" class="form-control" required>
                        </div>
                        
                        <button type="submit" name="login_usuario" class="btn btn-primary w-100 mt-3">Acessar</button>

                        <hr>
                        
                        <p class="text-center">
                            <a href="#" id="toggle-login" class="text-decoration-none">
                                Alternar para Login de Profissional (CRM/COREN)
                            </a>
                        </p>
                        <p class="text-center">
                            Ainda não tem conta? <a href="usuario-create.php">Cadastre-se</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Script para alternar entre login padrão (Email) e admin (CRM/COREN)
        document.getElementById('toggle-login').addEventListener('click', function(e) {
            e.preventDefault();
            const loginPadrao = document.getElementById('login-padrao');
            const loginAdmin = document.getElementById('login-admin');
            const registroInput = document.getElementById('registro');
            const emailInput = document.getElementById('email');
            
            if (loginPadrao.style.display !== 'none') {
                // Alternar para Admin Login
                loginPadrao.style.display = 'none';
                loginAdmin.style.display = 'block';
                registroInput.setAttribute('required', 'required');
                emailInput.removeAttribute('required');
                emailInput.value = ''; // Limpa o email
                this.textContent = 'Alternar para Login Padrão (Email)';
            } else {
                // Alternar para Login Padrão
                loginPadrao.style.display = 'block';
                loginAdmin.style.display = 'none';
                registroInput.removeAttribute('required');
                registroInput.value = ''; // Limpa o registro
                emailInput.setAttribute('required', 'required');
                this.textContent = 'Alternar para Login de Profissional (CRM/COREN)';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>