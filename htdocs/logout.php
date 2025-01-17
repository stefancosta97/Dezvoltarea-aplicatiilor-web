<?php
session_start();

// Șterge toate datele din sesiune
session_unset();

// Distruge sesiunea
session_destroy();

// Redirecționează utilizatorul către pagina de autentificare
header("Location: index.php");
exit();
?>