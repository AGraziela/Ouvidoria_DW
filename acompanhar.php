<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ouvidoria - Acompanhar Manifestação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style_manifest.css">
    <style>
        /* ══ BUSCA POR PROTOCOLO ══ */
        .busca-protocolo {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }
        .busca-protocolo .input_grupo {
            flex: 1;
            margin-bottom: 0;
        }
        .busca-protocolo input {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            font-size: 1rem;
        }
        .btn-buscar {
            padding: 14px 26px;
            background: #1e631d;
            color: #fff;
            border: none;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .btn-buscar:hover { background: #0daa0a; transform: translateY(-2px); }
        .btn-buscar:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }

        /* ══ CARD RESULTADO ══ */
        .card-resultado {
            display: none;
            background: #f8fdf8;
            border: 1.5px solid #c8e6c8;
            border-radius: 18px;
            padding: 28px 26px;
            margin-top: 18px;
            animation: fadeUp 0.4s ease forwards;
        }
        .card-resultado.visivel { display: block; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Cabeçalho do resultado */
        .resultado-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #dceadc;
        }
        .resultado-protocolo {
            font-size: 1rem;
            font-weight: 700;
            color: #1e631d;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Badge tipo */
        .badge-tipo {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: capitalize;
            letter-spacing: 0.5px;
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
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .status-pendente  { background: #fff3cd; color: #856404; }
        .status-respondida{ background: #d4edda; color: #155724; }

        /* Campos do resultado */
        .resultado-campo {
            margin-bottom: 14px;
        }
        .resultado-campo label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #1e631d;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 5px;
        }
        .resultado-campo .valor {
            background: #fff;
            border: 1.5px solid #dceadc;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.92rem;
            color: #333;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* Resposta do admin */
        .resposta-box {
            background: linear-gradient(135deg, #f0faf0, #e6f5e6);
            border: 2px solid #1e631d;
            border-radius: 14px;
            padding: 18px 20px;
            margin-top: 6px;
        }
        .resposta-box .resp-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1e631d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .resposta-box .resp-texto {
            font-size: 0.92rem;
            color: #2d5a2d;
            line-height: 1.65;
        }

        /* Pendente */
        .pendente-box {
            background: #fff8e1;
            border: 1.5px dashed #ff8c00;
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 6px;
        }
        .pendente-box i { color: #ff8c00; font-size: 1.4rem; flex-shrink: 0; }
        .pendente-box p { font-size: 0.88rem; color: #795900; margin: 0; line-height: 1.5; }

        /* ══ ERRO DE BUSCA ══ */
        .card-erro {
            display: none;
            background: #fdf0f0;
            border: 1.5px solid #f5c6c6;
            border-radius: 18px;
            padding: 22px 24px;
            margin-top: 16px;
            text-align: center;
            animation: fadeUp 0.4s ease forwards;
        }
        .card-erro.visivel { display: block; }
        .card-erro i { font-size: 2.2rem; color: #cc0000; margin-bottom: 10px; display: block; }
        .card-erro p { font-size: 0.9rem; color: #721c24; margin: 0; }

        /* ══ DIVISOR ══ */
        .divisor-secao {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 30px 0 20px;
        }
        .divisor-secao hr { flex: 1; border-color: #dceadc; border-width: 1.5px; }
        .divisor-secao span {
            font-size: 0.8rem;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        /* ══ LINK VOLTAR ══ */
        .link-voltar-topo {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #1e631d;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            margin-bottom: 18px;
            transition: 0.3s;
        }
        .link-voltar-topo:hover { color: #ff8c00; }

        /* ══ HINT ══ */
        .hint-protocolo {
            font-size: 0.78rem;
            color: #888;
            margin-top: 6px;
            margin-left: 4px;
        }
        .hint-protocolo i { color: #ff8c00; }
    </style>
</head>
<body>
<main class="container">
    <div class="card_formulario">
        <img src="logo_escola.png" alt="Logo Escola" class="logo-escola">

        <a href="index.html" class="link-voltar-topo">
            <i class="bi bi-arrow-left-circle-fill"></i> Voltar à tela inicial
        </a>

        <h1 class="titulo_form">Acompanhar Manifestação</h1>
        <p class="subtitulo">Digite o código de protocolo recebido ao enviar sua manifestação.</p>

        <!-- ══ BUSCA POR PROTOCOLO ══ -->
        <label class="form-label"><i class="bi bi-search"></i> Código de Protocolo:</label>
        <div class="busca-protocolo">
            <div class="input_grupo">
                <i class="bi bi-ticket-detailed-fill" style="color:#1e631d; margin-right:10px;"></i>
                <input type="text" id="inputProtocolo"
                       placeholder="Ex: OUV-2026-000001"
                       maxlength="18">
            </div>
            <button class="btn-buscar" id="btnBuscar" onclick="buscarProtocolo()">
                <i class="bi bi-search"></i> Buscar
            </button>
        </div>
        <p class="hint-protocolo">
            <i class="bi bi-info-circle"></i>
            O protocolo foi exibido na tela após o envio da sua manifestação.
        </p>

        <!-- Resultado da busca -->
        <div class="card-resultado" id="cardResultado">
            <div class="resultado-header">
                <span class="resultado-protocolo">
                    <i class="bi bi-ticket-detailed-fill"></i>
                    <span id="resProtocolo">—</span>
                </span>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <span class="badge-tipo" id="resTipo">—</span>
                    <span class="badge-status" id="resStatus">—</span>
                </div>
            </div>

            <div class="resultado-campo">
                <label><i class="bi bi-card-text"></i> Assunto</label>
                <div class="valor" id="resAssunto">—</div>
            </div>

            <div class="resultado-campo">
                <label><i class="bi bi-textarea-resize"></i> Mensagem</label>
                <div class="valor" id="resMensagem">—</div>
            </div>

            <div class="resultado-campo">
                <label><i class="bi bi-chat-left-dots-fill"></i> Resposta da Ouvidoria</label>
                <div id="resResposta"></div>
            </div>
        </div>

        <!-- Erro de busca -->
        <div class="card-erro" id="cardErro">
            <i class="bi bi-search"></i>
            <p id="msgBuscaErro">Protocolo não encontrado. Verifique o código e tente novamente.</p>
        </div>

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
// Formata o input em maiúsculas enquanto digita
$('#inputProtocolo').on('input', function() {
    this.value = this.value.toUpperCase();
});

// Buscar pelo Enter também
$('#inputProtocolo').on('keydown', function(e) {
    if (e.key === 'Enter') buscarProtocolo();
});

function buscarProtocolo() {
    const protocolo = $('#inputProtocolo').val().trim();

    if (!protocolo) {
        $('#inputProtocolo').focus();
        return;
    }

    const btn = $('#btnBuscar');
    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Buscando...');

    // Oculta resultados anteriores
    $('#cardResultado').removeClass('visivel');
    $('#cardErro').removeClass('visivel');

    $.ajax({
        url: 'busca_protocolo.php',
        method: 'GET',
        data: { protocolo: protocolo },
        dataType: 'json',
        success: function(res) {
            btn.prop('disabled', false).html('<i class="bi bi-search"></i> Buscar');

            if (res.status === 'sucesso') {
                preencherResultado(res.dados);
            } else {
                $('#msgBuscaErro').text(res.mensagem || 'Protocolo não encontrado.');
                $('#cardErro').addClass('visivel');
            }
        },
        error: function() {
            btn.prop('disabled', false).html('<i class="bi bi-search"></i> Buscar');
            $('#msgBuscaErro').text('Falha na conexão. Tente novamente.');
            $('#cardErro').addClass('visivel');
        }
    });
}

function preencherResultado(d) {
    $('#resProtocolo').text(d.contato);

    // Badge tipo
    const tipoIcons = {
        elogio:     '<i class="fas fa-smile"></i>',
        sugestao:   '<i class="fas fa-lightbulb"></i>',
        reclamacao: '<i class="fas fa-exclamation-circle"></i>',
        denuncia:   '<i class="fas fa-gavel"></i>'
    };
    const tipoLabels = { elogio:'Elogio', sugestao:'Sugestão', reclamacao:'Reclamação', denuncia:'Denúncia' };
    const tipoKey = d.idtipo.toLowerCase();
    $('#resTipo')
        .attr('class', 'badge-tipo badge-' + tipoKey)
        .html((tipoIcons[tipoKey] || '') + ' ' + (tipoLabels[tipoKey] || d.idtipo));

    // Badge status
    if (d.feedback) {
        $('#resStatus').attr('class','badge-status status-respondida').html('<i class="bi bi-check-circle-fill"></i> Respondida');
    } else {
        $('#resStatus').attr('class','badge-status status-pendente').html('<i class="bi bi-clock-fill"></i> Pendente');
    }

    $('#resAssunto').text(d.assunto);
    $('#resMensagem').text(d.manifest);

    // Resposta
    if (d.feedback) {
        $('#resResposta').html(
            '<div class="resposta-box">' +
            '<div class="resp-header"><i class="bi bi-chat-left-dots-fill"></i> Resposta da Ouvidoria</div>' +
            '<div class="resp-texto">' + escHtml(d.feedback) + '</div>' +
            '</div>'
        );
    } else {
        $('#resResposta').html(
            '<div class="pendente-box">' +
            '<i class="bi bi-hourglass-split"></i>' +
            '<p>Sua manifestação está sendo analisada. Em breve a ouvidoria enviará uma resposta. Consulte novamente mais tarde.</p>' +
            '</div>'
        );
    }

    $('#cardResultado').addClass('visivel');
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}
</script>
</body>
</html>
