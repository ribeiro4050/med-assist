 <!-- py-0 tira o padding vertical -->
  <nav class="navbar navbar-dark bg-dark py-0">
    <div class="container-md d-flex justify-content-between">
        <a href="index.php" class="navbar-brand">
            <img src="../img/logo.png" alt="Logo MedAssist" style="height: 50px; margin-bottom: 4px" > 
            MedAssist
        </a>
        <?php if(isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
            <ul class="navbar-nav d-flex flex-row align-items-center">
                
                <?php if(isset($_SESSION['role_usuario']) && $_SESSION['role_usuario'] === 'medico'): ?>
                <li class="nav-item me-3">
                    <a href="receitas.php" class="btn btn-outline-info btn-sm">
                        Receituário
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item me-3">
                    <a href="../controller/acoes.php?logout=true" class="btn btn-sm btn-outline-danger">Sair</a>
                </li>
                <li class="nav-item">
                    <a href="perfil.php" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                          <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                          <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                        </svg>
                    </a>
                </li>
            </ul>
        <?php else: ?>
             <a href="login.php" class="btn btn-outline-light">Login</a>
        <?php endif; ?>
    </div>
</nav>