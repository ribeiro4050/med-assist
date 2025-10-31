<?php  
    define('HOST', 'localhost'); // alterar para o host do seu banco
    define('USUARIO', 'root'); // alterar para o usuario do seu banco
    define('SENHA', ''); // alterar para a senha do seu banco
    define('DB', 'med_assistdb'); // alterar para o nome do seu banco

    $conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die('Não foi possível conectar');
?>