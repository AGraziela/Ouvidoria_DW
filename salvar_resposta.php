<?php
// ============================================================
//  OUVIDORIA - EEEP DOM WALFRIDO
//  Arquivo: salvar_resposta.php
//  Salva feedback e status de uma manifestação
//  ⚠ Acesso exclusivo para administradores logados
// ============================================================

session_start();
require_once 'conexao.php';

header('Content-Type: application/json');

// ── Proteção: só admin ──
if (!isset($_SESSION['adm_id']) || $_SESSION['tipo'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado. Faça login como administrador.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método inválido.']);
    exit();
}

// ── Coleta e valida ──
$idmanifest = filter_input(INPUT_POST, 'idmanifest', FILTER_VALIDATE_INT);
$feedback   = trim($_POST['feedback'] ?? '');
$status     = trim($_POST['status']   ?? '');

$statusValidos = ['pendente', 'em_andamento', 'visualizada', 'finalizada'];

if (!$idmanifest || $idmanifest <= 0) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'ID de manifestação inválido.']);
    exit();
}

if (!in_array($status, $statusValidos)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Status inválido.']);
    exit();
}

// ── Atualiza no banco ──
// Salva feedback (pode ser vazio se só mudou status) e status
// Registra qual admin respondeu (idadm)
try {
    $sql = "UPDATE tbmanifestacoes
            SET feedback = :feedback,
                status   = :status,
                idadm    = :idadm
            WHERE idmanifest = :idmanifest";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':feedback'   => $feedback ?: null,   // NULL se não digitou nada
        ':status'     => $status,
        ':idadm'      => $_SESSION['adm_id'],
        ':idmanifest' => $idmanifest,
    ]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Manifestação não encontrada.']);
        exit();
    }

    echo json_encode(['status' => 'sucesso']);

} catch (PDOException $e) {
    error_log("Erro em salvar_resposta.php: " . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao salvar. Tente novamente.']);
}
