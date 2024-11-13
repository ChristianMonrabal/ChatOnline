<?php
// Incluye el archivo de configuración de la base de datos
include('../config/db.php');
// Inicia la sesión para acceder a las variables de sesión del usuario
session_start();

// Verifica si el método de solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene el ID del emisor del mensaje desde la sesión del usuario
    $emisor_id = $_SESSION['usuario_id'];
    // Obtiene el ID del receptor del mensaje enviado desde el formulario
    $receptor_id = $_POST['receptor_id'];
    // Limpia el mensaje recibido para prevenir inyecciones SQL y eliminar espacios innecesarios
    $mensaje = mysqli_real_escape_string($conn, trim($_POST['mensaje']));

    // Comprueba si el mensaje no está vacío y si no excede los 250 caracteres
    if (!empty($mensaje) && strlen($mensaje) <= 250) {
        // Prepara la consulta SQL para insertar el mensaje en la tabla de Mensajes
        $query = "INSERT INTO Mensajes (emisor_id, receptor_id, mensaje, fecha_envio, leido) 
                    VALUES ('$emisor_id', '$receptor_id', '$mensaje', NOW(), 0)";
        // Ejecuta la consulta e inserta el mensaje en la base de datos
        if (mysqli_query($conn, $query)) {
            // Si el mensaje fue enviado exitosamente, muestra el mensaje en pantalla
            echo ($emisor_id == $_SESSION['usuario_id'] ? "Tú: " : "Ellos: ") . "<br>";
            echo htmlspecialchars($mensaje) . "<br>";
        } else {
            // Muestra un mensaje de error si hubo un problema al enviar el mensaje
            echo "Error al enviar el mensaje.";
        }
    } elseif (strlen($mensaje) > 250) {
        // Muestra un mensaje de error si el mensaje es demasiado largo
        echo "El mensaje es demasiado largo, el máximo son 250 caracteres.";
    } else {
        // Muestra un mensaje de error si el mensaje está vacío
        echo "El mensaje no puede estar vacío.";
    }
}
?>
