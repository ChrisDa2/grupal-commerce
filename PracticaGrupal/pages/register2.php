<?php
// Maneja la solicitud POST para el registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoge los datos enviados por el formulario
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Inicializa un mensaje de error vacío
    $response = ['status' => 'error', 'message' => ''];

    // Validaciones básicas
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $response['message'] = 'All fields are required.';
        echo json_encode($response);
        exit;
    }
/*
    // Validación de la contraseña
    if (strlen($password) < 8) {
        $response['message'] = 'Password must be at least 8 characters long.';
        echo json_encode($response);
        exit;
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $response['message'] = 'Password must contain at least one uppercase letter.';
        echo json_encode($response);
        exit;
    } elseif (!preg_match("/[a-z]/", $password)) {
        $response['message'] = 'Password must contain at least one lowercase letter.';
        echo json_encode($response);
        exit;
    } elseif (!preg_match("/[0-9]/", $password)) {
        $response['message'] = 'Password must contain at least one number.';
        echo json_encode($response);
        exit;
    } elseif (!preg_match("/[\W_]/", $password)) {
        $response['message'] = 'Password must contain at least one special character (e.g., @, #, $, %, etc.).';
        echo json_encode($response);
        exit;
    } elseif ($password !== $confirm_password) {
        $response['message'] = 'Passwords do not match.';
        echo json_encode($response);
        exit;
    }
*/
    // Comprueba si el email o el nombre de usuario ya existen
    $stmt_check = $mysqliM->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    if (!$stmt_check) {
        $response['message'] = 'Database error: ' . $mysqliM->error;
        echo json_encode($response);
        exit;
    }
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $response['message'] = 'Username or email already exists.';
        $stmt_check->close();
        echo json_encode($response);
        exit;
    }
    $stmt_check->close();

    // Hashea la contraseña antes de almacenarla
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Inserta el nuevo usuario en la base de datos
    $stmt = $mysqliM->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        $response['message'] = 'Database error: ' . $mysqliM->error;
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Registration successful.';
    } else {
        $response['message'] = 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $mysqliM->close();
    echo json_encode($response);
    exit;
}
?>

<!-- Registration Form -->
<h2>Register</h2>
<?php if (isset($error_message) && !empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form method="POST" action="index.php">
    <input type="hidden" name="data-page" value="register2">
    <label>Username: </label><input type="text" name="username" required><br><br>
    <label>Email: </label><input type="email" name="email" required><br><br>
    <label>Password: </label><input type="password" name="password" required><br><br>
    <label>Confirm Password: </label><input type="password" name="confirm_password" required><br><br>
    <button type="submit">Register</button>
</form>

<p><a href="#" data-page="login">Already have an account? Login here.</a></p>

