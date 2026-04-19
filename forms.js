
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
            nome: { required: true, minlength: 3, letras: true },
            senha: { required: true, minlength: 6,}
        },
        messages: {
            nome: { required: "Digite seu nome", minlength: "Mínimo 3 caracteres" },
            senha: { required: "Digite sua senha", minlength: "Mínimo 6 caracteres" }
        },
        submitHandler: function(form) {
            $.ajax({
                url: 'action.php',
                method: 'POST',
                data: $(form).serialize() + '&action=Login_User',
                success: function(response) {
                    if (response.trim() === "sucesso") {
                        window.location = "manisfestacao.php";
                    } else {
                        alert("Dados de aluno incorretos!");
                    }
                }
            });
        }
    });

    
    $("#Adm_form").validate({
        rules: {
            email: { required: true, email: true },
            senha: { required: true, minlength: 6 }
        },
        messages: {
            email: { required: "Digite seu email", email: "E-mail inválido" },
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
            nome: { required: true, minlength: 3, letras: true },
            matricula: { required: true, minlength: 6 },
            senha:{required: true, minlength: 6},
            curso: { required: true },
            serie: { required: true }
            
        },
        messages: {
            nome: { required: "Digite seu nome", minlength: "Mínimo 3 caracteres", letras:"Digite apenas letras" },
            matricula: { required: "Digite sua matricula", minlength: "Mínimo 6 caracteres" },
            senha: { required: "Digite sua senha", minlength: "Mínimo 6 caracteres" },
            curso: { required: "Selecione seu curso" },
            serie: { required: "Selecione sua série" }
        },
        submitHandler: function(form) {
            $.ajax({
                url: 'action.php',
                method: 'POST',
                data: $(form).serialize() + '&action=Cadastro_btn',
                success: function(response) {
                    $("#mensagem").removeClass("d-none").html("Cadastro realizado com sucesso!");
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