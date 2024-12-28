<?php
session_start(); // Start the session to handle user login status

require 'database.php';

// Determine the current page based on the URL query parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// List of allowed pages to avoid external access
$allowedPages = [
    'main', 'home', 'products', 'product', 'cart','cart_action', 'cart_remove', 'checkout', 'admin', 'edit_product', 'order_success','login', 'logout', 'register', 'register2', 'forgot_password', 'reset_password', 'change_password', 'confirm_email'
];

// If the page is not allowed, default to 'home'
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

// Include the header for the website (this will include the navigation)
include 'header.php';

// Include the requested page based on the query parameter
// The pages are stored in the "pages" folder
//include "pages/$page.php";

?>

<script>
    // Function to load pages dynamically
    async function loadPage(page) {
        try {
            // Fetch the content of the requested page
            const response = await fetch('pages/' + page + '.php');
            if (!response.ok) {
                throw new Error('Failed to load page.');
            }

            // Get the response text and update the main content
            const data = await response.text();
            document.getElementById('main-content').innerHTML = data;
        } catch (error) {
            // Display an error message in case of failure
            document.getElementById('main-content').innerHTML = `<p>Error loading the page: ${error.message}</p>`;
        }
    }

    // Event delegation for navigation links
    document.addEventListener('click', function (e) {
        // Check if the clicked element has the data-page attribute
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            e.preventDefault(); // Prevent default link behavior
            const page = e.target.dataset.page; // Get the value of data-page
            loadPage(page); // Load the requested page
        }
    });
</script>


<?php

// Include the footer for the website
include 'footer.php';

// Close the database connection
$mysqliC->close();
$mysqliH->close();
$mysqliM->close();
?>
