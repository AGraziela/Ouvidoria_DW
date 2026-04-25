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
</head>
<body>

    <main class="container">

        <?php if(isset($_GET['erro'])): ?>
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_GET['erro']) ?>
            </div>
        <?php endif; ?>

        <div class="card_formulario">
            <img src="logo_escola.png" alt="Logo Escola" class="logo-escola">
               <h1 class="titulo_form">Ouvidoria</h1>
               <p class="subtitulo">Sua voz é fundamental para melhorarmos nossa instituição. Registre sua manifestação abaixo.</p>
           <div class="dados_usu">
                <label for="">Nome:</label>
                    <input type="text" name="nome" value="<?php echo $user['nome']; ?>" readonly> 
                <label for="">Matricula:</label> 
                    <input type="email" name="curso" value="<?php echo $user['matricula'];?>" readonly> 
                <label for="">Ano:</label> 
                    <input type="text" value="<?php echo $user['serie']; ?>º Ano" readonly>
                <label for="">Curso:</label> 
                    <input type="email" name="curso" value="<?php echo $user['curso']; ?>" readonly> 
            </div>
            
                   
                  
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
                            
                            <!-- name="assunto" adicionado -->
                            <input type="text" class="" id="assunto" name="assunto" placeholder="Resuma o motivo do contato" required>
                        </div>
                           <label for="mensagem" class="form-label"><i class="bi bi-textarea-resize"></i> Sua Mensagem:</label>
                        <div class="input_grupo">
                            
                            <!-- name="mensagem" adicionado -->
                            <textarea class="manifes_texto" id="mensagem" name="mensagem" rows="5" placeholder="Descreva detalhadamente sua manifestação..." required></textarea>
                        </div>

                        
                            <button type="submit" class="botao ">Enviar Manifestação</button>
                      
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
</body>
</html>
