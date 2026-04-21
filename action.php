<?php
require 'conexao.php';
session_start();

function limpar($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['action'])) {

    
  if ($_POST['action'] == 'Login_User') {
    $email = trim($_POST['email_user']);
    $senha = trim($_POST['senha']); 

    $sql = "SELECT * FROM tbusuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    
    if ($user && password_verify($senha, $user['senha'])) {
       
        $_SESSION['usuario_id'] = $user['id_usu']; 
        $_SESSION['usuario_email'] = $user['email'];
        echo "sucesso";
    } else {
        echo "invalido";
    }
    exit;
}

    
    if ($_POST['action'] == 'Cadastro_btn') {
        $nome = limpar($_POST['nome']);
        $email = $_POST['email_cad'];
        $senha = $_POST['senha']; 
        $curso = limpar($_POST['curso']);
        $serie = limpar($_POST['serie']);
        $matricula = limpar($_POST['matricula']); 

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO tbusuarios (nome,email,senha,serie,curso,matricula) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$nome,$email,$senha_hash,$serie,$curso,$matricula])) {
                echo "sucesso";
            }
        } catch (PDOException $e) {
            echo "erro";
        }
    }
}
