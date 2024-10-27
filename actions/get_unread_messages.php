<?php
include('../config/db.php');
session_start();

if (!isset($_SESSION['usuario_id'])) {
exit(json_encode([]));
}

$usuario_id = $_SESSION['usuario_id'];

$query = "SELECT u.id, 
        (SELECT COUNT(*) FROM Mensajes 
            WHERE emisor_id = u.id 
            AND receptor_id = '$usuario_id' 
            AND leido = 0) AS mensajes_sin_leer
        FROM Amistades a 
        JOIN Usuarios u ON (a.usuario_id = u.id OR a.amigo_id = u.id) 
        WHERE (a.usuario_id = '$usuario_id' OR a.amigo_id = '$usuario_id') 
        AND a.estado = 'aceptada' 
        AND u.id != '$usuario_id'";

$result = mysqli_query($conn, $query);

$unread_messages = [];
while ($row = mysqli_fetch_assoc($result)) {
$unread_messages[$row['id']] = $row['mensajes_sin_leer'];
}

echo json_encode($unread_messages);
?>