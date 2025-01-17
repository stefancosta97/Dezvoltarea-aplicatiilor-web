<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_message = "Parolele nu se potrivesc.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'client')");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Înregistrarea a fost realizată cu succes. Vă puteți autentifica acum!";
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Eroare la crearea contului. Verificați datele și încercați din nou.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Înregistrare - Hypermarket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Înregistrare</h1>
        <form method="POST">
            <label for="username">Nume utilizator:</label>
            <input type="text" id="username" name="username" placeholder="Nume utilizator" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email" required><br>

            <label for="password">Parolă:</label>
            <input type="password" id="password" name="password" placeholder="Parolă" required><br>

            <label for="confirm_password">Confirmă Parola:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmă Parola" required><br>

            <button type="submit">Înregistrează-te</button>
        </form>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"> <?php echo $error_message; ?> </p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <p style="color: green;"> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?> </p>
        <?php endif; ?>

        <!-- Butonul de Back -->
        <a href="index.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Înapoi</a>
    </div>
</body>
</html>
