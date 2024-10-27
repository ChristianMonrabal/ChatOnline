<?php
include('../config/db.php');
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: ../public/dashboard.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$amigo_id = $_GET['id'];

// Eliminar la amistad
$query_delete = "DELETE FROM Amistades 
                 WHERE (usuario_id = ? AND amigo_id = ?) 
                 OR (usuario_id = ? AND amigo_id = ?)";
$stmt_delete = $conn->prepare($query_delete);
$stmt_delete->bind_param("iiii", $usuario_id, $amigo_id, $amigo_id, $usuario_id);

if ($stmt_delete->execute()) {
    $_SESSION['mensaje'] = "Amigo eliminado con éxito.";
} else {
    $_SESSION['error_mensaje'] = "Error al eliminar al amigo.";
}

$stmt_delete->close();
$conn->close();

header("Location: ../public/dashboard.php");
exit();
?>

