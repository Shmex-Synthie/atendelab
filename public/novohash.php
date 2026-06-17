

<?php
// A senha que você deseja criptografar
$senha = '123456';

// Cria o hash usando o algoritmo padrão atual do PHP (bcrypt ou similar)
$hash = password_hash($senha, PASSWORD_DEFAULT);

// Exibe o hash gerado
echo $hash;
?>