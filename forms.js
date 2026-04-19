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
     
  


function mostrarTela(tela){
    if(tela === "cadastro"){
        $("#login_form").hide();
        $("#secao-cadastro").show();
    } else {
        $("#secao-cadastro").hide();
        $("#login_form").show();
    }
}

if (window.location.hash === "#cadastro") {
    mostrarTela("cadastro");
} else {
    mostrarTela("login");
}





    $.validator.addMethod("letras", function(value, element) {
        return this.optional(element) || /^[A-Za-zÀ-ÿ\s]+$/.test(value);
    }, "Digite apenas letras");
    $.validator.addMethod("numeros", function(value, element) {
    return this.optional(element) || /^[0-9]+$/.test(value);
}, "Digite apenas números");



    $("#User_form").validate({

        
        rules: {
         
            nome: {
                required: true,
                minlength: 3,
                letras: true
                
            },
            matricula: {
                required: true,
                minlength: 6,
                numeros: true
            }
        },
        messages: {
            nome: {
                required: "Digite seu nome",
                minlength: "Mínimo 3 caracteres",
                letras: "Digite apenas letras"
            },
            matricula: {
                required: "Digite sua matrícula",
                minlength: "Mínimo 6 caracteres",
                numeros:"Digite apenas números"
            }
        }
    });


 $("#Adm_form").validate({

        
        rules: {
         
            email: {
                required: true,
                minlength: 3,
                email: true
               
                
            },
            senha: {
                required: true,
                minlength: 6
                
            }
        },
        messages: {
            email: {
                required: "Digite seu email",
                minlength: "Mínimo 3 caracteres",
                email:"Digite um Email válido"
                
            },
            senha: {
                required: "Digite sua senha",
                minlength: "Mínimo 6 caracteres"
              
            }
        }
    });
    $("#Cad_Form").validate({

        
        rules: {
         
            nome: {
                required: true,
                minlength: 3,
                letras: true
                
            },
            matricula: {
                required: true,
                minlength: 6,
                numeros: true
            },
            curso:{
                required: true
            },
            serie:{
                required: true
            }
        },
        messages: {
            nome: {
                required: "Digite seu nome",
                minlength: "Mínimo 3 caracteres",
                letras: "Digite apenas letras"
            },
            matricula: {
                required: "Digite sua matrícula",
                minlength: "Mínimo 6 caracteres",
                numeros:"Digite apenas números"
            },
             curso:{
                required: "Selecione seu curso"
            },
            serie:{
                required:"Selecione sua série"
            }
        },
  

    submitHandler: function(form) {

        $.ajax({
            url: 'action.php',
            method: 'POST',
            data: $(form).serialize() + '&action=Cadastro_btn',

            success: function(response){
                $("#mensagem")
                  .removeClass("d-none")
                  .html("Cadastro realizado! Você já pode fazer login.");
            },

            error: function(){
                $("#mensagem")
                  .removeClass("d-none")
                  .html("Erro na requisição");
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