
function switchTab(type) {
    const userBtn = document.querySelectorAll('.toggle-btn')[0];
    const adminBtn = document.querySelectorAll('.toggle-btn')[1];
    const formUser = document.getElementById('form-user');
    const formAdmin = document.getElementById('form-admin');

    if (type === 'admin') {
        userBtn.classList.remove('active');
        adminBtn.classList.add('active');
        trocarTitulo("Login Admin");
        formUser.classList.remove('active-form');
        formAdmin.classList.add('active-form');
    } else {
        adminBtn.classList.remove('active');
        userBtn.classList.add('active');
        formAdmin.classList.remove('active-form');
        formUser.classList.add('active-form');
        trocarTitulo("Login Usuário");
    }
}

function trocarTitulo(novoTexto) {
    const titulo = document.querySelector('.titulo_form');
    titulo.classList.add('fade-out');
    setTimeout(() => {
        titulo.textContent = novoTexto;
        titulo.classList.remove('fade-out');
        titulo.classList.add('fade-in');
    }, 150);
}

$(document).ready(function() {

    
    function mostrarTela(tela) {
        if (tela === "cadastro") {
            $("#login_form").hide();
            $("#secao-cadastro").show();
        } else {
            $("#secao-cadastro").hide();
            $("#login_form").show();
        }
    }

    $("#btn-ir-cadastro").click(function(e) {
        e.preventDefault();
        mostrarTela("cadastro");
        history.pushState({ tela: "cadastro" }, "", "#cadastro");
    });

    $("#btn-ir-login").click(function(e) {
        e.preventDefault();
        mostrarTela("login");
        history.pushState({ tela: "login" }, "", "#login");
    });

    if (window.location.hash === "#cadastro") {
        mostrarTela("cadastro");
    }

   
    $.validator.addMethod("letras", function(value, element) {
        return this.optional(element) || /^[A-Za-zÀ-ÿ\s]+$/.test(value);
    }, "Digite apenas letras");

  
    
    $("#User_form").validate({
        rules: {
            email_user: { required: true, email: true, minlength: 6},
            senha: { required: true, minlength: 8}
        },
        messages: {
           email_user:{ required: "Digite seu email", email:"Digite um email válido", minlength: "Mínimo 6 caracteres"},
            senha: { required: "Digite sua senha", minlength: "Mínimo 8 caracteres" }
        },
        submitHandler: function(form) {
            $.ajax({
                url: 'action.php',
                method: 'POST',
                data: $(form).serialize() + '&action=Login_User',
                success: function(response) {
                    if (response.trim() === "sucesso") {
                        window.location = "manifestacao.php";
                    } else {
                        $("#mensagem_erro").removeClass("d-none").html("Usuário não identificado! Tente novamente");
                    
                    }
                }
            });
        }
    });

    
    $("#Adm_form").validate({
        rules: {
            email_adm: { required: true, email: true },
            senha: { required: true, minlength: 6 }
        },
        messages: {
            email_adm: { required: "Digite seu email", email: "E-mail inválido" },
            senha: { required: "Digite sua senha", minlength: "Mínimo 6 caracteres" }
        },
        submitHandler: function(form) {
            $.ajax({
                url: 'action.php',
                method: 'POST',
                data: $(form).serialize() + '&action=Login_Admin',
                success: function(response) {
                    if (response.trim() === "sucesso") {
                        window.location = "painel_admin.php";
                    } else {
                        alert("Admin não encontrado ou senha incorreta!");
                    }
                }
            });
        }
    });

  
    $("#Cad_Form").validate({
        rules: {
            nome: { required: true, minlength: 8, letras: true },
            email_cad: { required: true, email: true, minlength: 6},
            matricula: { required: true, minlength: 6, number:true},
            senha:{required: true, minlength: 8},
            curso: { required: true },
            serie: { required: true }
            
        },
        messages: {
            nome: { required: "Digite seu nome", minlength: "Mínimo 8 caracteres" },
            email_cad:{ required: "Digite seu email", email:"Digite um email válido", minlength: "Mínimo 6 caracteres"},
            matricula: { required: "Digite sua matricula", minlength: "Mínimo 6 caracteres", number:"Digite apenas números" },
            senha: { required: "Digite sua senha", minlength: "Mínimo 8 caracteres" },
            curso: { required: "Selecione seu curso" },
            serie: { required: "Selecione sua série" }
        },
        submitHandler: function(form) {
            $.ajax({
                url: 'action.php',
                method: 'POST',
                data: $(form).serialize() + '&action=Cadastro_btn',
                success: function(response) {
                    $("#mensagem").removeClass("d-none").html("Cadastro realizado com sucesso! Faça login!");
                    $("#Cad_Form")[0].reset();
                },
                error: function() {
                    alert("Erro na requisição de cadastro.");
                }
            });
        }
    });
});


window.onpopstate = function(event) {
    if (event.state && event.state.tela === "cadastro") {
        $("#login_form").hide();
        $("#secao-cadastro").show();
    } else {
        $("#secao-cadastro").hide();
        $("#login_form").show();
    }
};