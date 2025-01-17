<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');

    $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssdisi", $name, $category, $price, $stock, $description, $id);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Eroare la actualizarea produsului.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editează produs</title>
</head>
<body>
    <h1>Editează produs</h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required><br>
        <input type="text" name="category" value="<?php echo $product['category']; ?>" required><br>
        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required><br>
        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required><br>
        <textarea name="description"><?php echo $product['description']; ?></textarea><br>
        <button type="submit">Actualizează</button>
    </form>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
</body>
</html>