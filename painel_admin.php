<?php
// ============================================================
//  OUVIDORIA - EEEP DOM WALFRIDO
//  Arquivo: painel_admin.php
//  Acesso exclusivo para administradores
// ============================================================
session_start();
require 'conexao.php';

// Proteção: só admin logado
if (!isset($_SESSION['adm_id']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: forms.php");
    exit();
}

$admNome = $_SESSION['adm_nome'] ?? 'Administrador';

// Filtro por status (URL: ?filtro=pendente | em_andamento | visualizada | finalizada | todas)
$filtroValidos = ['todas', 'pendente', 'em_andamento', 'visualizada', 'finalizada'];
$filtro = in_array($_GET['filtro'] ?? '', $filtroValidos) ? $_GET['filtro'] : 'todas';

// Query com filtro
if ($filtro === 'todas') {
    $stmt = $pdo->query(
        "SELECT m.*, u.nome AS nome_usuario, u.curso, u.serie, u.matricula
         FROM tbmanifestacoes m
         LEFT JOIN tbusuarios u ON m.idusuario = u.id_usu
         ORDER BY m.idmanifest DESC"
    );
} else {
    $stmt = $pdo->prepare(
        "SELECT m.*, u.nome AS nome_usuario, u.curso, u.serie, u.matricula
         FROM tbmanifestacoes m
         LEFT JOIN tbusuarios u ON m.idusuario = u.id_usu
         WHERE m.status = ?
         ORDER BY m.idmanifest DESC"
    );
    $stmt->execute([$filtro]);
}
$manifestacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contadores para o resumo
$stmtCount = $pdo->query("SELECT status, COUNT(*) as total FROM tbmanifestacoes GROUP BY status");
$contadores = ['pendente' => 0, 'em_andamento' => 0, 'visualizada' => 0, 'finalizada' => 0];
$totalGeral = 0;
foreach ($stmtCount->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $contadores[$row['status']] = (int)$row['total'];
    $totalGeral += (int)$row['total'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Ouvidoria DW</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style_manifest.css">
    <style>
        /* ══ BARRA ADMIN ══ */
        .barra-admin {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding: 12px 18px;
            background: linear-gradient(135deg, #1e631d, #0daa0a);
            border-radius: 14px;
            flex-wrap: wrap;
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
        }
        .admin-info .badge-admin {
            background: rgba(255,255,255,0.2);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .admin-info .nome-adm {
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
        }
        .btn-logout-adm {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: 2px solid rgba(255,255,255,0.5);
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-logout-adm:hover {
            background: rgba(255,255,255,0.28);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ══ RESUMO CARDS ══ */
        .resumo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .resumo-card {
            background: #f4f7f4;
            border: 1.5px solid #dceadc;
            border-radius: 14px;
            padding: 16px 10px;
            text-align: center;
            cursor: pointer;
            transition: 0.25s;
            text-decoration: none;
        }
        .resumo-card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(30,99,29,0.12); }
        .resumo-card.ativo { border-color: #1e631d; background: #e8f5e8; box-shadow: 0 4px 14px rgba(30,99,29,0.18); }
        .resumo-card .num  { font-size: 1.8rem; font-weight: 700; display: block; }
        .resumo-card .leg  { font-size: 0.68rem; color: #666; text-transform: uppercase; letter-spacing: 0.8px; }
        .resumo-card.total       .num { color: #1e631d; }
        .resumo-card.pendentes   .num { color: #ff8c00; }
        .resumo-card.andamento   .num { color: #0d6efd; }
        .resumo-card.visualizada .num { color: #6f42c1; }
        .resumo-card.finalizada  .num { color: #0daa0a; }

        /* ══ FILTROS ══ */
        .filtros-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .btn-filtro {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            border: 2px solid #dceadc;
            background: #f4f7f4;
            color: #555;
            cursor: pointer;
            text-decoration: none;
            transition: 0.25s;
        }
        .btn-filtro:hover  { border-color: #1e631d; color: #1e631d; }
        .btn-filtro.ativo  { background: #1e631d; color: #fff; border-color: #1e631d; }

        /* ══ BADGE TIPO ══ */
        .badge-tipo {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 999px; font-size: 0.73rem; font-weight: 700;
        }
        .badge-elogio     { background: #d4edda; color: #155724; }
        .badge-sugestao   { background: #d1ecf1; color: #0c5460; }
        .badge-reclamacao { background: #fff3cd; color: #856404; }
        .badge-denuncia   { background: #f8d7da; color: #721c24; }

        /* ══ BADGE STATUS ══ */
        .badge-status {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 999px; font-size: 0.73rem; font-weight: 700;
        }
        .st-pendente    { background: #fff3cd; color: #856404; }
        .st-em_andamento{ background: #cce5ff; color: #004085; }
        .st-visualizada { background: #e2d9f3; color: #4a235a; }
        .st-finalizada  { background: #d4edda; color: #155724; }

        /* ══ CARD MANIFESTAÇÃO ══ */
        .manifest-card {
            background: #fff;
            border: 1.5px solid #dceadc;
            border-radius: 16px;
            margin-bottom: 14px;
            overflow: hidden;
            transition: box-shadow 0.25s, transform 0.25s;
        }
        .manifest-card:hover { box-shadow: 0 6px 22px rgba(30,99,29,0.1); transform: translateY(-2px); }

        .manifest-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 18px; cursor: pointer; user-select: none;
            gap: 10px; flex-wrap: wrap;
            background: #f8fdf8;
            border-bottom: 1px solid transparent;
            transition: border-color 0.25s;
        }
        .manifest-card.aberto .manifest-card-header { border-color: #c8e6c8; }
        .card-esquerda { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .card-assunto  { font-size: 0.9rem; font-weight: 600; color: #1a1a1a; }
        .card-meta     { font-size: 0.73rem; color: #888; letter-spacing: 0.5px; }
        .chevron { color: #1e631d; font-size: 1.1rem; transition: transform 0.3s; flex-shrink: 0; }
        .manifest-card.aberto .chevron { transform: rotate(180deg); }

        /* Accordion body */
        .manifest-card-body {
            max-height: 0; overflow: hidden;
            transition: max-height 0.45s ease, padding 0.3s;
            padding: 0 18px;
        }
        .manifest-card.aberto .manifest-card-body { max-height: 900px; padding: 20px 20px 24px; }

        /* Campos */
        .campo-detalhe { margin-bottom: 14px; }
        .campo-detalhe label {
            font-size: 0.7rem; font-weight: 700; color: #1e631d;
            text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px;
        }
        .campo-detalhe .valor {
            background: #f4f7f4; border: 1.5px solid #dceadc;
            border-radius: 12px; padding: 12px 14px;
            font-size: 0.9rem; color: #333; line-height: 1.65; white-space: pre-wrap;
        }

        /* Info do usuário */
        .info-usuario {
            background: #f4f7f4; border: 1.5px solid #dceadc;
            border-radius: 12px; padding: 14px 16px; margin-bottom: 16px;
            display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 8px;
        }
        .info-usuario .info-item label {
            font-size: 0.68rem; font-weight: 700; color: #1e631d;
            text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 3px;
        }
        .info-usuario .info-item span { font-size: 0.87rem; color: #333; font-weight: 500; }

        /* ══ ÁREA DE RESPOSTA ══ */
        .area-resposta {
            background: linear-gradient(135deg, #f0faf0, #e8f5e8);
            border: 2px solid #1e631d;
            border-radius: 16px;
            padding: 20px;
            margin-top: 6px;
        }
        .area-resposta .resp-titulo {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.78rem; font-weight: 700; color: #1e631d;
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px;
        }

        /* Resposta já existente */
        .resposta-existente {
            background: #fff; border: 1.5px solid #c8e6c8;
            border-radius: 12px; padding: 14px 16px;
            font-size: 0.9rem; color: #2d5a2d; line-height: 1.65;
            white-space: pre-wrap; margin-bottom: 14px;
        }

        /* Textarea de resposta */
        .textarea-resposta {
            width: 100%; border: 2px solid #c8e6c8;
            border-radius: 12px; padding: 12px 14px;
            font-size: 0.9rem; font-family: 'Poppins', sans-serif;
            color: #333; background: #fff; outline: none;
            transition: border-color 0.25s; resize: vertical; min-height: 100px;
            margin-bottom: 14px;
        }
        .textarea-resposta:focus { border-color: #1e631d; }

        /* ══ BOTÕES DE STATUS ══ */
        .status-botoes {
            display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px;
        }
        .status-botoes label {
            font-size: 0.72rem; font-weight: 700; color: #1e631d;
            text-transform: uppercase; letter-spacing: 1px;
            display: block; width: 100%; margin-bottom: 8px;
        }
        .btn-status {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; border-radius: 12px;
            font-size: 0.82rem; font-weight: 600;
            font-family: 'Poppins', sans-serif;
            border: 2px solid transparent;
            cursor: pointer; transition: 0.25s;
        }
        .btn-status:hover { transform: translateY(-2px); box-shadow: 0 5px 14px rgba(0,0,0,0.12); }

        .btn-andamento  { background: #cce5ff; color: #004085; border-color: #b8d4f5; }
        .btn-andamento:hover  { background: #0d6efd; color: #fff; border-color: #0d6efd; }
        .btn-andamento.ativo  { background: #0d6efd; color: #fff; border-color: #0d6efd; }

        .btn-visualizada { background: #e2d9f3; color: #4a235a; border-color: #cfc0ea; }
        .btn-visualizada:hover { background: #6f42c1; color: #fff; border-color: #6f42c1; }
        .btn-visualizada.ativo { background: #6f42c1; color: #fff; border-color: #6f42c1; }

        .btn-finalizada { background: #d4edda; color: #155724; border-color: #b8ddc4; }
        .btn-finalizada:hover { background: #0daa0a; color: #fff; border-color: #0daa0a; }
        .btn-finalizada.ativo { background: #0daa0a; color: #fff; border-color: #0daa0a; }

        /* Botão enviar resposta */
        .btn-enviar-resp {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 12px 26px; background: #1e631d; color: #fff;
            border: none; border-radius: 12px; font-size: 0.9rem; font-weight: 600;
            font-family: 'Poppins', sans-serif; cursor: pointer; transition: 0.3s;
        }
        .btn-enviar-resp:hover { background: #0daa0a; transform: translateY(-2px); box-shadow: 0 5px 16px rgba(30,99,29,0.3); }
        .btn-enviar-resp:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }

        /* Feedback de salvamento */
        .salvo-msg {
            display: none; font-size: 0.82rem; font-weight: 600; color: #0daa0a;
            align-items: center; gap: 6px; margin-left: 10px;
        }
        .salvo-msg.visivel { display: inline-flex; animation: fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        /* ══ LISTA VAZIA ══ */
        .lista-vazia {
            text-align: center; padding: 40px 20px; color: #888;
        }
        .lista-vazia i { font-size: 3rem; color: #ccc; display: block; margin-bottom: 14px; }

        /* ══ OVERLAY LOGOUT ══ */
        .overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 9000;
            justify-content: center; align-items: center; backdrop-filter: blur(4px);
        }
        .overlay.ativo { display: flex; }
        .popup-box {
            background: #fff; border-radius: 22px; padding: 44px 36px 38px;
            max-width: 380px; width: 92%; text-align: center;
            box-shadow: 0 20px 55px rgba(0,0,0,0.18);
            animation: popIn 0.38s cubic-bezier(0.34,1.56,0.64,1) forwards;
        }
        @keyframes popIn {
            from { opacity:0; transform:scale(0.72) translateY(28px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
        .popup-icone {
            width:78px; height:78px;
            background: linear-gradient(135deg,#ff8c00,#ffb347);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            margin:0 auto 18px; box-shadow:0 6px 22px rgba(255,140,0,0.3);
        }
        .popup-icone i { font-size:2.2rem; color:#fff; }
        .popup-box h2 { font-size:1.3rem; font-weight:700; color:#1a1a1a; margin-bottom:8px; }
        .popup-box p  { font-size:0.87rem; color:#666; margin-bottom:24px; }
        .popup-botoes { display:flex; gap:10px; justify-content:center; }
        .btn-cancelar {
            padding:11px 22px; border:2px solid #ccc; border-radius:12px;
            background:transparent; color:#555; font-weight:600;
            font-family:'Poppins',sans-serif; cursor:pointer; transition:0.3s;
        }
        .btn-cancelar:hover { border-color:#888; color:#222; }
        .btn-confirmar {
            padding:11px 22px; border:none; border-radius:12px;
            background:#cc0000; color:#fff; font-weight:600;
            font-family:'Poppins',sans-serif; cursor:pointer; transition:0.3s;
        }
        .btn-confirmar:hover { background:#aa0000; }

        /* Anonimo badge */
        .badge-anonimo {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; background: #f8f9fa; color: #666;
            border: 1px solid #dee2e6; border-radius: 999px;
            font-size: 0.7rem; font-weight: 600;
        }
    </style>
</head>
<body>

<!-- ══ POPUP CONFIRMAR LOGOUT ══ -->
<div class="overlay" id="overlayLogout">
    <div class="popup-box">
        <div class="popup-icone"><i class="bi bi-box-arrow-right"></i></div>
        <h2>Sair do painel?</h2>
        <p>Sua sessão de administrador será encerrada.</p>
        <div class="popup-botoes">
            <button class="btn-cancelar" onclick="document.getElementById('overlayLogout').classList.remove('ativo')">Cancelar</button>
            <button class="btn-confirmar" onclick="window.location.href='logout_admin.php'">Sim, sair</button>
        </div>
    </div>
</div>

<main class="container" style="max-width:1000px;">
    <div class="card_formulario">
        <img src="logo_escola.png" alt="Logo Escola" class="logo-escola">
        <h1 class="titulo_form">Painel Admin</h1>
        <p class="subtitulo">Gerencie e responda as manifestações da ouvidoria.</p>

        <!-- Barra admin -->
        <div class="barra-admin">
            <div class="admin-info">
                <span class="badge-admin"><i class="bi bi-shield-fill-check"></i> Admin</span>
                <span class="nome-adm"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($admNome) ?></span>
            </div>
            <button class="btn-logout-adm" onclick="document.getElementById('overlayLogout').classList.add('ativo')">
                <i class="bi bi-box-arrow-right"></i> Sair
            </button>
        </div>

        <!-- Resumo contadores -->
        <div class="resumo-grid">
            <a href="?filtro=todas" class="resumo-card total <?= $filtro==='todas'?'ativo':'' ?>">
                <span class="num"><?= $totalGeral ?></span>
                <span class="leg">Total</span>
            </a>
            <a href="?filtro=pendente" class="resumo-card pendentes <?= $filtro==='pendente'?'ativo':'' ?>">
                <span class="num"><?= $contadores['pendente'] ?></span>
                <span class="leg">Pendentes</span>
            </a>
            <a href="?filtro=em_andamento" class="resumo-card andamento <?= $filtro==='em_andamento'?'ativo':'' ?>">
                <span class="num"><?= $contadores['em_andamento'] ?></span>
                <span class="leg">Em Andamento</span>
            </a>
            <a href="?filtro=visualizada" class="resumo-card visualizada <?= $filtro==='visualizada'?'ativo':'' ?>">
                <span class="num"><?= $contadores['visualizada'] ?></span>
                <span class="leg">Visualizadas</span>
            </a>
            <a href="?filtro=finalizada" class="resumo-card finalizada <?= $filtro==='finalizada'?'ativo':'' ?>">
                <span class="num"><?= $contadores['finalizada'] ?></span>
                <span class="leg">Finalizadas</span>
            </a>
        </div>

        <!-- Filtros rápidos -->
        <div class="filtros-bar">
            <a href="?filtro=todas"       class="btn-filtro <?= $filtro==='todas'?'ativo':'' ?>"><i class="bi bi-grid-fill"></i> Todas</a>
            <a href="?filtro=pendente"    class="btn-filtro <?= $filtro==='pendente'?'ativo':'' ?>"><i class="bi bi-clock-fill"></i> Pendentes</a>
            <a href="?filtro=em_andamento"class="btn-filtro <?= $filtro==='em_andamento'?'ativo':'' ?>"><i class="bi bi-arrow-repeat"></i> Em Andamento</a>
            <a href="?filtro=visualizada" class="btn-filtro <?= $filtro==='visualizada'?'ativo':'' ?>"><i class="bi bi-eye-fill"></i> Visualizadas</a>
            <a href="?filtro=finalizada"  class="btn-filtro <?= $filtro==='finalizada'?'ativo':'' ?>"><i class="bi bi-check-circle-fill"></i> Finalizadas</a>
        </div>

        <!-- Lista de manifestações -->
        <?php if (empty($manifestacoes)): ?>
            <div class="lista-vazia">
                <i class="bi bi-inbox"></i>
                <p>Nenhuma manifestação encontrada para este filtro.</p>
            </div>
        <?php else: ?>

            <?php
            $tipoIcons  = ['elogio'=>'fa-smile','sugestao'=>'fa-lightbulb','reclamacao'=>'fa-exclamation-circle','denuncia'=>'fa-gavel'];
            $tipoLabels = ['elogio'=>'Elogio','sugestao'=>'Sugestão','reclamacao'=>'Reclamação','denuncia'=>'Denúncia'];
            $statusIcons = [
                'pendente'     => 'bi-clock-fill',
                'em_andamento' => 'bi-arrow-repeat',
                'visualizada'  => 'bi-eye-fill',
                'finalizada'   => 'bi-check-circle-fill',
            ];
            $statusLabels = [
                'pendente'     => 'Pendente',
                'em_andamento' => 'Em Andamento',
                'visualizada'  => 'Visualizada',
                'finalizada'   => 'Finalizada',
            ];

            foreach ($manifestacoes as $i => $m):
                $tipoKey   = strtolower($m['idtipo']);
                $statusKey = $m['status'] ?? 'pendente';
                $anonimo   = empty($m['idusuario']);
            ?>
            <div class="manifest-card" id="card-<?= $i ?>">

                <!-- Cabeçalho clicável -->
                <div class="manifest-card-header" onclick="toggleCard(<?= $i ?>)">
                    <div class="card-esquerda">
                        <span class="badge-tipo badge-<?= $tipoKey ?>">
                            <i class="fas <?= $tipoIcons[$tipoKey] ?? 'fa-comment' ?>"></i>
                            <?= $tipoLabels[$tipoKey] ?? $tipoKey ?>
                        </span>
                        <span class="badge-status st-<?= $statusKey ?>">
                            <i class="bi <?= $statusIcons[$statusKey] ?? 'bi-clock-fill' ?>"></i>
                            <?= $statusLabels[$statusKey] ?? $statusKey ?>
                        </span>
                        <div>
                            <div class="card-assunto"><?= htmlspecialchars($m['assunto']) ?></div>
                            <div class="card-meta">
                                <?= htmlspecialchars($m['contato']) ?>
                                <?php if ($anonimo): ?>
                                    &nbsp;<span class="badge-anonimo"><i class="bi bi-shield-lock-fill"></i> Anônimo</span>
                                <?php else: ?>
                                    &nbsp;·&nbsp; <?= htmlspecialchars($m['nome_usuario'] ?? '—') ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-down chevron"></i>
                </div>

                <!-- Corpo accordion -->
                <div class="manifest-card-body">

                    <!-- Dados do autor (se identificado) -->
                    <?php if (!$anonimo): ?>
                    <div class="info-usuario">
                        <div class="info-item">
                            <label><i class="bi bi-person-fill"></i> Aluno</label>
                            <span><?= htmlspecialchars($m['nome_usuario'] ?? '—') ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="bi bi-mortarboard-fill"></i> Curso</label>
                            <span><?= htmlspecialchars($m['curso'] ?? '—') ?></span>
                        </div>
                        <div class="info-item">
                            <label><i class="bi bi-layers-fill"></i> Série</label>
                            <span><?= ($m['serie'] ?? '—') ?>º Ano</span>
                        </div>
                        <div class="info-item">
                            <label><i class="bi bi-pencil-square"></i> Matrícula</label>
                            <span><?= htmlspecialchars($m['matricula'] ?? '—') ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Mensagem -->
                    <div class="campo-detalhe">
                        <label><i class="bi bi-textarea-resize"></i> Mensagem recebida</label>
                        <div class="valor"><?= htmlspecialchars($m['manifest']) ?></div>
                    </div>

                    <!-- Área de resposta + controle de status -->
                    <div class="campo-detalhe">
                        <label><i class="bi bi-reply-fill"></i> Resposta & Status</label>

                        <div class="area-resposta">
                            <div class="resp-titulo">
                                <i class="bi bi-pencil-fill"></i> Responder Manifestação
                            </div>

                            <!-- Botões de status -->
                            <div class="status-botoes">
                                <label>Definir Status:</label>
                                <button type="button"
                                    class="btn-status btn-andamento <?= $statusKey==='em_andamento'?'ativo':'' ?>"
                                    onclick="selecionarStatus(<?= $i ?>, 'em_andamento', this)">
                                    <i class="bi bi-arrow-repeat"></i> Em Andamento
                                </button>
                                <button type="button"
                                    class="btn-status btn-visualizada <?= $statusKey==='visualizada'?'ativo':'' ?>"
                                    onclick="selecionarStatus(<?= $i ?>, 'visualizada', this)">
                                    <i class="bi bi-eye-fill"></i> Visualizada
                                </button>
                                <button type="button"
                                    class="btn-status btn-finalizada <?= $statusKey==='finalizada'?'ativo':'' ?>"
                                    onclick="selecionarStatus(<?= $i ?>, 'finalizada', this)">
                                    <i class="bi bi-check-circle-fill"></i> Finalizada
                                </button>
                            </div>

                            <!-- Campo resposta -->
                            <textarea
                                class="textarea-resposta"
                                id="resposta-<?= $i ?>"
                                placeholder="Digite sua resposta para esta manifestação..."
                            ><?= htmlspecialchars($m['feedback'] ?? '') ?></textarea>

                            <!-- Botão enviar + feedback -->
                            <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">
                                <button
                                    type="button"
                                    class="btn-enviar-resp"
                                    id="btn-resp-<?= $i ?>"
                                    onclick="enviarResposta(<?= $i ?>, <?= $m['idmanifest'] ?>)">
                                    <i class="bi bi-send-fill"></i> Salvar Resposta
                                </button>
                                <span class="salvo-msg" id="salvo-<?= $i ?>">
                                    <i class="bi bi-check-circle-fill"></i> Salvo com sucesso!
                                </span>
                            </div>

                            <!-- Input hidden para status selecionado -->
                            <input type="hidden" id="status-<?= $i ?>" value="<?= htmlspecialchars($statusKey) ?>">
                        </div>
                    </div>

                </div><!-- fim card-body -->
            </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</main>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>Contato</h3>
            <p>📞 (88) 1234-5678</p>
            <p>📧 contato@escola.com.br</p>
        </div>
        <div class="footer-section">
            <h3>Localização</h3>
            <p>Rua da Educação, 123 - Centro</p>
            <p>Sobral, CE</p>
        </div>
        <div class="footer-section">
            <h3>Horário de Atendimento</h3>
            <p>Segunda a Sexta: 07h às 18h</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 Ouvidoria Escolar - Todos os direitos reservados.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// ══ ACCORDION ══
function toggleCard(i) {
    document.getElementById('card-' + i).classList.toggle('aberto');
}

// ══ SELECIONAR STATUS ══
function selecionarStatus(cardIdx, status, btnClicado) {
    // Remove .ativo de todos os botões do card
    const card = document.getElementById('card-' + cardIdx);
    card.querySelectorAll('.btn-status').forEach(b => b.classList.remove('ativo'));
    // Marca o clicado
    btnClicado.classList.add('ativo');
    // Atualiza o hidden
    document.getElementById('status-' + cardIdx).value = status;
}

// ══ ENVIAR RESPOSTA VIA AJAX ══
function enviarResposta(cardIdx, idmanifest) {
    const feedback = document.getElementById('resposta-' + cardIdx).value.trim();
    const status   = document.getElementById('status-' + cardIdx).value;
    const btn      = document.getElementById('btn-resp-' + cardIdx);
    const salvoMsg = document.getElementById('salvo-' + cardIdx);

    if (!feedback && status === 'pendente') {
        alert('Digite uma resposta ou selecione um status antes de salvar.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';
    salvoMsg.classList.remove('visivel');

    $.ajax({
        url: 'salvar_resposta.php',
        method: 'POST',
        data: {
            idmanifest: idmanifest,
            feedback:   feedback,
            status:     status
        },
        dataType: 'json',
        success: function(res) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill"></i> Salvar Resposta';

            if (res.status === 'sucesso') {
                salvoMsg.classList.add('visivel');
                // Atualiza badge de status no cabeçalho do card visualmente
                atualizarBadgeStatus(cardIdx, status);
                setTimeout(() => salvoMsg.classList.remove('visivel'), 3000);
            } else {
                alert('Erro ao salvar: ' + (res.mensagem || 'Tente novamente.'));
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill"></i> Salvar Resposta';
            alert('Falha na conexão. Tente novamente.');
        }
    });
}

// Atualiza o badge de status no cabeçalho sem recarregar
function atualizarBadgeStatus(cardIdx, novoStatus) {
    const labels = { pendente:'Pendente', em_andamento:'Em Andamento', visualizada:'Visualizada', finalizada:'Finalizada' };
    const icons  = { pendente:'bi-clock-fill', em_andamento:'bi-arrow-repeat', visualizada:'bi-eye-fill', finalizada:'bi-check-circle-fill' };
    const card   = document.getElementById('card-' + cardIdx);
    const badge  = card.querySelector('.badge-status');
    if (!badge) return;
    badge.className = 'badge-status st-' + novoStatus;
    badge.innerHTML = '<i class="bi ' + icons[novoStatus] + '"></i> ' + labels[novoStatus];
}

// Fecha overlay clicando fora
document.getElementById('overlayLogout').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('ativo');
});
</script>
</body>
</html>
