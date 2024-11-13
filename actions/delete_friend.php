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

// Consulta SQL para eliminar la relación de amistad entre los dos usuarios
$query_delete = "DELETE FROM Amistades 
                WHERE (usuario_id = $usuario_id AND amigo_id = $amigo_id) 
                OR (usuario_id = $amigo_id AND amigo_id = $usuario_id)";

// Ejecuta la consulta para eliminar la amistad
if (mysqli_query($conn, $query_delete)) {
    // Si la amistad fue eliminada, elimina los mensajes entre los dos usuarios
    $query_delete_messages = "DELETE FROM Mensajes 
                            WHERE (emisor_id = $usuario_id AND receptor_id = $amigo_id) 
                            OR (emisor_id = $amigo_id AND receptor_id = $usuario_id)";

    // Ejecuta la consulta para eliminar los mensajes
    if (mysqli_query($conn, $query_delete_messages)) {
        // Si se eliminaron los mensajes con éxito, almacena un mensaje de éxito en la sesión
        $_SESSION['mensaje'] = "Amigo y mensajes eliminados con éxito.";
    } else {
        // Si hubo un error al eliminar los mensajes, almacena un mensaje de error
        $_SESSION['error_mensaje'] = "Error al eliminar los mensajes.";
    }
} else {
    // Si hubo un error al eliminar la amistad, almacena un mensaje de error
    $_SESSION['error_mensaje'] = "Error al eliminar al amigo.";
}

// Cierra la conexión a la base de datos
mysqli_close($conn);

// Redirige al usuario de vuelta al dashboard
header("Location: ../public/dashboard.php");
exit();
?>
