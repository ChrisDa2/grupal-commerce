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
<section>
    <h2>Welcome to Our E-Commerce Platform</h2>
    <p>Shop the latest products and enjoy exclusive deals.</p>
</section>

<?php
// Include the footer for the website
include 'footer.php';

// Close the database connection
$mysqliC->close();
$mysqliH->close();
$mysqliM->close();
?>
