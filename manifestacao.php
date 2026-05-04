<?php
session_start();
require 'conexao.php';
 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: forms.php");
    exit();
}
 
$id = $_SESSION['usuario_id'];
 
$sql = "SELECT nome, curso, serie, matricula FROM tbusuarios WHERE id_usu = ?";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style_manifest.css">
    <style>
        /* ── Barra superior de usuário ── */
        .barra-usuario {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            padding: 10px 16px;
            background: #f4f7f4;
            border-radius: 15px;
            border: 1px solid #e0e7e0;
        }
 
        .barra-usuario .nome-usuario {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e631d;
            display: flex;
            align-items: center;
            gap: 6px;
        }
 
        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            background: transparent;
            color: #cc0000;
            border: 2px solid #cc0000;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }
 
        .btn-logout:hover {
            background: #cc0000;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(204, 0, 0, 0.25);
        }
 
        /* ── Modal de confirmação de logout ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
 
        .modal-overlay.ativo {
            display: flex;
        }
 
        .modal-box {
            background: #fff;
            border-radius: 20px;
            padding: 40px 35px;
            max-width: 380px;
            width: 90%;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            animation: scaleIn 0.3s ease-out;
        }
 
        .modal-box i {
            font-size: 3rem;
            color: #cc0000;
            margin-bottom: 15px;
        }
 
        .modal-box h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 8px;
        }
 
        .modal-box p {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 25px;
        }
 
        .modal-botoes {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
 
        .btn-cancelar {
            padding: 10px 24px;
            border: 2px solid #ccc;
            border-radius: 10px;
            background: transparent;
            color: #555;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
        }
 
        .btn-cancelar:hover {
            border-color: #888;
            color: #222;
        }
 
        .btn-confirmar-logout {
            padding: 10px 24px;
            border: none;
            border-radius: 10px;
            background: #cc0000;
            color: #fff;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
        }
 
        .btn-confirmar-logout:hover {
            background: #aa0000;
        }
    </style>
</head>
<body>
 
<!-- ══ MODAL DE CONFIRMAÇÃO DE LOGOUT ══ -->
<div class="modal-overlay" id="modalLogout">
    <div class="modal-box">
        <i class="bi bi-box-arrow-right"></i>
        <h3>Sair da conta?</h3>
        <p>Você será redirecionado para a tela de login.</p>
        <div class="modal-botoes">
            <button class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
            <a href="logout.php" class="btn-confirmar-logout">Sim, sair</a>
        </div>
    </div>
</div>
 
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
 
        <!-- ── Barra com nome do usuário + botão logout ── -->
        <div class="barra-usuario">
            <span class="nome-usuario">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($user['nome']) ?>
            </span>
            <button class="btn-logout" onclick="abrirModal()">
                <i class="bi bi-box-arrow-right"></i> Sair
            </button>
        </div>
 
        <!-- ── Dados do usuário (somente leitura) ── -->
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
 
        <!-- ── Formulário de manifestação ── -->
        <form id="formManifestacao" action="processa_manifestacao.php" method="POST">
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
                <input type="text" id="assunto" name="assunto" placeholder="Resuma o motivo do contato" required>
            </div>
 
            <label for="mensagem" class="form-label"><i class="bi bi-textarea-resize"></i> Sua Mensagem:</label>
            <div class="input_grupo">
                <textarea class="manifes_texto" id="mensagem" name="mensagem" rows="5"
                    placeholder="Descreva detalhadamente sua manifestação... (mínimo 20 caracteres)" required></textarea>
            </div>
 
            <button type="submit" class="botao">
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
 
<script>
    function abrirModal() {
        document.getElementById('modalLogout').classList.add('ativo');
    }
    function fecharModal() {
        document.getElementById('modalLogout').classList.remove('ativo');
    }
    // Fecha modal clicando fora da caixa
    document.getElementById('modalLogout').addEventListener('click', function(e) {
        if (e.target === this) fecharModal();
    });
</script>
</body>
</html>