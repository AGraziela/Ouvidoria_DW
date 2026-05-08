<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: forms.php");
    exit();
}

$id   = $_SESSION['usuario_id'];

// Dados do usuário
$stmt = $pdo->prepare("SELECT nome, curso, serie, matricula FROM tbusuarios WHERE id_usu = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Manifestações do usuário ordenadas pela mais recente
$stmtM = $pdo->prepare(
    "SELECT idmanifest, idtipo, assunto, manifest, feedback, contato
     FROM tbmanifestacoes
     WHERE idusuario = ?
     ORDER BY idmanifest DESC"
);
$stmtM->execute([$id]);
$manifestacoes = $stmtM->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ouvidoria - Minhas Manifestações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style_manifest.css">
    <style>
        /* ══ BARRA USUÁRIO ══ */
        .barra-usuario {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            padding: 10px 16px;
            background: #f4f7f4;
            border-radius: 14px;
            border: 1px solid #dceadc;
            flex-wrap: wrap;
        }
        .nome-usuario {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e631d;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .barra-acoes { display: flex; gap: 10px; flex-wrap: wrap; }

        .btn-acao-topo {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            border: 2px solid transparent;
        }
        .btn-nova   { background: #1e631d; color: #fff; }
        .btn-nova:hover { background: #0daa0a; transform: translateY(-2px); color: #fff; }
        .btn-logout { background: transparent; color: #cc0000; border-color: #cc0000; }
        .btn-logout:hover { background: #cc0000; color: #fff; transform: translateY(-2px); }

        /* ══ CONTADORES ══ */
        .resumo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin-bottom: 26px;
        }
        .resumo-card {
            background: #f4f7f4;
            border: 1.5px solid #dceadc;
            border-radius: 14px;
            padding: 16px 10px;
            text-align: center;
        }
        .resumo-card .num {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e631d;
            display: block;
        }
        .resumo-card .leg {
            font-size: 0.72rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .resumo-card.respondidas .num { color: #0daa0a; }
        .resumo-card.pendentes   .num { color: #ff8c00; }

        /* ══ LISTA ══ */
        .lista-vazia {
            text-align: center;
            padding: 40px 20px;
            color: #888;
        }
        .lista-vazia i { font-size: 3rem; color: #ccc; display: block; margin-bottom: 14px; }
        .lista-vazia p { font-size: 0.9rem; }

        /* ══ CARD DE MANIFESTAÇÃO ══ */
        .manifest-card {
            background: #fff;
            border: 1.5px solid #dceadc;
            border-radius: 16px;
            margin-bottom: 14px;
            overflow: hidden;
            transition: box-shadow 0.25s, transform 0.25s;
        }
        .manifest-card:hover {
            box-shadow: 0 6px 22px rgba(30,99,29,0.12);
            transform: translateY(-2px);
        }

        /* Cabeçalho do card */
        .manifest-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            cursor: pointer;
            user-select: none;
            gap: 10px;
            flex-wrap: wrap;
            background: #f8fdf8;
            border-bottom: 1px solid transparent;
            transition: border-color 0.25s;
        }
        .manifest-card.aberto .manifest-card-header {
            border-color: #c8e6c8;
        }

        .card-esquerda { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

        /* Badge tipo */
        .badge-tipo {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 13px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: capitalize;
        }
        .badge-elogio    { background: #d4edda; color: #155724; }
        .badge-sugestao  { background: #d1ecf1; color: #0c5460; }
        .badge-reclamacao{ background: #fff3cd; color: #856404; }
        .badge-denuncia  { background: #f8d7da; color: #721c24; }

        /* Badge status */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 13px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .status-respondida { background: #d4edda; color: #155724; }
        .status-pendente   { background: #fff3cd; color: #856404; }

        .card-assunto {
            font-size: 0.92rem;
            font-weight: 600;
            color: #1a1a1a;
        }
        .card-protocolo {
            font-size: 0.75rem;
            color: #888;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .chevron {
            color: #1e631d;
            font-size: 1.1rem;
            transition: transform 0.3s;
            flex-shrink: 0;
        }
        .manifest-card.aberto .chevron { transform: rotate(180deg); }

        /* Corpo do card (accordion) */
        .manifest-card-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.3s;
            padding: 0 18px;
        }
        .manifest-card.aberto .manifest-card-body {
            max-height: 600px;
            padding: 18px 18px 20px;
        }

        /* Campos internos */
        .campo-detalhe { margin-bottom: 14px; }
        .campo-detalhe label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #1e631d;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 5px;
        }
        .campo-detalhe .valor {
            background: #f4f7f4;
            border: 1.5px solid #dceadc;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.9rem;
            color: #333;
            line-height: 1.65;
            white-space: pre-wrap;
        }

        /* Resposta */
        .resposta-box {
            background: linear-gradient(135deg, #f0faf0, #e6f5e6);
            border: 2px solid #1e631d;
            border-radius: 14px;
            padding: 16px 18px;
        }
        .resposta-box .resp-header {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #1e631d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .resposta-box .resp-texto {
            font-size: 0.9rem;
            color: #2d5a2d;
            line-height: 1.65;
        }

        /* Pendente */
        .pendente-box {
            background: #fff8e1;
            border: 1.5px dashed #ff8c00;
            border-radius: 12px;
            padding: 13px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .pendente-box i { color: #ff8c00; font-size: 1.3rem; flex-shrink: 0; }
        .pendente-box p { font-size: 0.85rem; color: #795900; margin: 0; line-height: 1.5; }

        /* ══ OVERLAYS LOGOUT ══ */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }
        .overlay.ativo { display: flex; }
        .popup-box {
            background: #fff;
            border-radius: 22px;
            padding: 44px 36px 38px;
            max-width: 380px;
            width: 92%;
            text-align: center;
            box-shadow: 0 20px 55px rgba(0,0,0,0.18);
            animation: popIn 0.38s cubic-bezier(0.34,1.56,0.64,1) forwards;
        }
        @keyframes popIn {
            from { opacity:0; transform:scale(0.72) translateY(28px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
        .popup-icone {
            width: 78px; height: 78px;
            background: linear-gradient(135deg, #ff8c00, #ffb347);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 6px 22px rgba(255,140,0,0.3);
        }
        .popup-icone i { font-size: 2.2rem; color: #fff; }
        .popup-box h2  { font-size: 1.3rem; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; }
        .popup-box p   { font-size: 0.87rem; color: #666; margin-bottom: 24px; }
        .popup-botoes  { display: flex; gap: 10px; justify-content: center; }
        .btn-cancelar {
            padding: 11px 22px; border: 2px solid #ccc; border-radius: 12px;
            background: transparent; color: #555; font-weight: 600;
            font-family: 'Poppins',sans-serif; cursor: pointer; transition: 0.3s;
        }
        .btn-cancelar:hover { border-color: #888; color: #222; }
        .btn-confirmar {
            padding: 11px 22px; border: none; border-radius: 12px;
            background: #cc0000; color: #fff; font-weight: 600;
            font-family: 'Poppins',sans-serif; cursor: pointer; transition: 0.3s;
        }
        .btn-confirmar:hover { background: #aa0000; }
    </style>
</head>
<body>

<!-- ══ POPUP CONFIRMAR LOGOUT ══ -->
<div class="overlay" id="overlayLogout">
    <div class="popup-box">
        <div class="popup-icone"><i class="bi bi-box-arrow-right"></i></div>
        <h2>Deseja sair?</h2>
        <p>Você será redirecionado para a tela inicial.</p>
        <div class="popup-botoes">
            <button class="btn-cancelar" onclick="document.getElementById('overlayLogout').classList.remove('ativo')">Cancelar</button>
            <button class="btn-confirmar" onclick="window.location.href='logout.php'">Sim, sair</button>
        </div>
    </div>
</div>

<main class="container">
    <div class="card_formulario">
        <img src="logo_escola.png" alt="Logo Escola" class="logo-escola">
        <h1 class="titulo_form">Minhas Manifestações</h1>
        <p class="subtitulo">Acompanhe o status de todas as suas manifestações e veja as respostas da ouvidoria.</p>

        <!-- Barra do usuário -->
        <div class="barra-usuario">
            <span class="nome-usuario">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($user['nome']) ?>
            </span>
            <div class="barra-acoes">
                <a href="manifestacao.php" class="btn-acao-topo btn-nova">
                    <i class="bi bi-plus-circle-fill"></i> Nova Manifestação
                </a>
                <button class="btn-acao-topo btn-logout" onclick="document.getElementById('overlayLogout').classList.add('ativo')">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </button>
            </div>
        </div>

        <?php
        $total      = count($manifestacoes);
        $respondidas = array_filter($manifestacoes, fn($m) => !empty($m['feedback']));
        $pendentes   = $total - count($respondidas);
        ?>

        <!-- Contadores -->
        <div class="resumo-grid">
            <div class="resumo-card">
                <span class="num"><?= $total ?></span>
                <span class="leg">Total</span>
            </div>
            <div class="resumo-card respondidas">
                <span class="num"><?= count($respondidas) ?></span>
                <span class="leg">Respondidas</span>
            </div>
            <div class="resumo-card pendentes">
                <span class="num"><?= $pendentes ?></span>
                <span class="leg">Pendentes</span>
            </div>
        </div>

        <!-- Lista de manifestações -->
        <?php if (empty($manifestacoes)): ?>
            <div class="lista-vazia">
                <i class="bi bi-inbox"></i>
                <p>Você ainda não fez nenhuma manifestação.<br>
                   <a href="manifestacao.php" style="color:#1e631d; font-weight:600;">Registre sua primeira manifestação</a>
                </p>
            </div>
        <?php else: ?>
            <div id="listaManifestacoes">
                <?php
                $tipoIcons  = ['elogio'=>'fa-smile','sugestao'=>'fa-lightbulb','reclamacao'=>'fa-exclamation-circle','denuncia'=>'fa-gavel'];
                $tipoLabels = ['elogio'=>'Elogio','sugestao'=>'Sugestão','reclamacao'=>'Reclamação','denuncia'=>'Denúncia'];

                foreach ($manifestacoes as $i => $m):
                    $tipoKey  = strtolower($m['idtipo']);
                    $icon     = $tipoIcons[$tipoKey]  ?? 'fa-comment';
                    $label    = $tipoLabels[$tipoKey] ?? $m['idtipo'];
                    $respondida = !empty($m['feedback']);
                ?>
                <div class="manifest-card" id="card-<?= $i ?>">
                    <div class="manifest-card-header" onclick="toggleCard(<?= $i ?>)">
                        <div class="card-esquerda">
                            <span class="badge-tipo badge-<?= $tipoKey ?>">
                                <i class="fas <?= $icon ?>"></i> <?= $label ?>
                            </span>
                            <span class="badge-status <?= $respondida ? 'status-respondida' : 'status-pendente' ?>">
                                <i class="bi <?= $respondida ? 'bi-check-circle-fill' : 'bi-clock-fill' ?>"></i>
                                <?= $respondida ? 'Respondida' : 'Pendente' ?>
                            </span>
                            <div>
                                <div class="card-assunto"><?= htmlspecialchars($m['assunto']) ?></div>
                                <div class="card-protocolo"><?= htmlspecialchars($m['contato']) ?></div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-down chevron"></i>
                    </div>

                    <div class="manifest-card-body">
                        <div class="campo-detalhe">
                            <label><i class="bi bi-textarea-resize"></i> Mensagem enviada</label>
                            <div class="valor"><?= htmlspecialchars($m['manifest']) ?></div>
                        </div>

                        <div class="campo-detalhe">
                            <label><i class="bi bi-chat-left-dots-fill"></i> Resposta da Ouvidoria</label>
                            <?php if ($respondida): ?>
                                <div class="resposta-box">
                                    <div class="resp-header">
                                        <i class="bi bi-chat-left-dots-fill"></i> Resposta da Ouvidoria
                                    </div>
                                    <div class="resp-texto"><?= nl2br(htmlspecialchars($m['feedback'])) ?></div>
                                </div>
                            <?php else: ?>
                                <div class="pendente-box">
                                    <i class="bi bi-hourglass-split"></i>
                                    <p>Sua manifestação está sendo analisada. Em breve a ouvidoria enviará uma resposta.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
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

<script>
function toggleCard(i) {
    const card = document.getElementById('card-' + i);
    card.classList.toggle('aberto');
}
// Fecha overlay clicando fora
document.getElementById('overlayLogout').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('ativo');
});
</script>
</body>
</html>
