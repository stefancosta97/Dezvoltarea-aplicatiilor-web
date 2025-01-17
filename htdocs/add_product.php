<?php
session_start();
include 'db_config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');


    if ($price === false || $price < 0) {
        $error_message = "Prețul trebuie să fie un număr pozitiv.";
    } elseif ($stock === false || $stock < 0) {
        $error_message = "Stocul trebuie să fie un număr întreg pozitiv.";
    } else {
        
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $name, $category, $price, $stock, $description);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Eroare la adăugarea produsului.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Adaugă produs</title>
</head>
<body>
    <h1>Adaugă produs</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Nume produs" required><br>
        <input type="text" name="category" placeholder="Categorie" required><br>
        <input type="number" step="0.01" name="price" placeholder="Preț" required><br>
        <input type="number" name="stock" placeholder="Stoc" required><br>
        <textarea name="description" placeholder="Descriere"></textarea><br>
        <button type="submit">Adaugă</button>
    </form>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
</body>
</html>
