
document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // MENU LATERAL
    // ==========================================

    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');

    if (menuToggle && sidebar) {

        menuToggle.addEventListener('click', () => {

            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('open');
            } else {
                sidebar.classList.toggle('collapsed');
            }

        });

    }


    // ==========================================
    // MODAL DE COMPOSIÇÃO
    // ==========================================

    const composeBtn = document.querySelector('.compose-btn');
    const composeModal = document.getElementById('compose-modal');
    const composeClose = document.getElementById('compose-close');

    if (composeBtn && composeModal) {

        composeBtn.addEventListener('click', () => {
            composeModal.style.display = 'flex';
        });

    }

    if (composeClose && composeModal) {

        composeClose.addEventListener('click', () => {
            composeModal.style.display = 'none';
        });

    }


    // ==========================================
    // ALTERNAR TEMA CLARO / ESCURO
    // ==========================================

    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {

        htmlElement.setAttribute('data-theme', 'dark');

        if (themeToggle) {
            themeToggle.checked = true;
        }

    } else {

        htmlElement.removeAttribute('data-theme');

        if (themeToggle) {
            themeToggle.checked = false;
        }

    }

    if (themeToggle) {

        themeToggle.addEventListener('change', () => {

            if (themeToggle.checked) {

                htmlElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');

            } else {

                htmlElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');

            }

        });

    }


    // ==========================================
    // LOGIN - ETAPAS EMAIL E SENHA
    // ==========================================

    const emailStep = document.getElementById('email-step');
    const passwordStep = document.getElementById('password-step');

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('senha');

    const nextButton = document.getElementById('next-button');
    const backButton = document.getElementById('back-button');

    const loginTitle = document.getElementById('login-title');
    const loginSubtitle = document.getElementById('login-subtitle');

    // Avançar para senha
    if (
        emailStep &&
        passwordStep &&
        emailInput &&
        passwordInput &&
        nextButton
    ) {

        nextButton.addEventListener('click', () => {

            if (!emailInput.checkValidity()) {
                emailInput.reportValidity();
                return;
            }

            emailStep.hidden = true;
            passwordStep.hidden = false;

            passwordInput.disabled = false;

            if (loginTitle) {
                loginTitle.textContent = 'Bem-vindo';
            }

            if (loginSubtitle) {
                loginSubtitle.textContent = 'Digite sua senha para continuar';
            }

            passwordInput.focus();

        });


        // Voltar para email
        if (backButton) {

            backButton.addEventListener('click', () => {

                passwordStep.hidden = true;
                emailStep.hidden = false;

                passwordInput.disabled = true;
                passwordInput.value = '';

                // Resetar mostrar senha
                if (showPassword) {
                    showPassword.checked = false;
                }

                passwordInput.type = 'password';

                if (loginTitle) {
                    loginTitle.textContent = 'Fazer login';
                }

                if (loginSubtitle) {
                    loginSubtitle.textContent = 'para continuar no BCCmail';
                }

                emailInput.focus();

            });

        }

    }


    // ==========================================
    // MOSTRAR / OCULTAR SENHA
    // ==========================================

    const showPassword = document.getElementById('show-password');

    if (showPassword && passwordInput) {

        showPassword.addEventListener('change', () => {

            passwordInput.type = showPassword.checked
                ? 'text'
                : 'password';

        });

    }


    // ==========================================
    // ABRIR EMAIL
    // ==========================================

    window.openEmail = function(sender, subject, snippet, time) {

        const listView = document.getElementById('email-list-view');
        const detailView = document.getElementById('email-detail-view');

        if (!listView || !detailView) {
            return;
        }

        document.getElementById('detail-sender').textContent = sender;
        document.getElementById('detail-subject').textContent = subject;
        document.getElementById('detail-date').textContent = time;

        document.getElementById('detail-text').textContent =
            snippet + " — Conteúdo completo da mensagem.";

        listView.style.display = 'none';
        detailView.style.display = 'block';

    };


    // ==========================================
    // VOLTAR PARA LISTA DE EMAILS
    // ==========================================

    const backBtn = document.getElementById('back-to-list');

    const listView = document.getElementById('email-list-view');
    const detailView = document.getElementById('email-detail-view');

    if (backBtn && listView && detailView) {

        backBtn.addEventListener('click', () => {

            detailView.style.display = 'none';
            listView.style.display = 'block';

        });

    }

});

// Tela de cadastro
const etapasCadastro = document.querySelectorAll(".cadastro-etapa");
const btnAvancarCadastro = document.getElementById("btn-avancar");

if (btnAvancarCadastro) {
    const tituloCadastro = document.getElementById("titulo-cadastro");
    const descricaoCadastro = document.getElementById("descricao-cadastro");

    let etapaAtual = 1;

    const titulos = [
        "Criar conta",
        "Informações básicas",
        "Criar endereço de e-mail",
        "Criar senha"
    ];

    const descricoes = [
        "Digite seu nome e sobrenome",
        "Informe sua data de nascimento e gênero",
        "Escolha um endereço de acesso para o BCC-Mail",
        "Crie uma senha para sua conta com pelo menos 6 caracteres e 1 número"
    ];

    function mostrarEtapa(numero) {
        etapasCadastro.forEach((etapa, index) => {
            etapa.hidden = index + 1 !== numero;
        });

        tituloCadastro.textContent = titulos[numero - 1];
        descricaoCadastro.textContent = descricoes[numero - 1];

        btnAvancarCadastro.textContent = numero === 4 ? "Criar conta" : "Avançar";
        etapaAtual = numero;
    }

    btnAvancarCadastro.addEventListener("click", function () {
        const etapa = document.getElementById("etapa-" + etapaAtual);
        const campos = etapa.querySelectorAll("input, select");

        for (const campo of campos) {
            if (!campo.checkValidity()) {
                campo.reportValidity();
                return;
            }
        }

        if (etapaAtual === 3) {
            const email = document.getElementById("email-cadastro").value;

            if (!/^[a-zA-Z0-9.]+$/.test(email)) {
                campoAlerts.textContent="O endereço deve conter apenas letras, números e pontos.";
                return;
            }
        }

        // mínimo 6 caractere, sendo: 1 numero e 1 letra
        if (etapaAtual === 4) {
            const senha = document.getElementById("senha-cadastro").value;
            const campoAlerts = document.getElementById("alerts");
            const confirmar = document.getElementById("confirmar-senha").value;

            const erro = [
                {
                    invalida: senha.length < 6,
                    mensagem: "A senha deve ter pelo menos 6 caracteres."
                },
                {
                    invalida: !/[0-9]/.test(senha),
                    mensagem: "A senha deve conter pelo menos 1 número."
                },
                {
                    invalida: !/[a-zA-Z]/.test(senha),
                    mensagem: "A senha deve conter pelo menos 1 letra."
                },
                {
                    invalida: senha !== confirmar,
                    mensagem: "As senhas não são iguais."
                }
            ].find(regra => regra.invalida);

            if (erro) {
                campoAlerts.textContent = erro.mensagem;
                return;
            }

            document.getElementById("form-cadastro").requestSubmit();
            return;
        }

        mostrarEtapa(etapaAtual + 1);
    });

    const mostrarSenha = document.getElementById("mostrar-senha");

    if (mostrarSenha) {
        mostrarSenha.addEventListener("change", function () {
            const senha = document.getElementById("senha-cadastro");
            const confirmar = document.getElementById("confirmar-senha");

            senha.type = this.checked ? "text" : "password";
            confirmar.type = this.checked ? "text" : "password";
        });
    }
}

document.addEventListener("keydown", function (event) {
    if (event.key === "Enter" && btnAvancarCadastro) {
        event.preventDefault();
        btnAvancarCadastro.click();
    }
});