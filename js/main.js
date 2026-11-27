document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // CONFIGURAÇÕES DA API
    // ==========================================

    const API = {
        inbox: '/api/emails',
        sent: '/api/emails/sent',
        favorites: '/api/emails/favorites',
        addFavorite: '/api/emails/favorites/add',
        removeFavorite: '/api/emails/favorites/remove'
    };


    // ==========================================
    // FUNÇÃO AUXILIAR - REQUISIÇÕES JSON
    // ==========================================

    async function requestJSON(url, options = {}) {

        const response = await fetch(url, {
            credentials: 'same-origin',
            ...options
        });

        let data;

        try {
            data = await response.json();
        } catch {
            throw new Error('O servidor retornou uma resposta inválida.');
        }

        if (!response.ok || data.success === false) {
            throw new Error(data.message || 'Erro na requisição.');
        }

        return data;
    }


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

    // Fechar modal clicando fora
    if (composeModal) {

        composeModal.addEventListener('click', (event) => {

            if (event.target === composeModal) {
                composeModal.style.display = 'none';
            }

        });

    }


    // ==========================================
    // TEMA CLARO / ESCURO
    // ==========================================

    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {

        htmlElement.setAttribute('data-theme', 'dark');

    } else {

        htmlElement.removeAttribute('data-theme');

    }

    if (themeToggle) {

        themeToggle.checked = savedTheme === 'dark';

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

    const showPassword = document.getElementById('show-password');


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
                loginSubtitle.textContent =
                    'Digite sua senha para continuar';
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

                if (showPassword) {
                    showPassword.checked = false;
                }

                passwordInput.type = 'password';

                if (loginTitle) {
                    loginTitle.textContent = 'Fazer login';
                }

                if (loginSubtitle) {
                    loginSubtitle.textContent =
                        'para continuar no BCCmail';
                }

                emailInput.focus();

            });

        }

    }


    // Mostrar / ocultar senha
    if (showPassword && passwordInput) {

        showPassword.addEventListener('change', () => {

            passwordInput.type = showPassword.checked
                ? 'text'
                : 'password';

        });

    }


    // ==========================================
    // IDENTIFICAR A PÁGINA ATUAL
    // ==========================================

    const currentPath = window.location.pathname.toLowerCase();

    let currentPage = 'inbox';

    if (/\/enviados(?:\.html)?\/?$/.test(currentPath)) {

        currentPage = 'sent';

    } else if (
        /\/(importantes|favoritos)(?:\.html)?\/?$/.test(currentPath)
    ) {

        currentPage = 'favorites';

    }


    // ==========================================
    // ELEMENTOS DA LISTA DE EMAILS
    // ==========================================

    const listView = document.getElementById('email-list-view');
    const detailView = document.getElementById('email-detail-view');

    let emailListContainer = document.querySelector(
        '[data-email-list], #emails-container, #email-list'
    );

    // Criar container caso ainda não exista
    if (!emailListContainer && listView) {

        emailListContainer = document.createElement('div');

        emailListContainer.id = 'emails-container';
        emailListContainer.setAttribute('data-email-list', '');

        if (detailView && detailView.parentElement === listView) {

            listView.insertBefore(emailListContainer, detailView);

        } else {

            listView.appendChild(emailListContainer);

        }

    }

    let displayedEmails = [];

    let favoriteIds = new Set();


    // ==========================================
    // FORMATAR DATA
    // ==========================================

    function formatDate(dateValue) {

        if (!dateValue) {
            return '';
        }

        const date = new Date(
            String(dateValue).replace(' ', 'T')
        );

        if (isNaN(date.getTime())) {
            return dateValue;
        }

        return date.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

    }


    // ==========================================
    // ABRIR EMAIL
    // ==========================================

    window.openEmail = function(email) {

        if (!email || !listView || !detailView) {
            return;
        }

        const sender = document.getElementById('detail-sender');
        const subject = document.getElementById('detail-subject');
        const date = document.getElementById('detail-date');
        const text = document.getElementById('detail-text');
        const receiver = document.getElementById('detail-receiver');

        if (sender) {
            sender.textContent = email.from || '';
        }

        if (subject) {
            subject.textContent = email.subject || '(Sem assunto)';
        }

        if (date) {
            date.textContent = formatDate(email.date);
        }

        if (text) {
            text.textContent = email.body || '';
        }

        if (receiver) {
            receiver.textContent = email.to || '';
        }

        listView.style.display = 'none';
        detailView.style.display = 'block';

    };


    // ==========================================
    // RENDERIZAR UM EMAIL
    // ==========================================

    function createEmailElement(email) {

        const card = document.createElement('article');

        card.className = 'email-card';
        card.dataset.emailId = email.id;

        const isFavorite = favoriteIds.has(Number(email.id));

        // Remetente ou destinatário
        const contact = document.createElement('div');

        contact.className = 'email-contact';

        contact.textContent = currentPage === 'sent'
            ? `Para: ${email.to}`
            : `De: ${email.from}`;


        // Assunto
        const subject = document.createElement('h3');

        subject.className = 'email-subject';
        subject.textContent = email.subject || '(Sem assunto)';


        // Prévia da mensagem
        const preview = document.createElement('p');

        preview.className = 'email-preview';

        preview.textContent = (email.body || '').length > 100
            ? email.body.substring(0, 100) + '...'
            : email.body || '';


        // Data
        const date = document.createElement('span');

        date.className = 'email-date';
        date.textContent = formatDate(email.date);


        // Botão de favorito
        const favoriteButton = document.createElement('button');

        favoriteButton.type = 'button';
        favoriteButton.className = 'favorite-button';

        favoriteButton.dataset.action = 'favorite';

        favoriteButton.dataset.emailId = email.id;

        favoriteButton.textContent = isFavorite ? '★' : '☆';

        favoriteButton.title = isFavorite
            ? 'Remover dos favoritos'
            : 'Adicionar aos favoritos';


        card.appendChild(contact);
        card.appendChild(subject);
        card.appendChild(preview);
        card.appendChild(date);
        card.appendChild(favoriteButton);

        return card;

    }


    // ==========================================
    // RENDERIZAR LISTA DE EMAILS
    // ==========================================

    function renderEmails(emails) {

        if (!emailListContainer) {
            return;
        }

        emailListContainer.innerHTML = '';

        if (!emails.length) {

            const emptyMessage = document.createElement('p');

            emptyMessage.className = 'empty-emails';

            emptyMessage.textContent =
                currentPage === 'favorites'
                    ? 'Você ainda não possui emails favoritos.'
                    : currentPage === 'sent'
                        ? 'Você ainda não enviou nenhum email.'
                        : 'Sua caixa de entrada está vazia.';

            emailListContainer.appendChild(emptyMessage);

            return;

        }

        emails.forEach(email => {

            const element = createEmailElement(email);

            emailListContainer.appendChild(element);

        });

    }


    // ==========================================
    // CARREGAR EMAILS DA API
    // ==========================================

    async function carregarEmails() {

        if (!emailListContainer) {
            return;
        }

        emailListContainer.textContent = 'Carregando emails...';

        try {

            let endpoint = API.inbox;

            if (currentPage === 'sent') {

                endpoint = API.sent;

            } else if (currentPage === 'favorites') {

                endpoint = API.favorites;

            }


            // Buscar emails
            const data = await requestJSON(endpoint);

            displayedEmails = data.emails || [];


            // Identificar favoritos
            if (currentPage === 'favorites') {

                displayedEmails.forEach(email => {
                    favoriteIds.add(Number(email.id));
                });

            } else {

                // Consultar favoritos para identificar
                // quais emails já estão marcados.
                try {

                    const favoritesData = await requestJSON(API.favorites);

                    favoriteIds = new Set(
                        (favoritesData.emails || [])
                            .map(email => Number(email.id))
                    );

                } catch (error) {

                    console.warn(
                        'Não foi possível consultar os favoritos:',
                        error.message
                    );

                }

            }

            renderEmails(displayedEmails);

        } catch (error) {

            console.error('Erro ao carregar emails:', error);

            emailListContainer.textContent =
                'Não foi possível carregar os emails.';

        }

    }


    // ==========================================
    // ADICIONAR / REMOVER FAVORITOS
    // ==========================================

    async function alternarFavorito(emailId) {

        const id = Number(emailId);

        const jaFavorito = favoriteIds.has(id);

        const endpoint = jaFavorito
            ? API.removeFavorite
            : API.addFavorite;

        try {

            const data = await requestJSON(endpoint, {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify({
                    email_id: id
                })

            });


            if (jaFavorito) {

                favoriteIds.delete(id);

                // Na página de favoritos, remover
                // o card imediatamente.
                if (currentPage === 'favorites') {

                    displayedEmails = displayedEmails.filter(
                        email => Number(email.id) !== id
                    );

                    renderEmails(displayedEmails);

                    return;

                }

            } else {

                favoriteIds.add(id);

            }


            // Atualizar o botão
            const button = emailListContainer.querySelector(
                `[data-action="favorite"][data-email-id="${id}"]`
            );

            if (button) {

                button.textContent = jaFavorito ? '☆' : '★';

                button.title = jaFavorito
                    ? 'Adicionar aos favoritos'
                    : 'Remover dos favoritos';

            }

            console.log(data.message);

        } catch (error) {

            console.error('Erro ao atualizar favorito:', error);

            alert(error.message);

        }

    }


    // ==========================================
    // EVENTOS DA LISTA DE EMAILS
    // ==========================================

    if (emailListContainer) {

        emailListContainer.addEventListener('click', async (event) => {

            // Verificar clique no botão de favorito
            const favoriteButton = event.target.closest(
                '[data-action="favorite"]'
            );

            if (favoriteButton) {

                event.stopPropagation();

                const emailId = favoriteButton.dataset.emailId;

                await alternarFavorito(emailId);

                return;

            }


            // Verificar clique no card
            const card = event.target.closest('.email-card');

            if (card) {

                const emailId = Number(card.dataset.emailId);

                const email = displayedEmails.find(
                    item => Number(item.id) === emailId
                );

                if (email) {
                    window.openEmail(email);
                }

            }

        });

    }


    // ==========================================
    // VOLTAR PARA LISTA DE EMAILS
    // ==========================================

    const backBtn = document.getElementById('back-to-list');

    if (backBtn && listView && detailView) {

        backBtn.addEventListener('click', () => {

            detailView.style.display = 'none';
            listView.style.display = 'block';

        });

    }


    // ==========================================
    // INICIALIZAR LISTA
    // ==========================================

    carregarEmails();


    // ==========================================
    // CADASTRO - ETAPAS
    // ==========================================

    const etapasCadastro = document.querySelectorAll('.cadastro-etapa');

    const btnAvancarCadastro = document.getElementById('btn-avancar');

    const tituloCadastro = document.getElementById('titulo-cadastro');

    const descricaoCadastro = document.getElementById('descricao-cadastro');

    const campoAlerts = document.getElementById('alerts');

    const formCadastro = document.getElementById('form-cadastro');

    if (btnAvancarCadastro) {

        let etapaAtual = 1;

        const titulos = [
            'Criar conta',
            'Informações básicas',
            'Criar endereço de e-mail',
            'Criar senha'
        ];

        const descricoes = [
            'Digite seu nome e sobrenome',
            'Informe sua data de nascimento e gênero',
            'Escolha um endereço de acesso para o BCC-Mail',
            'Crie uma senha para sua conta com pelo menos 6 caracteres e 1 número'
        ];


        // ==========================================
        // MOSTRAR ETAPA DO CADASTRO
        // ==========================================

        function mostrarEtapa(numero) {

            etapasCadastro.forEach((etapa, index) => {

                etapa.hidden = index + 1 !== numero;

            });

            if (tituloCadastro) {
                tituloCadastro.textContent = titulos[numero - 1];
            }

            if (descricaoCadastro) {
                descricaoCadastro.textContent = descricoes[numero - 1];
            }

            btnAvancarCadastro.textContent =
                numero === 4 ? 'Criar conta' : 'Avançar';

            etapaAtual = numero;

        }


        mostrarEtapa(1);


        // ==========================================
        // AVANÇAR NO CADASTRO
        // ==========================================

        btnAvancarCadastro.addEventListener('click', function () {

            if (campoAlerts) {
                campoAlerts.textContent = '';
            }

            const etapa = document.getElementById(
                'etapa-' + etapaAtual
            );

            if (!etapa) {
                return;
            }

            const campos = etapa.querySelectorAll('input, select');

            // Validar campos obrigatórios
            for (const campo of campos) {

                if (!campo.checkValidity()) {

                    campo.reportValidity();

                    return;

                }

            }


            // ==========================================
            // VALIDAR ENDEREÇO DE EMAIL
            // ==========================================

            if (etapaAtual === 3) {

                const email = document.getElementById(
                    'email-cadastro'
                ).value;

                if (!/^[a-zA-Z0-9.]+$/.test(email)) {

                    if (campoAlerts) {
                        campoAlerts.textContent =
                            'O endereço deve conter apenas letras, números e pontos.';
                    }

                    return;

                }

            }


            // ==========================================
            // VALIDAR SENHA
            // ==========================================

            if (etapaAtual === 4) {

                const senha = document.getElementById(
                    'senha-cadastro'
                ).value;

                const confirmar = document.getElementById(
                    'confirmar-senha'
                ).value;


                const erro = [

                    {
                        invalida: senha.length < 6,
                        mensagem: 'A senha deve ter pelo menos 6 caracteres.'
                    },

                    {
                        invalida: !/[0-9]/.test(senha),
                        mensagem: 'A senha deve conter pelo menos 1 número.'
                    },

                    {
                        invalida: !/[a-zA-Z]/.test(senha),
                        mensagem: 'A senha deve conter pelo menos 1 letra.'
                    },

                    {
                        invalida: senha !== confirmar,
                        mensagem: 'As senhas não são iguais.'
                    }

                ].find(regra => regra.invalida);


                if (erro) {

                    if (campoAlerts) {
                        campoAlerts.textContent = erro.mensagem;
                    }

                    return;

                }


                // Enviar formulário
                if (formCadastro) {

                    formCadastro.requestSubmit();

                }

                return;

            }


            mostrarEtapa(etapaAtual + 1);

        });


        // ==========================================
        // MOSTRAR / OCULTAR SENHA DO CADASTRO
        // ==========================================

        const mostrarSenha = document.getElementById(
            'mostrar-senha'
        );

        if (mostrarSenha) {

            mostrarSenha.addEventListener('change', function () {

                const senha = document.getElementById(
                    'senha-cadastro'
                );

                const confirmar = document.getElementById(
                    'confirmar-senha'
                );

                senha.type = this.checked ? 'text' : 'password';

                confirmar.type = this.checked ? 'text' : 'password';

            });

        }


        // ==========================================
        // ENTER PARA AVANÇAR NO CADASTRO
        // ==========================================

        if (formCadastro) {

            formCadastro.addEventListener('keydown', (event) => {

                if (
                    event.key === 'Enter' &&
                    event.target.tagName !== 'TEXTAREA' &&
                    event.target !== btnAvancarCadastro
                ) {

                    event.preventDefault();

                    btnAvancarCadastro.click();

                }

            });

        }

    }

});