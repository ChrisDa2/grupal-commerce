<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Fetch the user by email
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $password = trim($_POST['password']);
         
        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            echo "Correct password.";
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>


<form method="POST">
    <label>Email: </label><input type="email" name="email" required>
    <br><br>
    <label>Password: </label><input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
<br>
<a href="index.php?page=forgot_password">Forgot Password?</a>
