<?php
session_start();

// Eliminamos todos los datos de la sesión.
session_destroy();

header("Location: login.php");
exit;
?>
