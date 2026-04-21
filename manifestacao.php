<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    
    session_destroy();
    header("Location: login.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ouvidoria - Manifestação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style_manifest.css">
</head>
<body>

    <main class="container my-5">
        <header class="header">
            <img src="logo_escola.png" alt="Logo Escola" class="logo-escola">
            <h1>Ouvidoria</h1>
            <p class="boas-vindas">Sua voz é fundamental para melhorarmos nossa instituição. Registre sua manifestação abaixo.</p>
        </header>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="opcao-card p-4">
                    <form id="formManifestacao">
                        <div class="mb-4">
                            <label class="form-label d-block fw-bold mb-3">Tipo de Manifestação:</label>
                            <div class="btn-group w-100" role="group" aria-label="Tipos de manifestação">
                                <input type="radio" class="btn-check" name="tipo" id="elogio" value="elogio" autocomplete="off" checked>
                                <label class="btn btn-outline-success" for="elogio"><i class="fas fa-smile"></i> Elogio</label>

                                <input type="radio" class="btn-check" name="tipo" id="sugestao" value="sugestao" autocomplete="off">
                                <label class="btn btn-outline-success" for="sugestao"><i class="fas fa-lightbulb"></i> Sugestão</label>

                                <input type="radio" class="btn-check" name="tipo" id="reclamacao" value="reclamacao" autocomplete="off">
                                <label class="btn btn-outline-success" for="reclamacao"><i class="fas fa-exclamation-circle"></i> Reclamação</label>

                                <input type="radio" class="btn-check" name="tipo" id="denuncia" value="denuncia" autocomplete="off">
                                <label class="btn btn-outline-success" for="denuncia"><i class="fas fa-gavel"></i> Denúncia</label>
                            </div>
                        </div>

                        

                        <div class="mb-3">
                            <label for="assunto" class="form-label">Assunto</label>
                            <input type="text" class="form-control" id="assunto" placeholder="Resuma o motivo do contato" required>
                        </div>

                        <div class="mb-4">
                            <label for="mensagem" class="form-label">Sua Mensagem</label>
                            <textarea class="form-control" id="mensagem" rows="5" placeholder="Descreva detalhadamente sua manifestação..." required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-verde py-3">Enviar Manifestação</button>
                        </div>
                    </form>
                </div>
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
</body>
</html>