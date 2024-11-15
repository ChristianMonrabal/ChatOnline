<?php
// Incluye el archivo de configuración de la base de datos
include('../config/db.php');
// Inicia la sesión para acceder a las variables de sesión
session_start();

// Verifica si el usuario ha iniciado sesión; si no, devuelve una respuesta vacía en formato JSON
if (!isset($_SESSION['usuario_id'])) {
    exit(json_encode([]));
}

// Obtiene el ID del usuario actual desde la sesión
$usuario_id = $_SESSION['usuario_id'];

// Inicia una transacción
mysqli_begin_transaction($conn);

try {
    // Prepara la consulta para obtener los usuarios con los que el usuario actual tiene amistad aceptada y el conteo de mensajes no leídos
    $query = "SELECT u.id, 
            (SELECT COUNT(*) FROM Mensajes 
            WHERE emisor_id = u.id 
            AND receptor_id = ? 
            AND leido = 0) AS mensajes_sin_leer
            FROM Amistades a 
            JOIN Usuarios u ON (a.usuario_id = u.id OR a.amigo_id = u.id) 
            WHERE (a.usuario_id = ? OR a.amigo_id = ?) 
            AND a.estado = 'aceptada' 
            AND u.id != ?";

    // Prepara la consulta
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . mysqli_error($conn));
    }

    // Vincula los parámetros a la consulta
    mysqli_stmt_bind_param($stmt, 'iiii', $usuario_id, $usuario_id, $usuario_id, $usuario_id);

    // Ejecuta la consulta
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Inicializa un array para almacenar los mensajes no leídos por usuario
    $unread_messages = [];
    // Recorre los resultados de la consulta
    while ($row = mysqli_fetch_assoc($result)) {
        // Almacena el conteo de mensajes no leídos para cada usuario amigo
        $unread_messages[$row['id']] = $row['mensajes_sin_leer'];
    }

    // Confirma la transacción
    mysqli_commit($conn);

    // Devuelve los datos de mensajes no leídos en formato JSON
    echo json_encode($unread_messages);
} catch (Exception $e) {
    // Si ocurre un error, revierte la transacción
    mysqli_rollback($conn);
    // Devuelve un mensaje de error en formato JSON
    echo json_encode(['error' => 'Ocurrió un error al obtener los mensajes no leídos.']);
} finally {
    // Cierra el statement
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
}
?>
