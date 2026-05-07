<nav class="navbar navbar-dark bg-dark py-0">
    <div class="container-md d-flex justify-content-between">
        
        <?php
            // Determina para onde redirecionar
            $home_link = "../view/home.php"; // Padrão para não logado

            if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                if ($_SESSION['role_usuario'] === 'medico') {
                    $home_link = "../view/painel-medico.php";
                } elseif ($_SESSION['role_usuario'] === 'enfermeiro') {
                    $home_link = "../view/home-enfermeiro.php";
                } elseif ($_SESSION['role_usuario'] === 'paciente') {
                $home_link = "../view/home.php";
                } elseif ($_SESSION['role_usuario'] === 'admin') {
                    $home_link = "../view/home.php";
                } else {
                    $home_link = "../view/lista-de-usuarios.php";
                }
            }
        ?>
        
        <a href="<?= $home_link ?>" class="navbar-brand">
            <img src="../img/logo.png" alt="Logo MedAssist" style="height: 50px; margin-bottom: 4px" > 
            MedAssist
        </a>
        
        <?php if(isset($_SESSION['logado']) && $_SESSION['logado'] === true): ?>
            <ul class="navbar-nav d-flex flex-row align-items-center">
                
                <?php if(isset($_SESSION['role_usuario']) && $_SESSION['role_usuario'] === 'medico'): ?>
                <li class="nav-item me-3">
                    <a href="receitas.php" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-file-earmark-medical"></i>
                        Receituário
                    </a>
                </li>
                <?php endif; ?>

                <?php if($_SESSION['role_usuario'] === 'enfermeiro'): ?>
                <li class="nav-item me-3">
                    <a href="painel-enfermagem.php" class="btn btn-outline-primary btn-sm text-white border-white">
                        <i class="bi bi-clipboard-pulse"></i> Triagem
                    </a>
                </li>
                <?php endif; ?>

                <?php if(isset($_SESSION['role_usuario']) && $_SESSION['role_usuario'] === 'paciente'): ?>
                <li class="nav-item me-3">
                    <a href="../controller/historico-paciente-controller.php?id=<?php echo $_SESSION['id_usuario']; ?>" class="btn btn-outline-light btn-sm">
                    <span class="bi bi-clipboard-data"></span> &nbsp;
                    Histórico</a>
                </li>
                <?php endif; ?>
                
                <?php if($_SESSION['role_usuario'] === 'admin'): ?>
                <li class="nav-item me-3">
                    <a href="admin-painel.php" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-shield-lock"></i> Painel ADM
                    </a>
                </li>
                <?php endif; ?>
                
                <?php // Verificação de role para o botão Histórico para evitar repetição, mantendo apenas o bloco de teste original. ?>
                
                <li class="nav-item me-3">
                    <a href="../controller/AuthController.php?logout=true" class="btn btn-sm btn-outline-danger">Sair</a>
                </li>
                <li class="nav-item">
                    <a href="../view/perfil.php" class="nav-link">
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