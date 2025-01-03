<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Gym & Bikes</title>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php">Gym & Bikes</a></h1>

            <!-- Search Bar Section -->
            <div class="search-bar">
                <form method="GET" action="index.php">
                    <input type="hidden" name="page" value="products">
                    <input type="text" name="search" placeholder="Search for products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <!-- Navigation Menu -->
            <nav>
                <ul>
                    <li><a href="index.php?page=home">Home</a></li>
                    <li><a href="index.php?page=products">Products</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php?page=cart">Cart</a></li>
                        <li><a href="index.php?page=change_password">Change Password</a></li>
                        <li><a href="index.php?page=logout">Logout</a></li>
                        <?php if ($_SESSION['email'] == 'admin@gmail.com'): ?>
                            <li><a href="index.php?page=admin">Admin</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="index.php?page=login">Login</a></li>
                        <li><a href="index.php?page=register2">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

        </div>
    </header>
    <main>
