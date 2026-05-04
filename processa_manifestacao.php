<?php
// ============================================================
//  OUVIDORIA - EEEP DOM WALFRIDO
//  Arquivo: processa_manifestacao.php
//
//  Estrutura real da tabela tbmanifestacoes:
//  idmanifest (AI PK) | idtipo (varchar 20) | idadm (int, null)
//  | idusuario (int) | assunto (varchar 200) | manifest (text)
//  | feedback (text, null) | contato (varchar 20, UNIQUE)
// ============================================================
 
session_start();
require_once 'conexao.php'; // fornece $pdo (PDO)
 
// --- VERIFICA SE ESTÁ LOGADO ---
if (!isset($_SESSION['usuario_id'])) {
    session_destroy();
    header("Location: forms.php");
    exit();
}
 
// --- SÓ ACEITA POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manifestacao.php');
    exit();
}
 
// --- COLETA E SANITIZA OS DADOS ---
$tipo      = filter_input(INPUT_POST, 'tipo',    FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$assunto   = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$mensagem  = trim($_POST['mensagem'] ?? '');
$usuarioId = (int) $_SESSION['usuario_id'];
 
// --- VALIDAÇÕES ---
// idtipo é varchar(20) → a string do formulário vai direto para o banco
$tiposValidos = ['elogio', 'sugestao', 'reclamacao', 'denuncia'];
 
if (!in_array($tipo, $tiposValidos)) {
    header("Location: manifestacao.php?erro=" . urlencode("Tipo de manifestação inválido."));
    exit();
}
 
if (empty($assunto) || strlen($assunto) < 5) {
    header("Location: manifestacao.php?erro=" . urlencode("O assunto deve ter pelo menos 5 caracteres."));
    exit();
}
 
if (empty($mensagem) || strlen($mensagem) < 20) {
    header("Location: manifestacao.php?erro=" . urlencode("A mensagem deve ter pelo menos 20 caracteres."));
    exit();
}
 
// --- GERA PROTOCOLO ÚNICO (campo 'contato' tem UNIQUE KEY no banco) ---
// Formato: OUV-2026-000001
try {
    $ano       = date('Y');
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM tbmanifestacoes");
    $total     = (int) $stmtCount->fetchColumn();
    $sequencia = str_pad($total + 1, 6, '0', STR_PAD_LEFT);
    $protocolo = "OUV-{$ano}-{$sequencia}";
} catch (PDOException $e) {
    error_log("Erro ao gerar protocolo: " . $e->getMessage());
    header("Location: manifestacao.php?erro=" . urlencode("Erro interno. Tente novamente."));
    exit();
}
 
// --- INSERE NO BANCO ---
// idtipo   = varchar(20) → recebe string direto: 'elogio', 'sugestao', etc.
// idadm    = NULL (preenchido pelo admin depois)
// feedback = NULL (resposta do admin, preenchida depois)
// contato  = protocolo único gerado acima (UNIQUE KEY)
$sql = "INSERT INTO tbmanifestacoes 
            (idtipo, idadm, idusuario, assunto, manifest, feedback, contato)
        VALUES 
            (:idtipo, NULL, :idusuario, :assunto, :manifest, NULL, :contato)";
 
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':idtipo'    => $tipo,
        ':idusuario' => $usuarioId,
        ':assunto'   => $assunto,
        ':manifest'  => $mensagem,
        ':contato'   => $protocolo,
    ]);
 
    header("Location: confirmacao.php?protocolo=" . urlencode($protocolo));
    exit();
 
} catch (PDOException $e) {
    error_log("Erro ao registrar manifestação: " . $e->getMessage());
    header("Location: manifestacao.php?erro=" . urlencode("Erro ao enviar sua manifestação. Tente novamente."));
    exit();
}