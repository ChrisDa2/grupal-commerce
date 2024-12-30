<?php
require '../database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Busca el usuario por email
    $stmt = $mysqliM->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifica la contraseña hasheada
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            echo "Correct password.";
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>

<section>
    <h2>Login</h2>
    <form id="login-form">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <p id="login-message"></p>
    <a href="#" data-page="register2">Register</a> | <a href="#" data-page="forgot_password">Forgot Password?</a>
</section>
