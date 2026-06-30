<?php

$host = '127.0.0.1';
$banco = 'atendelab';
$port = '3307';
$usuario = 'root';
$senha = '';

try {

    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$banco};charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    exit('Erro ao conectar com o banco de dados.');
}