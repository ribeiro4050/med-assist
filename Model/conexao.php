<?php  
define('HOST', 'localhost'); // alterar para o host do seu banco
define('USUARIO', 'root'); // alterar para o usuario do seu banco
define('SENHA', ''); // alterar para a senha do seu banco
define('DB', 'medassistdb'); // alterar para o nome do seu banco

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB);
    function filtrar_sql($input) {
    // mysqli_real_escape_string e trim previnem SQL Injection e removem espaços
    global $conexao;
    return mysqli_real_escape_string($conexao, trim($input));
}
?>