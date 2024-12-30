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

var_dump($_SESSION); // Verifica los datos de la sesión

if (isset($_SESSION['user_id']))
{
    echo "PASUDOFIASJEFÑ3KLE";
}

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

    // Función para actualizar el menú después de hacer login
function updateMenu(isAdmin) {
    const loginLink = document.querySelector('a[data-page="login"]');
    const registerLink = document.querySelector('a[data-page="register2"]');
    const logoutLink = document.querySelector('a[data-page="logout"]');
    const cartLink = document.querySelector('a[data-page="cart"]');
    const changePasswordLink = document.querySelector('a[data-page="change_password"]');
    const adminLink = document.querySelector('a[data-page="admin"]');

    // Mostrar el botón de logout y cart
    if (loginLink) loginLink.style.display = 'none';
    if (registerLink) registerLink.style.display = 'none';
    if (logoutLink) logoutLink.style.display = 'block';
    if (cartLink) cartLink.style.display = 'block';
    if (changePasswordLink) changePasswordLink.style.display = 'block';

    // Mostrar el botón de Admin solo si el usuario es admin
    if (isAdmin) {
        if (adminLink) adminLink.style.display = 'block';
    } else {
        if (adminLink) adminLink.style.display = 'none';
    }
}



// Variable para controlar si ya se han inicializado los formularios
let formInitialized = false;

function initializeFormHandlers() {
    if (formInitialized) return;  // Evita volver a registrar los eventos

    formInitialized = true;  // Marca que los formularios ya han sido inicializados
        // Función para manejar el login en el formulario
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

                    if (result.includes('Correct password')) {

                        // Actualizar el menú dinámicamente
                        //updateMenu(true);  // Pasar true si es admin
                        //loginForm.reset(); // Limpia el formulario
                        // Cargar la página principal después del login
                        //loadPage('home');
                        //window.location.href = 'pages/home.php';
                        //window.location.href = 'pages/login.php';

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
    //loadPage('home');
</script>

<?php
include 'footer.php';
?>