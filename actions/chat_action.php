<?php
include('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emisor_id = $_SESSION['usuario_id'];
    $receptor_id = $_POST['receptor_id'];
    $mensaje = mysqli_real_escape_string($conn, trim($_POST['mensaje']));

    if (!empty($mensaje) && strlen($mensaje) <= 250) {
        $query = "INSERT INTO Mensajes (emisor_id, receptor_id, mensaje, fecha_envio, leido) 
                    VALUES ('$emisor_id', '$receptor_id', '$mensaje', NOW(), 0)";
        if (mysqli_query($conn, $query)) {
            echo $emisor_id == $_SESSION['usuario_id'] ? "Tú: " : "Ellos: ";
            echo htmlspecialchars($mensaje) . "<br>";
        } else {
            echo "Error al enviar el mensaje.";
        }
    } elseif (strlen($mensaje) > 250) {
        echo "El mensaje es demasiado largo, el máximo son 250 caracteres.";
    } else {
        echo "El mensaje no puede estar vacío.";
    }
}
?>
