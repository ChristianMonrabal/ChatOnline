<?php
include('../config/db.php');
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: ../public/dashboard.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$amigo_id = $_GET['id'];

$query_delete = "DELETE FROM Amistades 
                WHERE (usuario_id = $usuario_id AND amigo_id = $amigo_id) 
                OR (usuario_id = $amigo_id AND amigo_id = $usuario_id)";

if (mysqli_query($conn, $query_delete)) {
    $_SESSION['mensaje'] = "Amigo eliminado con éxito.";
} else {
    $_SESSION['error_mensaje'] = "Error al eliminar al amigo.";
}

mysqli_close($conn);

header("Location: ../public/dashboard.php");
exit();
?>
