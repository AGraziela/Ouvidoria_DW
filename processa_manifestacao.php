<?php
// ============================================================
//  OUVIDORIA - EEEP DOM WALFRIDO
//  Arquivo: processa_manifestacao.php
//  Campos reais da tbmanifestacoes:
//  idmanifest | idtipo | idadm | idusuario | assunto | manifest | feedback | contato
// ============================================================

session_start();
require_once 'conexao.php';

// --- VERIFICA SE ESTÁ LOGADO ---
if (!isset($_SESSION['usuario_id'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// --- SÓ ACEITA POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manisfestacao.php');
    exit;
}

// --- COLETA DOS DADOS ---
$tipo      = filter_input(INPUT_POST, 'tipo',    FILTER_SANITIZE_SPECIAL_CHARS);
$assunto   = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_SPECIAL_CHARS);
$mensagem  = trim($_POST['mensagem'] ?? '');
$usuarioId = $_SESSION['usuario_id'];

// --- VALIDAÇÕES ---
$tiposValidos = ['elogio', 'sugestao', 'reclamacao', 'denuncia'];

if (!in_array($tipo, $tiposValidos)) {
    header("Location: manisfestacao.php?erro=" . urlencode("Tipo de manifestação inválido."));
    exit;
}

if (empty($assunto) || strlen($assunto) < 5) {
    header("Location: manisfestacao.php?erro=" . urlencode("O assunto deve ter pelo menos 5 caracteres."));
    exit;
}

if (empty($mensagem) || strlen($mensagem) < 20) {
    header("Location: manisfestacao.php?erro=" . urlencode("A mensagem deve ter pelo menos 20 caracteres."));
    exit;
}

// --- GERA PROTOCOLO ÚNICO (salvo no campo 'contato') ---
// Formato: OUV-2026-000001
$ano  = date('Y');
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tbmanifestacoes");
$stmt->execute();
$total     = $stmt->get_result()->fetch_assoc()['total'];
$sequencia = str_pad($total + 1, 6, '0', STR_PAD_LEFT);
$protocolo = "OUV-{$ano}-{$sequencia}";
$stmt->close();

// --- INSERE NO BANCO ---
// idmanifest → AUTO_INCREMENT (banco gera automaticamente)
// idadm      → NULL (preenchido pelo administrador depois)
// feedback   → NULL (resposta do admin, preenchida depois)
// contato    → protocolo gerado para o aluno acompanhar
$stmt = $conn->prepare(
    "INSERT INTO tbmanifestacoes (idmanifest, idtipo, idadm, idusuario, assunto, manifest, feedback)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param('sisss', $tipo, $usuarioId, $assunto, $mensagem, $protocolo);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: confirmacao.php?protocolo=" . urlencode($protocolo));
    exit;
} else {
    error_log("Erro ao registrar manifestação: " . $stmt->error);
    $stmt->close();
    $conn->close();
    header("Location: manisfestacao.php?erro=" . urlencode("Erro ao enviar. Tente novamente."));
    exit;
}
