<?php
// ============================================================
//  OUVIDORIA - EEEP DOM WALFRIDO
//  Arquivo: busca_protocolo.php
//  Retorna JSON com dados de uma manifestação pelo protocolo
//  Não exige login (tela pública de acompanhamento)
// ============================================================

require_once 'conexao.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Método inválido.']);
    exit();
}

$protocolo = trim($_GET['protocolo'] ?? '');

if (empty($protocolo)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Informe o protocolo.']);
    exit();
}

// Expõe apenas os campos necessários — nunca idusuario nem idadm
$sql = "SELECT idtipo, assunto, manifest, feedback, status, contato
        FROM tbmanifestacoes
        WHERE contato = :protocolo
        LIMIT 1";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':protocolo' => strtoupper($protocolo)]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['status' => 'erro', 'mensagem' => 'Protocolo não encontrado. Verifique o código e tente novamente.']);
        exit();
    }

    echo json_encode(['status' => 'sucesso', 'dados' => $row]);

} catch (PDOException $e) {
    error_log("Erro em busca_protocolo.php: " . $e->getMessage());
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno. Tente novamente.']);
}
