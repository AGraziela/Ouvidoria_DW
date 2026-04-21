<?php 
$pdo = new PDO("mysql:host=localhost;dbname=ouvidoria_dw", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>