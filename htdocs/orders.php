<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Preia comenzile utilizatorului din baza de date
$stmt = $conn->prepare("SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Istoric comenzi</title>
</head>
<body>
    <h1>Istoric comenzi</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID Comandă</th>
                <th>Total</th>
                <th>Status</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo number_format($order['total'], 2); ?> lei</td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
