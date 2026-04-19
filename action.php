<?php
require 'conexao.php';
session_start();

function limpar($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['action'])) {

    
  if ($_POST['action'] == 'Login_User') {
    $nome = limpar($_POST['nome']);
    $senha = trim($_POST['senha']); 

    $sql = "SELECT * FROM tbusuarios WHERE nome = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome]);
    $user = $stmt->fetch();

    
    if ($user && password_verify($senha, $user['senha'])) {
       
        $_SESSION['usuario_id'] = $user['idusu']; 
        $_SESSION['usuario_nome'] = $user['nome'];
        echo "sucesso";
    } else {
        echo "invalido";
    }
    exit;
}

    
    if ($_POST['action'] == 'Cadastro_btn') {
        $nome = limpar($_POST['nome']);
        $matricula = limpar($_POST['matricula']);
        $curso = limpar($_POST['curso']);
        $serie = limpar($_POST['serie']);
        $senha = $_POST['senha']; 

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO tbusuarios (nome, matricula, curso, serie, senha) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$nome, $matricula, $curso, $serie, $senha_hash])) {
                echo "sucesso";
            }
        } catch (PDOException $e) {
            echo "erro";
        }
    }
}