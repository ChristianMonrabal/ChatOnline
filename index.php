<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ./views/form.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatOnline</title>
</head>
<body>
    <p>Bienvenido, <?php echo ($_SESSION['nombre_real']); ?>!</p>

    <form action="./db/logout.php" method="post">
        <button type="submit">Cerrar sesión</button>
    </form>
</body>
</html>
