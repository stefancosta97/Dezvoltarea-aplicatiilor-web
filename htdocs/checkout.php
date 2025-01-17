<?php
session_start();
include 'db_config.php';

// Verifică dacă utilizatorul este logat
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Verifică dacă coșul este gol
if (empty($cart)) {
    echo "Coșul este gol. Nu puteți finaliza comanda.";
    exit();
}

// Începe o tranzacție
$conn->begin_transaction();

try {
    // Creează comanda
    $total = 0;

    foreach ($cart as $product_id => $quantity) {
        // Verifică dacă produsul există și are stoc suficient
        $stmt = $conn->prepare("SELECT price, stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$product || $product['stock'] < $quantity) {
            throw new Exception("Stoc insuficient pentru produsul cu ID $product_id.");
        }

        $total += $product['price'] * $quantity;

        // Scade stocul produsului
        $new_stock = $product['stock'] - $quantity;
        $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_stock, $product_id);
        $stmt->execute();
        $stmt->close();
    }

    // Inserează comanda în baza de date
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'in procesare')");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // Inserează produsele din coș în order_items
    foreach ($cart as $product_id => $quantity) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $product['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Finalizează tranzacția
    $conn->commit();

    // Golește coșul
    unset($_SESSION['cart']);

    echo "Comanda a fost finalizată cu succes!";
    header("Location: orders.php");
    exit();
} catch (Exception $e) {
    $conn->rollback();
    echo "Eroare la finalizarea comenzii: " . $e->getMessage();
}
?>
