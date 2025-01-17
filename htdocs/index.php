<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Hypermarket</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Bine ați venit la Hypermarket!</h1>
        </header>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Bine ai venit, <?php echo htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8'); ?>!</p>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="dashboard.php">Admin Dashboard</a> |
                    <a href="manage_orders.php">Gestionează Comenzi</a> |
                <?php else: ?>
                    <a href="products.php">Produse</a> |
                    <a href="cart.php">Coșul meu</a> |
                    <a href="orders.php">Istoric Comenzi</a> |
                <?php endif; ?>
                <a href="logout.php">Deconectare</a>
            <?php else: ?>
                <a href="login.php">Autentificare</a> |
                <a href="register.php">Înregistrare</a>
            <?php endif; ?>
        </nav>
        <main>
            <section>
                <h2>Despre Hypermarket</h2>
                <p>
                    Descoperă cele mai bune oferte la produsele tale preferate! 
                    Produse de calitate, la prețuri accesibile, pentru toate nevoile familiei tale.
                </p>
            </section>
            <section>
                <h2>Oferte speciale</h2>
                <p>
                    Vizitează secțiunea <a href="products.php">Produse</a> pentru a vedea ce ți-am pregătit.
                </p>
            </section>
        </main>
        <footer>
            <p>&copy; 2025 Hypermarket. Toate drepturile rezervate.</p>
        </footer>
    </div>
</body>
</html>
