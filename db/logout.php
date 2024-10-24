<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../views/form.php");
    exit();
}

session_unset();
session_destroy();
header("Location: ../views/form.php");
exit();
?>
