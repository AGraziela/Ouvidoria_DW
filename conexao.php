<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=ouvidoria_dw;charset=utf8mb4","root", "",
    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>