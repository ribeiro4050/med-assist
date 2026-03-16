<?php  
    // Configurações de Conexão - Aiven MySQL
    // Copie os dados do painel do Aiven (Service Management -> Overview)
    
    define('HOST', 'med-assist-med-assist.g.aivencloud.com');
    define('PORT', '13589');
    define('USUARIO', 'avnadmin');
    define('SENHA', 'AVNS_iNJTi_85-Lp302IwPsO');
    define('DB', 'defaultdb');

    // A conexão precisa da porta para funcionar fora do localhost
    $conexao = mysqli_connect(HOST, USUARIO, SENHA, DB, PORT);

    // Verificação de segurança para o seu TCC
    if (!$conexao) {
        die("Erro de conexão: " . mysqli_connect_error());
    }

    // Garante que o PHP entenda caracteres especiais e acentos (UTF-8)
    mysqli_set_charset($conexao, "utf8");
?>