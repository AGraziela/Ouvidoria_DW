<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: forms.php");
    exit();
}

$id   = $_SESSION['usuario_id'];
$sql  = "SELECT nome, curso, serie, matricula FROM tbusuarios WHERE id_usu = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ouvidoria - Manifestação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style_manifest.css">
    <style>
        /* ══ BARRA DE USUÁRIO ══ */
        .barra-usuario {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
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
        .barra-acoes { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-minhas {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            background: #1e631d;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-minhas:hover {
            background: #0daa0a;
            transform: translateY(-2px);
            color: #fff;
        }
        .btn-logout-topo {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            background: transparent;
            color: #cc0000;
            border: 2px solid #cc0000;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-logout-topo:hover {
            background: #cc0000;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(204,0,0,0.22);
        }

        /* ══ OVERLAY GERAL ══ */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 9000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(3px);
        }
        .overlay.ativo { display: flex; }

        /* ══ POPUP BASE ══ */
        .popup-box {
            background: #fff;
            border-radius: 24px;
            padding: 50px 40px 40px;
            max-width: 420px;
            width: 92%;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0,0,0,0.18);
            position: relative;
            animation: popIn 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards;
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(0.7) translateY(30px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* ══ POPUP SUCESSO ══ */
        .popup-sucesso .icone-wrap {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #1e631d, #0daa0a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            box-shadow: 0 8px 25px rgba(30,99,29,0.35);
            animation: pulsar 2s ease-in-out infinite;
        }
        @keyframes pulsar {
            0%,100% { box-shadow: 0 8px 25px rgba(30,99,29,0.35); }
            50%      { box-shadow: 0 8px 35px rgba(30,99,29,0.6); }
        }
        .popup-sucesso .icone-wrap i {
            font-size: 2.8rem;
            color: #fff;
        }
        .popup-sucesso h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        .popup-sucesso .subtexto {
            font-size: 0.88rem;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .protocolo-destaque {
            background: linear-gradient(135deg, #f0faf0, #e6f5e6);
            border: 2px dashed #1e631d;
            border-radius: 14px;
            padding: 16px 22px;
            margin: 0 auto 28px;
            display: inline-block;
            min-width: 200px;
        }
        .protocolo-destaque span {
            display: block;
            font-size: 0.75rem;
            color: #555;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .protocolo-destaque strong {
            font-size: 1.3rem;
            color: #1e631d;
            letter-spacing: 3px;
            font-weight: 700;
        }
        .popup-botoes {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-nova-manifest {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 13px 22px;
            background: #1e631d;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-nova-manifest:hover {
            background: #0daa0a;
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(30,99,29,0.3);
        }
        .btn-logout-popup {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 13px 22px;
            background: transparent;
            color: #cc0000;
            border: 2px solid #cc0000;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-logout-popup:hover {
            background: #cc0000;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(204,0,0,0.25);
        }

        /* ══ POPUP ERRO ══ */
        .popup-erro .icone-wrap {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #cc0000, #ff4444);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            box-shadow: 0 8px 25px rgba(204,0,0,0.35);
        }
        .popup-erro .icone-wrap i { font-size: 2.8rem; color: #fff; }
        .popup-erro h2 { font-size: 1.4rem; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; }
        .popup-erro .subtexto { font-size: 0.88rem; color: #666; margin-bottom: 25px; }
        .btn-fechar-erro {
            padding: 12px 30px;
            background: #cc0000;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-fechar-erro:hover { background: #aa0000; }

        /* ══ POPUP LOGOUT (confirmação) ══ */
        .popup-logout .icone-wrap {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ff8c00, #ffb347);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 25px rgba(255,140,0,0.35);
        }
        .popup-logout .icone-wrap i { font-size: 2.4rem; color: #fff; }
        .popup-logout h2 { font-size: 1.35rem; font-weight: 700; color: #1a1a1a; margin-bottom: 8px; }
        .popup-logout .subtexto { font-size: 0.88rem; color: #666; margin-bottom: 25px; }
        .btn-cancelar {
            padding: 12px 22px;
            border: 2px solid #ccc;
            border-radius: 14px;
            background: transparent;
            color: #555;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-cancelar:hover { border-color: #888; color: #222; }
        .btn-confirmar-logout {
            padding: 12px 22px;
            border: none;
            border-radius: 14px;
            background: #cc0000;
            color: #fff;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-confirmar-logout:hover { background: #aa0000; }

        /* ══ LOADING NO BOTÃO DE ENVIO ══ */
        .botao:disabled { opacity: 0.7; cursor: not-allowed; }

        /* ══ CONFETES ══ */
        .confete {
            position: fixed;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            top: -10px;
            animation: cair linear forwards;
            z-index: 9999;
        }
        @keyframes cair {
            to { transform: translateY(110vh) rotate(720deg); opacity: 0; }
        }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════
     POPUP: SUCESSO AO ENVIAR
══════════════════════════════════════ -->
<div class="overlay" id="overlaySuccesso">
    <div class="popup-box popup-sucesso">
        <div class="icone-wrap">
            <i class="bi bi-check-lg"></i>
        </div>
        <h2>Manifestação Enviada!</h2>
        <p class="subtexto">Sua manifestação foi registrada com sucesso.<br>Guarde seu número de protocolo:</p>
        <div class="protocolo-destaque">
            <span>Protocolo</span>
            <strong id="numProtocolo">—</strong>
        </div>
        <div class="popup-botoes">
            <button class="btn-nova-manifest" onclick="novaManifestacao()">
                <i class="bi bi-plus-circle-fill"></i> Nova Manifestação
            </button>
            <button class="btn-logout-popup" onclick="abrirPopupLogout()">
                <i class="bi bi-box-arrow-right"></i> Sair
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     POPUP: ERRO AO ENVIAR
══════════════════════════════════════ -->
<div class="overlay" id="overlayErro">
    <div class="popup-box popup-erro">
        <div class="icone-wrap">
            <i class="bi bi-x-lg"></i>
        </div>
        <h2>Ops! Algo deu errado</h2>
        <p class="subtexto" id="msgErro">Tente novamente.</p>
        <button class="btn-fechar-erro" onclick="fecharOverlay('overlayErro')">Tentar novamente</button>
    </div>
</div>

<!-- ══════════════════════════════════════
     POPUP: CONFIRMAR LOGOUT
══════════════════════════════════════ -->
<div class="overlay" id="overlayLogout">
    <div class="popup-box popup-logout">
        <div class="icone-wrap">
            <i class="bi bi-box-arrow-right"></i>
        </div>
        <h2>Deseja sair?</h2>
        <p class="subtexto">Você será redirecionado para a tela inicial.</p>
        <div class="popup-botoes">
            <button class="btn-cancelar" onclick="fecharOverlay('overlayLogout')">Cancelar</button>
            <button class="btn-confirmar-logout" onclick="fazerLogout()">Sim, sair</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     CONTEÚDO PRINCIPAL
══════════════════════════════════════ -->
<main class="container">

    <?php if (isset($_GET['erro'])): ?>
        <div class="alert alert-danger text-center" style="border-radius:12px; margin-bottom:15px;">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_GET['erro']) ?>
        </div>
    <?php endif; ?>

    <div class="card_formulario">
        <img src="logo_escola.png" alt="Logo Escola" class="logo-escola">
        <h1 class="titulo_form">Ouvidoria</h1>
        <p class="subtitulo">Sua voz é fundamental para melhorarmos nossa instituição. Registre sua manifestação abaixo.</p>

        <!-- Barra usuário -->
        <div class="barra-usuario">
            <span class="nome-usuario">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($user['nome']) ?>
            </span>
            <div class="barra-acoes">
                <a href="minhas_manifestacoes.php" class="btn-minhas">
                    <i class="bi bi-list-check"></i> Minhas Manifestações
                </a>
                <button class="btn-logout-topo" onclick="abrirPopupLogout()">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </button>
            </div>
        </div>

        <!-- Dados do usuário (somente leitura) -->
        <div class="dados_usu">
            <label>Nome:</label>
            <input type="text" value="<?= htmlspecialchars($user['nome']) ?>" readonly>

            <label>Matrícula:</label>
            <input type="text" value="<?= htmlspecialchars($user['matricula']) ?>" readonly>

            <label>Ano:</label>
            <input type="text" value="<?= htmlspecialchars($user['serie']) ?>º Ano" readonly>

            <label>Curso:</label>
            <input type="text" value="<?= htmlspecialchars($user['curso']) ?>" readonly>
        </div>

        <!-- Formulário -->
        <form id="formManifestacao">
            <br>
            <div class="tipo_manifest">
                <label>Tipo de Manifestação:</label>
                <div class="radio_container" role="group">
                    <input type="radio" class="tipo_radio" name="tipo" id="elogio" value="elogio" autocomplete="off" checked>
                    <label class="label_tipo" for="elogio"><i class="fas fa-smile"></i> Elogio</label>

                    <input type="radio" class="tipo_radio" name="tipo" id="sugestao" value="sugestao" autocomplete="off">
                    <label class="label_tipo" for="sugestao"><i class="fas fa-lightbulb"></i> Sugestão</label>

                    <input type="radio" class="tipo_radio" name="tipo" id="reclamacao" value="reclamacao" autocomplete="off">
                    <label class="label_tipo" for="reclamacao"><i class="fas fa-exclamation-circle"></i> Reclamação</label>

                    <input type="radio" class="tipo_radio" name="tipo" id="denuncia" value="denuncia" autocomplete="off">
                    <label class="label_tipo" for="denuncia"><i class="fas fa-gavel"></i> Denúncia</label>
                </div>
            </div>

            <label for="assunto" class="form-label"><i class="bi bi-card-text"></i> Assunto:</label>
            <div class="input_grupo">
                <input type="text" id="assunto" name="assunto"
                    placeholder="Resuma o motivo do contato" required>
            </div>

            <label for="mensagem" class="form-label"><i class="bi bi-textarea-resize"></i> Sua Mensagem:</label>
            <div class="input_grupo">
                <textarea class="manifes_texto" id="mensagem" name="mensagem" rows="5"
                    placeholder="Descreva detalhadamente sua manifestação... (mínimo 20 caracteres)" required></textarea>
            </div>

            <button type="submit" class="botao" id="btnEnviar">
                <i class="bi bi-send-fill"></i> Enviar Manifestação
            </button>
        </form>
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
// ══ ENVIO DO FORMULÁRIO VIA AJAX ══
$('#formManifestacao').on('submit', function(e) {
    e.preventDefault();

    const btn = $('#btnEnviar');
    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Enviando...');

    $.ajax({
        url: 'processa_manifestacao.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
            btn.prop('disabled', false).html('<i class="bi bi-send-fill"></i> Enviar Manifestação');

            if (res.status === 'sucesso') {
                // Exibe protocolo no popup
                $('#numProtocolo').text(res.protocolo);
                abrirOverlay('overlaySuccesso');
                lancarConfetes();
            } else {
                $('#msgErro').text(res.mensagem || 'Erro ao enviar. Tente novamente.');
                abrirOverlay('overlayErro');
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="bi bi-send-fill"></i> Enviar Manifestação');
            $('#msgErro').text('Falha na conexão. Verifique sua internet e tente novamente.');
            abrirOverlay('overlayErro');
        }
    });
});

// ══ CONTROLE DOS OVERLAYS ══
function abrirOverlay(id) {
    // Fecha todos os outros antes
    document.querySelectorAll('.overlay').forEach(o => o.classList.remove('ativo'));
    document.getElementById(id).classList.add('ativo');
}
function fecharOverlay(id) {
    document.getElementById(id).classList.remove('ativo');
}

// ══ POPUP DE LOGOUT (confirmação) ══
function abrirPopupLogout() {
    abrirOverlay('overlayLogout');
}

// ══ NOVA MANIFESTAÇÃO ══
function novaManifestacao() {
    fecharOverlay('overlaySuccesso');
    // Limpa o formulário
    document.getElementById('formManifestacao').reset();
    // Volta ao topo
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ══ LOGOUT: redireciona com flag ?logout=1 para o index mostrar popup ══
function fazerLogout() {
    window.location.href = 'logout.php';
}

// ══ FECHA OVERLAY CLICANDO FORA DA CAIXA ══
document.querySelectorAll('.overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        // Só fecha se o ID não for overlaySuccesso (sucesso não fecha clicando fora)
        if (e.target === this && this.id !== 'overlaySuccesso') {
            this.classList.remove('ativo');
        }
    });
});

// ══ CONFETES ══
function lancarConfetes() {
    const cores = ['#1e631d','#ff8c00','#0daa0a','#ffcc00','#ff4444','#4fc3f7'];
    for (let i = 0; i < 60; i++) {
        setTimeout(function() {
            const c = document.createElement('div');
            c.className = 'confete';
            c.style.left        = Math.random() * 100 + 'vw';
            c.style.background  = cores[Math.floor(Math.random() * cores.length)];
            c.style.width       = (Math.random() * 8 + 6) + 'px';
            c.style.height      = (Math.random() * 8 + 6) + 'px';
            c.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            c.style.animationDuration = (Math.random() * 2 + 2) + 's';
            c.style.animationDelay    = (Math.random() * 0.5) + 's';
            document.body.appendChild(c);
            setTimeout(() => c.remove(), 4000);
        }, i * 30);
    }
}
</script>
</body>
</html>
