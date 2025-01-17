<?php
session_start();
include 'db_config.php';

// Verifică dacă utilizatorul este logat și este un client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$message = ""; // Variabilă pentru a stoca mesajul de eroare sau succes

// Verifică dacă ID-ul produsului a fost trimis prin GET
if (isset($_GET['id'])) {
    $product_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($product_id) {
        // Verifică stocul produsului în baza de date
        $stmt = $conn->prepare("SELECT stock, name FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();

        if ($product['stock'] <= 0) {
            $message = "Produsul <strong>" . htmlspecialchars($product['name']) . "</strong> nu este în stoc.";
        } else {
            // Adaugă produsul în coș și actualizează stocul
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$product_id])) {
                if ($_SESSION['cart'][$product_id] >= $product['stock']) {
                    $message = "Produsul <strong>" . htmlspecialchars($product['name']) . "</strong> nu mai poate fi adăugat deoarece stocul este insuficient.";
                } else {
                    $_SESSION['cart'][$product_id]++;
                    $stmt = $conn->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $stmt->close();
                    $message = "Produsul <strong>" . htmlspecialchars($product['name']) . "</strong> a fost adăugat în coș.";
                }
            } else {
                $_SESSION['cart'][$product_id] = 1;
                $stmt = $conn->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $stmt->close();
                $message = "Produsul <strong>" . htmlspecialchars($product['name']) . "</strong> a fost adăugat în coș.";
            }
        }
    } else {
        $message = "Produs invalid.";
    }
}

// Preia lista de produse din baza de date
$result = $conn->query("SELECT * FROM products WHERE stock > 0");
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Produse disponibile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Produse disponibile</h1>
    </header>
    <nav>
        <a href="products.php">Produse</a> |
        <a href="cart.php">Coșul meu</a> |
        <a href="logout.php">Deconectare</a>
    </nav>
    <main>
        <?php if ($message): ?>
            <p style="color: red; font-weight: bold;"><?php echo $message; ?></p>
        <?php endif; ?>

        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['name']); ?></strong> - 
                    <?php echo htmlspecialchars($row['category']); ?> - 
                    <span><?php echo $row['price']; ?> lei</span> - 
                    Stoc: <?php echo $row['stock']; ?>
                    <a href="?id=<?php echo $row['id']; ?>">Adaugă în coș</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </main>
    <footer>
        <p>&copy; 2025 MiniPic. Toate drepturile rezervate.</p>
    </footer>
</body>
</html>
