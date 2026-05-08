<?php
// ============================================================
//  OUVIDORIA - EEEP DOM WALFRIDO
//  Arquivo: action.php
//  Centraliza todas as ações AJAX do sistema
// ============================================================

require 'conexao.php';
session_start();

if (!isset($_POST['action'])) {
    echo "acao_invalida";
    exit;
}

// ════════════════════════════════════════
//  LOGIN USUÁRIO COMUM
// ════════════════════════════════════════
if ($_POST['action'] === 'Login_User') {

    $email = trim($_POST['email_user'] ?? '');
    $senha = trim($_POST['senha']      ?? '');

    if (empty($email) || empty($senha)) {
        echo "invalido";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM tbusuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        // Regenera o ID de sessão para prevenir session fixation
        session_regenerate_id(true);

        $_SESSION['usuario_id']    = $user['id_usu'];
        $_SESSION['usuario_email'] = $user['email'];
        $_SESSION['usuario_nome']  = $user['nome'];
        $_SESSION['tipo']          = 'usuario';

        echo "sucesso";
    } else {
        echo "invalido";
    }
    exit;
}

// ════════════════════════════════════════
//  LOGIN ADMINISTRADOR
// ════════════════════════════════════════
if ($_POST['action'] === 'Login_Admin') {

    $email = trim($_POST['email_adm'] ?? '');
    $senha = trim($_POST['senha']     ?? '');

    if (empty($email) || empty($senha)) {
        echo "invalido";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM tbadm WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $adm = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($adm && password_verify($senha, $adm['senha'])) {
        // Regenera o ID de sessão para prevenir session fixation
        session_regenerate_id(true);

        $_SESSION['adm_id']    = $adm['idadm'];
        $_SESSION['adm_email'] = $adm['email'];
        $_SESSION['adm_nome']  = $adm['nome'];
        $_SESSION['tipo']      = 'admin';

        echo "sucesso";
    } else {
        echo "invalido";
    }
    exit;
}

// ════════════════════════════════════════
//  CADASTRO DE USUÁRIO
// ════════════════════════════════════════
if ($_POST['action'] === 'Cadastro_btn') {

    $nome      = trim($_POST['nome']      ?? '');
    $email     = trim($_POST['email_cad'] ?? '');
    $senha     = trim($_POST['senha']     ?? '');
    $curso     = trim($_POST['curso']     ?? '');
    $serie     = trim($_POST['serie']     ?? '');
    $matricula = trim($_POST['matricula'] ?? '');

    if (empty($nome) || empty($email) || empty($senha) || empty($curso) || empty($serie) || empty($matricula)) {
        echo "erro";
        exit;
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $sql  = "INSERT INTO tbusuarios (nome, email, senha, serie, curso, matricula)
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $senha_hash, $serie, $curso, $matricula]);
        echo "sucesso";
    } catch (PDOException $e) {
        // Erro de email duplicado
        if ($e->getCode() == 23000) {
            echo "email_duplicado";
        } else {
            error_log("Erro cadastro: " . $e->getMessage());
            echo "erro";
        }
    }
    exit;
}

echo "acao_invalida";
