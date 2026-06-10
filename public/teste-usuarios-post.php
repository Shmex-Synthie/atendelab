<?php
// Arquivo temporário apenas para teste didático.

$dados = [
    'nome'   => 'Admin',
    'email'  => 'admin@atendelab.com',
    'senha'  => '123456',
    'perfil' => 'atendente',
    'status' => 'ativo'
];

$ch = curl_init('http://localhost/atendelab/public/?controller=usuarios&action=criar');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dados));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$resposta = curl_exec($ch);
curl_close($ch); // Function 'curl_close' is deprecated since PHP 8.5

echo '<pre>';
echo $resposta;
echo '</pre>';