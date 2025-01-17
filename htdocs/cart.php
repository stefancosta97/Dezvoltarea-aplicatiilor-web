<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Adăugare produs în coș (metoda POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    if (isset($cart[$product_id])) {
        $cart[$product_id] += $quantity;
    } else {
        $cart[$product_id] = $quantity;
    }

    $_SESSION['cart'] = $cart;

    header("Location: cart.php");
    exit();
}

// Golire coș
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

// Finalizare comandă
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalize'])) {
    $user_id = $_SESSION['user_id'];
    $total = 0;

    foreach ($cart as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $total += $product['price'] * $quantity;
    }

    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'in procesare')");
    $stmt->bind_param("id", $user_id, $total);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        foreach ($cart as $product_id => $quantity) {
            $stmt = $conn->prepare("SELECT price, stock FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            $price = $product['price'];
            $new_stock = $product['stock'] - $quantity;

            if ($new_stock < 0) {
                $_SESSION['error_message'] = "Stoc insuficient pentru produsul cu ID $product_id.";
                header("Location: cart.php");
                exit();
            }

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_stock, $product_id);
            $stmt->execute();
            $stmt->close();
        }

        unset($_SESSION['cart']);
        $_SESSION['success_message'] = "Comanda a fost finalizată cu succes!";
        header("Location: orders.php");
        exit();
    }
}
?>

<h1>Coșul meu</h1>
<link rel="stylesheet" href="style.css?v=1">

<ul>
    <?php 
    $total = 0;
    foreach ($cart as $product_id => $quantity): 
        $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $total += $product['price'] * $quantity;
    ?>
        <li>
            <?= htmlspecialchars($product['name']) ?> - Cantitate: <?= $quantity ?> - Preț: <?= $product['price'] ?> lei
        </li>
    <?php endforeach; ?>
</ul>
<p>Total: <?= $total ?> lei</p>
<form method="POST" style="display: inline;">
    <button type="submit" name="finalize">Finalizează comanda</button>
</form>
<form method="POST" style="display: inline;">
    <button type="submit" name="clear_cart">Golește coșul</button>
</form>
<?php if (isset($_SESSION['error_message'])): ?>
    <p style="color: red;"><?= $_SESSION['error_message'] ?></p>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- Buton de Back -->
<a href="dashboard.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">Înapoi la Produse</a>
