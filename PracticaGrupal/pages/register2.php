<?php
// Habilita la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../database.php';

// Maneja la solicitud POST para el registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Incluye la conexión a la base de datos
    

    // Recoge los datos enviados por el formulario
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validaciones básicas
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        die('All fields are required.');
    }

    
    // Validación de la contraseña
    if (strlen($password) < 8) {
        die('Password must be at least 8 characters long.');
    } elseif (!preg_match("/[A-Z]/", $password)) {
        die('Password must contain at least one uppercase letter.');
        exit;
    } elseif (!preg_match("/[a-z]/", $password)) {
        die('Password must contain at least one lowercase letter.');
        exit;
    } elseif (!preg_match("/[0-9]/", $password)) {
        die('Password must contain at least one number.');
        exit;
    } elseif (!preg_match("/[\W_]/", $password)) {
        die('Password must contain at least one special character (e.g., @, #, $, %, etc.).');
        exit;
    } elseif ($password !== $confirm_password) {
        die('Passwords do not match.');
        exit;
    }


    if ($password !== $confirm_password) {
        die('Passwords do not match.');
    }

    // Comprueba si el email o el nombre de usuario ya existen
    $stmt_check = $mysqliM->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    if (!$stmt_check) {
        die('Database error: ' . $mysqliM->error);
    }
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        die('Username or email already exists.');
    }
    $stmt_check->close();

    // Hashea la contraseña antes de almacenarla
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Inserta el nuevo usuario en la base de datos
    $stmt = $mysqliM->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        die('Database error: ' . $mysqliM->error);
    }
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo 'Registration successful.';
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $mysqliM->close();
    exit;
}
?>

<!-- Registration Form -->
<h2>Register</h2>
<form id="register-form" method="POST" action="register2.php">
    <label>Username: </label><input type="text" name="username" required><br><br>
    <label>Email: </label><input type="email" name="email" required><br><br>
    <label>Password: </label><input type="password" name="password" required><br><br>
    <label>Confirm Password: </label><input type="password" name="confirm_password" required><br><br>
    <button type="submit">Register</button>
</form>
<p id="register-message"></p>
<p><a href="#" data-page="login">Already have an account? Login here.</a></p>

