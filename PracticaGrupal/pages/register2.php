<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form inputs
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $error_message = '';

    // Validate the password
    if (strlen($password) < 8) {
        $error_message = 'Password must be at least 8 characters long.';
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $error_message = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match("/[a-z]/", $password)) {
        $error_message = 'Password must contain at least one lowercase letter.';
    } elseif (!preg_match("/[0-9]/", $password)) {
        $error_message = 'Password must contain at least one number.';
    } elseif (!preg_match("/[\W_]/", $password)) {
        $error_message = 'Password must contain at least one special character (e.g., @, #, $, %, etc.).';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    }

    // If no validation errors, proceed with registration
    if (empty($error_message)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqliM->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "Registration successful.";
            header("Location: index.php?page=login");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<!-- Registration Form -->
<h2>Register</h2>
<?php if (isset($error_message) && !empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST">
    <label>Username: </label><input type="text" name="username" required><br><br>
    <label>Email: </label><input type="email" name="email" required><br><br>
    <label>Password: </label><input type="password" name="password" required><br><br>
    <label>Confirm Password: </label><input type="password" name="confirm_password" required><br><br>
    <button type="submit">Register</button>
</form>

<p><a href="index.php?page=login">Already have an account? Login here.</a></p>

