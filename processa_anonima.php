<?php
// ============================================================
//  OUVIDORIA - EEEP DOM WALFRIDO
//  Arquivo: processa_anonima.php
//  Manifestação anônima: idusuario = NULL, sem sessão exigida
// ============================================================
 
require_once 'conexao.php';
 
header('Content-Type: application/json');
 
// --- SÓ ACEITA POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método inválido.']);
    exit();
}
 
// --- COLETA E SANITIZA ---
$tipo     = filter_input(INPUT_POST, 'tipo',    FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$assunto  = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
$mensagem = trim($_POST['mensagem'] ?? '');
 
// --- VALIDAÇÕES ---
$tiposValidos = ['elogio', 'sugestao', 'reclamacao', 'denuncia'];
 
if (!in_array($tipo, $tiposValidos)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Tipo de manifestação inválido.']);
    exit();
}
if (empty($assunto) || strlen($assunto) < 5) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'O assunto deve ter pelo menos 5 caracteres.']);
    exit();
}
if (empty($mensagem) || strlen($mensagem) < 20) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'A mensagem deve ter pelo menos 20 caracteres.']);
    exit();
}
 
// --- GERA PROTOCOLO ÚNICO ---
// Formato: OUV-2026-000001 (mesma sequência das identificadas)
try {
    $ano       = date('Y');
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM tbmanifestacoes");
    $total     = (int) $stmtCount->fetchColumn();
    $sequencia = str_pad($total + 1, 6, '0', STR_PAD_LEFT);
    $protocolo = "OUV-{$ano}-{$sequencia}";
} catch (PDOException $e) {
    error_log("Erro ao gerar protocolo: " . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno ao gerar protocolo.']);
    exit();
}
 
// --- INSERE NO BANCO ---
// idusuario = NULL  → manifestação anônima (coluna agora aceita NULL)
// idadm     = NULL  → preenchido pelo admin depois
// feedback  = NULL  → resposta do admin, preenchida depois
// contato   = protocolo único (UNIQUE KEY)
$sql = "INSERT INTO tbmanifestacoes
            (idtipo, idadm, idusuario, assunto, manifest, feedback, contato)
        VALUES
            (:idtipo, NULL, NULL, :assunto, :manifest, NULL, :contato)";
 
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':idtipo'   => $tipo,
        ':assunto'  => $assunto,
        ':manifest' => $mensagem,
        ':contato'  => $protocolo,
    ]);
 
    echo json_encode([
        'status'    => 'sucesso',
        'protocolo' => $protocolo,
    ]);
    exit();
 
} catch (PDOException $e) {
    error_log("Erro ao registrar manifestação anônima: " . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao enviar. Tente novamente.']);
    exit();
}
 