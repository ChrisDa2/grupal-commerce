<?php
session_start();
require 'database.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// List of allowed pages to avoid external access
$allowedPages = [
    'index', 'home', 'products', 'product', 'cart', 'admin', 'login', 'logout', 'register', 'register2', 'forgot_password', 'reset_password', 'change_password', 'confirm_email'
];

// If the page is not allowed, default to 'home'
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

include 'header.php';

?>

<script>
    // Función para cargar páginas dinámicamente
    async function loadPage(page) {
        try {
            const response = await fetch('pages/' + page + '.php');
            if (!response.ok) {
                throw new Error(`Failed to load page ${page}.`);
            }
            const content = await response.text();
            document.getElementById('main-content').innerHTML = content;

            // Vuelve a inicializar los formularios en la nueva página
            initializeFormHandlers();
        } catch (error) {
            document.getElementById('main-content').innerHTML = `<p>Error loading the page: ${error.message}</p>`;
        }
    }

    // Inicializa los manejadores de eventos de formularios
    function initializeFormHandlers() {
        const loginForm = document.querySelector('#login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(loginForm);
                try {
                    const response = await fetch('pages/login.php', {
                        method: 'POST',
                        body: formData,
                    });
                    const result = await response.text();
                    document.getElementById('login-message').innerHTML = result;

                    // Si el login es exitoso, carga la página principal
                    if (result.includes('Correct password')) {
                        loadPage('home');
                    }
                } catch (error) {
                    document.getElementById('login-message').innerHTML = `<p>Error: ${error.message}</p>`;
                }
            });
        }

        const registerForm = document.querySelector('#register-form');
        if (registerForm) {
            registerForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(registerForm);
                try {
                    const response = await fetch('pages/register2.php', {
                        method: 'POST',
                        body: formData,
                    });

                    // Verifica si la respuesta es válida
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    // Obtén la respuesta como texto
                    const result = await response.text();

                    // Manejo del mensaje en el cliente
                    const messageDiv = document.getElementById('register-message');
                    messageDiv.textContent = result;
                    messageDiv.style.color = result.includes('successful') ? 'green' : 'red';

                    // Si el registro fue exitoso, carga la página de inicio
                    if (result.includes('successful')) {
                        registerForm.reset(); // Limpia el formulario
                        loadPage('home');
                    }
                } catch (error) {
                    document.getElementById('register-message').innerHTML = `<p>Error: ${error.message}</p>`;
                }
            });
        }
}


    // Configuración inicial
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('a[data-page]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = link.getAttribute('data-page');
                loadPage(page);
            });
        });

        initializeFormHandlers();
    });
</script>

<?php
include 'footer.php';
?>