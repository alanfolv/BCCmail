
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
