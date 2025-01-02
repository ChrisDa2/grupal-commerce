<?php
session_start();
require 'database.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// List of allowed pages to avoid external access
$allowedPages = [
    'index', 'home', 'products', 'product', 'cart', 'admin', 'login', 'logout', 'register', 'register2', 'forgot_password', 'reset_password', 'change_password', 'confirm_email', 'filterProductsChristian'
];

// If the page is not allowed, default to 'home'
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

include 'header.php';

include "pages/$page.php";
?>

<script>
    //String currentPage = "";
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

    function initializeFormHandlers() {
        
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
    //loadPage(currentPage);
</script>

<?php
include 'footer.php';
?>