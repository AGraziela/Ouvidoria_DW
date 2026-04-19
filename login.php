
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Ouvidoria-Login</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
     <link rel="stylesheet" href="style_login.css">
     
    </head>
<body>
  <main class="container">
    
   <div class="card_formulario" id="login_form">
    <img src="logo_escola.png" alt="Logo Dom Walfrido" class="logo-escola">
    
    <div class="toggle-container">
        <div class="toggle-btn active" onclick="switchTab('user')">Usuário</div>
        <div class="toggle-btn" onclick="switchTab('admin')">Administrador</div>
    </div>
    
          <h2 class="titulo_form" >Login Usuário</h2>
          
        <div id="form-user" class="form-content active-form">
           
            <form action="valida_login.php" method="POST" id="User_form">
                <input type="hidden" name="tipo_usuario" value="comum">
                
                <div class="input_grupo">
                    <i class="bi bi-person-circle"></i>
                    <input type="text"  name="nome" placeholder="Nome" required>
                </div>
                <div class="input_grupo">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="matricula" placeholder="Matrícula" >
                </div>
                
                <button type="submit" class="botao">Entrar</button>
                
                <div class="form_links">
                    <a href="esqueceu_senha.php" class="link_secundario">Esqueceu a senha?</a>
                    <hr class="divisor">
                    <p>Não tem uma conta? <a href="#" class="link_destaque" id="btn-ir-cadastro">Cadastre-se</a></p>
                </div>
            </form>
        </div>

        <div id="form-admin" class="form-content">
            
            <form action="valida_login.php" method="POST" id="Adm_form" >
                <input type="hidden" name="tipo_usuario" value="admin">
                
                <div class="input_grupo">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input_grupo">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="senha" placeholder="Senha" required>
                </div>
                
                <button type="submit" class="botao">Acessar Painel</button>
            </form>
        </div>
         <div class="form_links">
            <a href="index.html" class="link_voltar">
                <i class="bi bi-box-arrow-left"></i> Tela Inicial
            </a>
        </div>
    </div>
     
            <div class="card_formulario" id="secao-cadastro">
        <img src="logo_escola.png" alt="Logo Dom Walfrido" class="logo-escola">
        <h2 class="titulo_form">Cadastro</h2>
        <p class="subtitulo">Crie sua conta para acessar o sistema</p>
        <div id="mensagem"  class="alert alert-success d-none"role="alert"> 
            <div  id="result">
            
             </div>

          </div>
        <form action="processa_cadastro.php" method="POST" id="Cad_Form">
            <div class="input_grupo">
                <i class="bi bi-person-circle"></i>
                <input type="text" name="nome" placeholder="Digite seu nome" required>
            </div>
            
            <div class="input_grupo">
                <i class="bi bi-lock-fill"></i>
                <input type="password" name="matricula" placeholder="Digite sua matrícula" required>
            </div>

            <div class="input_grupo">
                <i class="bi bi-mortarboard-fill"></i>
                <select name="curso" required style="border:none; background:transparent; width:100%; outline:none; font-family:'Poppins'; color:#666;">
                    <option value="" disabled selected>Selecione seu curso</option>
                    <option value="informatica">Informática</option>
                    <option value="enfermagem">Enfermagem</option>
                    <option value="administracao">Saúde Bucal</option>
                    <option value="administracao">Energias Renováveis</option>
                    </select>
            </div>

            <div class="input_grupo">
                <i class="bi bi-layers-fill"></i>
                <select name="serie" required style="border:none; background:transparent; width:100%; outline:none; font-family:'Poppins'; color:#666;">
                    <option value="" disabled selected>Série / Ano</option>
                    <option value="1">1º Ano</option>
                    <option value="2">2º Ano</option>
                    <option value="3">3º Ano</option>
                </select>
            </div>

            <button type="submit" class="botao" id="Cadastro_btn">Finalizar Cadastro</button>
            
            <div class="form_links">
                <p>Já possui uma conta? <a href="#" class="link_destaque" id="btn-ir-login">Faça Login</a></p>
        </div>
</form>
        <div class="form_links">
            <a href="index.html" class="link_voltar">
                <i class="bi bi-box-arrow-left"></i> Tela inicial
            </a>
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
      <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
      <script src="forms.js"></script>
</body>
</html>