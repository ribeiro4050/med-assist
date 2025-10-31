<?php 
if(isset($_SESSION['mensagem'])): // verifica se a variável de sessão mensagem existe
?>

<div class="alert alert-warning alert-dismissible fade show" role="alert">
<!-- role="alert é usado para acessibilidade, indica que este elemento é um alerta" -->
  <?= $_SESSION['mensagem']; ?>
    <!-- exibe a mensagem armazenada na variável de sessão -->
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    <!-- botão que serve para fechar a mensagem de alerta-->
</div>

<?php 
    unset($_SESSION['mensagem']); // remove a variável de sessão mensagem para que a mensagem não apareça novamente ao atualizar a página
    endif;
    // fim do if iniciado com (isset($_SESSION['mensagem'])) na linha 2
    // usa-se o endif para fechar o if quando se está usando a sintaxe alternativa do PHP (usando : ao invés de { } para abrir e fechar o bloco de código)
?>