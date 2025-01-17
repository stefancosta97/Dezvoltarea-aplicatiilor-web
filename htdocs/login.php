<?php
session_start();
include 'db_config.php';

$error_message = ""; // Inițializare mesaj de eroare

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Verifică dacă emailul și parola sunt completate
    if (empty($email) || empty($password)) {
        $error_message = "Te rugăm să completezi toate câmpurile.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();
        $stmt->close();

        // Verificare parolă
        if ($id && password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php"); // Redirecționare la dashboard
            exit();
        } else {
            $error_message = "Email sau parolă incorectă.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare - Hypermarket</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff; /* Fundal albastru deschis */
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-form {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .login-form button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <main>
        <form method="POST" class="login-form">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <label for="password">Parolă:</label>
            <input type="password" id="password" name="password" placeholder="Parolă" required>
            <button type="submit">Autentifică-te</button>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
        </form>

        <!-- Butonul de Back -->
        <a href="index.php">Înapoi la Pagina Principală</a>
    </main>
</body>
</html>
