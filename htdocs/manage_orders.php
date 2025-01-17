<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Preluăm toate comenzile
$query = "SELECT o.id, u.username, o.total, o.status, o.created_at 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
        $new_status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');

        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Starea comenzii a fost actualizată!";
            header("Location: manage_orders.php");
            exit();
        }
        $stmt->close();
    } elseif (isset($_POST['delete_order'])) {
        $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);

        // Șterge comanda și articolele asociate
        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success_message'] = "Comanda a fost ștearsă!";
        header("Location: manage_orders.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Gestionare comenzi</title>
</head>
<body>
    <h1>Gestionare comenzi</h1>
    <?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <table border="1">
        <thead>
            <tr>
                <th>ID Comandă</th>
                <th>Client</th>
                <th>Total</th>
                <th>Stare</th>
                <th>Data</th>
                <th>Acțiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo number_format($order['total'], 2); ?> lei</td>
                    <td><?php echo htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                    <td>
                        <!-- Formular pentru actualizarea statusului -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status">
                                <option value="in procesare" <?php echo $order['status'] === 'in procesare' ? 'selected' : ''; ?>>În procesare</option>
                                <option value="livrat" <?php echo $order['status'] === 'livrat' ? 'selected' : ''; ?>>Livrat</option>
                            </select>
                            <button type="submit" name="update_status">Actualizează</button>
                        </form>

                        <!-- Formular pentru ștergerea comenzii -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <button type="submit" name="delete_order" style="color:red;">Șterge</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
