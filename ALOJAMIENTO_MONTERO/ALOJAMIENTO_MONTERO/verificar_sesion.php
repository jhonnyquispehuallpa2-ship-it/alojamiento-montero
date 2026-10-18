<?php
/* =====================================================
   PROTEGER PÁGINAS DEL PANEL ADMINISTRATIVO
   Si no existe una sesión iniciada, regresa al login.
   ===================================================== */

session_start();

if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.php");
    exit;
}
?>
