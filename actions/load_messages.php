<?php
// Incluye el archivo de configuración de la base de datos
include('../config/db.php');
// Inicia la sesión para acceder a las variables de sesión
session_start();

// Verifica que el usuario haya iniciado sesión; si no, muestra un mensaje de error y termina la ejecución
if (!isset($_SESSION['usuario_id'])) {
    exit("Usuario no autenticado.");
}

// Obtiene el ID del usuario actual desde la sesión y el ID del amigo desde los parámetros GET
$usuario_id = $_SESSION['usuario_id'];
$amigo_id = isset($_GET['amigo_id']) ? $_GET['amigo_id'] : null;

// Verifica si se proporcionó un ID de amigo válido
if (!$amigo_id) {
    exit("ID de amigo no proporcionado.");
}

// Inicia una transacción
mysqli_begin_transaction($conn);

try {
    // Prepara la consulta para obtener los mensajes entre el usuario y su amigo, junto con el nombre de usuario del emisor
    $query = "SELECT m.*, u.username AS emisor_nombre 
            FROM Mensajes m 
            JOIN Usuarios u ON m.emisor_id = u.id 
            WHERE (m.emisor_id = ? AND m.receptor_id = ?) 
            OR (m.emisor_id = ? AND m.receptor_id = ?) 
            ORDER BY m.fecha_envio ASC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiii', $usuario_id, $amigo_id, $amigo_id, $usuario_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Prepara y ejecuta la consulta para marcar los mensajes recibidos como leídos en la base de datos
    $update_query = "UPDATE Mensajes SET leido = 1 
                    WHERE emisor_id = ? AND receptor_id = ? AND leido = 0";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, 'ii', $amigo_id, $usuario_id);
    mysqli_stmt_execute($update_stmt);

    // Confirma la transacción
    mysqli_commit($conn);

    // Recorre cada mensaje obtenido de la consulta y muestra el contenido formateado
    while ($mensaje = mysqli_fetch_assoc($result)) {
        // Asigna una clase CSS según si el mensaje fue enviado por el usuario o su amigo
        $class = $mensaje['emisor_id'] == $usuario_id ? "user" : "friend";
        // Muestra el mensaje con el nombre del emisor y su contenido en un contenedor con la clase correspondiente
        echo "<div class='message $class'>";
        echo "<strong>" . htmlspecialchars($mensaje['emisor_nombre']) . ":</strong><br>";
        echo htmlspecialchars($mensaje['mensaje']);
        echo "</div>";
    }
} catch (Exception $e) {
    // Si ocurre un error, revierte la transacción
    mysqli_rollback($conn);
    exit("Ocurrió un error al cargar los mensajes.");
}
?>
