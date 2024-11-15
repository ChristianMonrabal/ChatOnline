<?php
// Incluye el archivo de configuración de la base de datos
include('../config/db.php');
// Inicia la sesión para gestionar las variables de sesión
session_start();

// Verifica si el usuario está autenticado y si se ha proporcionado un ID de amigo
if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    // Redirige al dashboard si no se cumplen las condiciones
    header("Location: ../public/dashboard.php");
    exit();
}

// Obtiene el ID del usuario y el ID del amigo desde la sesión y los parámetros GET
$usuario_id = $_SESSION['usuario_id'];
$amigo_id = $_GET['id'];

// Inicia una transacción
mysqli_begin_transaction($conn);

try {
    // Prepara la consulta SQL para eliminar la relación de amistad entre los dos usuarios
    $query_delete = "DELETE FROM Amistades 
                    WHERE (usuario_id = ? AND amigo_id = ?) 
                    OR (usuario_id = ? AND amigo_id = ?)";
    $stmt = mysqli_prepare($conn, $query_delete);

    // Vincula los parámetros a la consulta preparada
    mysqli_stmt_bind_param($stmt, 'iiii', $usuario_id, $amigo_id, $amigo_id, $usuario_id);

    // Ejecuta la consulta para eliminar la amistad
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al eliminar la amistad.");
    }

    // Cierra el statement
    mysqli_stmt_close($stmt);

    // Prepara la consulta SQL para eliminar los mensajes entre los dos usuarios
    $query_delete_messages = "DELETE FROM Mensajes 
                            WHERE (emisor_id = ? AND receptor_id = ?) 
                            OR (emisor_id = ? AND receptor_id = ?)";
    $stmt = mysqli_prepare($conn, $query_delete_messages);

    // Vincula los parámetros a la consulta preparada
    mysqli_stmt_bind_param($stmt, 'iiii', $usuario_id, $amigo_id, $amigo_id, $usuario_id);

    // Ejecuta la consulta para eliminar los mensajes
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al eliminar los mensajes.");
    }

    // Cierra el statement
    mysqli_stmt_close($stmt);

    // Confirma la transacción
    mysqli_commit($conn);

    // Almacena un mensaje de éxito en la sesión
    $_SESSION['mensaje'] = "Amigo y mensajes eliminados con éxito.";
} catch (Exception $e) {
    // Si ocurre un error, revierte la transacción
    mysqli_rollback($conn);
    // Almacena el mensaje de error en la sesión
    $_SESSION['error_mensaje'] = $e->getMessage();
}

// Cierra la conexión a la base de datos
mysqli_close($conn);

// Redirige al usuario de vuelta al dashboard
header("Location: ../public/dashboard.php");
exit();
?>
