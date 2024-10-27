<?php
include('../config/db.php');
session_start();

if (!isset($_SESSION['usuario_id'])) {
    exit("Usuario no autenticado.");
}

$usuario_id = $_SESSION['usuario_id'];
$amigo_id = $_GET['amigo_id'];

$query = "SELECT * FROM Mensajes WHERE (emisor_id = '$usuario_id' AND receptor_id = '$amigo_id') 
        OR (emisor_id = '$amigo_id' AND receptor_id = '$usuario_id') 
        ORDER BY fecha_envio ASC";
$result = mysqli_query($conn, $query);

$update_query = "UPDATE Mensajes SET leido = 1 
                WHERE emisor_id = '$amigo_id' AND receptor_id = '$usuario_id' AND leido = 0";
mysqli_query($conn, $update_query);

while ($mensaje = mysqli_fetch_assoc($result)) {
    $class = $mensaje['emisor_id'] == $usuario_id ? "user" : "friend";
    echo "<div class='message $class'>";
    echo htmlspecialchars($mensaje['mensaje']);
    echo "</div>";
}
?>
