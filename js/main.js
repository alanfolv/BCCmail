document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');

    //menu lateral
    if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('open');
        } else {
            sidebar.classList.toggle('collapsed');
        }
    });
}


//alternar os temas
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        htmlElement.setAttribute('data-theme', 'dark');
        updateThemeIcon('dark');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                htmlElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                updateThemeIcon('light');
            } else {
                htmlElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                updateThemeIcon('dark');
            }
        });
    }

   
});


//adicionar função mostrar sua senha
    const showPassword = document.getElementById('show-password');
    const passwordInput = document.getElementById('senha');

    if (showPassword && passwordInput) {
        showPassword.addEventListener('change', function () {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
}

//abrir o email
function openEmail(sender, subject, snippet, time) {
    const listView = document.getElementById('email-list-view');
    const detailView = document.getElementById('email-detail-view');

    document.getElementById('detail-sender').textContent = sender;
    document.getElementById('detail-subject').textContent = subject;
    document.getElementById('detail-date').textContent = time;
    document.getElementById('detail-text').textContent = snippet + " — Conteúdo completo da mensagem.";

    if (listView && detailView) {
        listView.style.display = 'none';
        detailView.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', () => {
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