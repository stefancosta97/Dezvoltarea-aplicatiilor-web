<?php
session_start();
include 'db_config.php';

$result = $conn->query("SELECT * FROM products WHERE stock > 0");
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Produse disponibile</title>
</head>
<body>
    <h1>Produse disponibile</h1>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <?php echo htmlspecialchars($row['name']); ?> - 
                <?php echo htmlspecialchars($row['category']); ?> - 
                <?php echo $row['price']; ?> lei - 
                Stoc: <?php echo $row['stock']; ?>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'client'): ?>
                    - <a href="add_to_cart.php?id=<?php echo $row['id']; ?>">Adaugă în coș</a>
                <?php else: ?>
                    - <span style="color: red;">Conectează-te pentru a adăuga în coș</span>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
