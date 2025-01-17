<?php
session_start();
include 'db_config.php';

// Selectează produsele care au stoc mai mare decât 0
$result = $conn->query("SELECT * FROM products WHERE stock > 0");
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Produse disponibile</title>
    <link rel="stylesheet" href="style.css?v=1">

</head>
<body>
    <header>
        <h1>Produse disponibile</h1>
    </header>
    <nav>
        <a href="products.php">Produse</a> |
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'client'): ?>
                <a href="cart.php">Coșul meu</a> |
                <a href="orders.php">Comenzile mele</a> |
            <?php elseif ($_SESSION['role'] === 'admin'): ?>
                <a href="manage_orders.php">Gestionare comenzi</a> |
                <a href="add_product.php">Adaugă produs</a> |
            <?php endif; ?>
            <a href="logout.php">Deconectare</a>
        <?php else: ?>
            <a href="login.php">Conectare</a> |
            <a href="register.php">Înregistrare</a>
        <?php endif; ?>
    </nav>
    <main>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['name']); ?></strong> - 
                    <?php echo htmlspecialchars($row['category']); ?> - 
                    <span><?php echo $row['price']; ?> lei</span> - 
                    Stoc: <?php echo $row['stock']; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'client'): ?>
                            <?php if ($row['stock'] > 0): ?>
                                - <a href="add_to_cart.php?id=<?php echo $row['id']; ?>">Adaugă în coș</a>
                            <?php else: ?>
                                - <span style="color: red;">Stoc epuizat</span>
                            <?php endif; ?>
                        <?php elseif ($_SESSION['role'] === 'admin'): ?>
                            - <a href="edit_product.php?id=<?php echo $row['id']; ?>">Editează</a>
                            - <a href="delete_product.php?id=<?php echo $row['id']; ?>">Șterge</a>
                        <?php endif; ?>
                    <?php else: ?>
                        - <span style="color: red;">Conectează-te pentru a interacționa</span>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </main>
    <footer>
        <p>&copy; 2025 MiniPic. Toate drepturile rezervate.</p>
    </footer>
</body>
</html>
