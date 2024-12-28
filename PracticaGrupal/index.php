<?php
session_start();
require 'database.php';

include 'header.php';
?>

<main>
    <!-- Contenedor para cargar contenido dinámico -->
    <div id="main-content">
        <h2>Welcome to Gym & Bikes</h2>
        <p>Shop the latest products and enjoy exclusive deals.</p>
    </div>
</main>

<?php include 'footer.php'; ?>
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
                    const response = await fetch('login.php', {
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
