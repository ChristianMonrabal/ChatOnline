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
    // Limpia el mensaje recibido para eliminar espacios innecesarios
    $mensaje = trim($_POST['mensaje']);

    // Comprueba si el mensaje no está vacío y si no excede los 250 caracteres
    if (!empty($mensaje) && strlen($mensaje) <= 250) {
        // Inicia una transacción
        mysqli_begin_transaction($conn);

        try {
            // Prepara la consulta SQL para insertar el mensaje en la tabla de Mensajes
            $query = "INSERT INTO Mensajes (emisor_id, receptor_id, mensaje, fecha_envio, leido) 
                    VALUES (?, ?, ?, NOW(), 0)";
            $stmt = mysqli_prepare($conn, $query);

            // Vincula los parámetros a la consulta preparada
            mysqli_stmt_bind_param($stmt, 'iis', $emisor_id, $receptor_id, $mensaje);

            // Ejecuta la consulta
            if (mysqli_stmt_execute($stmt)) {
                // Confirma la transacción
                mysqli_commit($conn);
                // Muestra el mensaje enviado en pantalla
                echo ($emisor_id == $_SESSION['usuario_id'] ? "Tú: " : "Ellos: ") . "<br>";
                echo htmlspecialchars($mensaje) . "<br>";
            } else {
                // Si hubo un error al ejecutar la consulta, revierte la transacción
                mysqli_rollback($conn);
                echo "Error al enviar el mensaje.";
            }

            // Cierra el statement
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            // Si ocurre una excepción, revierte la transacción y muestra un mensaje de error
            mysqli_rollback($conn);
            echo "Ocurrió un error al procesar el mensaje.";
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
