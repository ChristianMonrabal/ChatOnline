<?php
// Incluye el archivo de configuración de la base de datos
include('../config/db.php');
// Inicia la sesión para gestionar las variables de sesión
session_start();

// Comprueba si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Verifica si se ha enviado un ID de amigo, lo que indica una nueva solicitud de amistad
    if (isset($_POST['amigo_id'])) {
        // Obtiene el ID del usuario y del amigo desde la sesión y el formulario
        $usuario_id = $_SESSION['usuario_id'];
        $amigo_id = $_POST['amigo_id'];

        // Consulta para comprobar si ya existe una amistad aceptada entre los dos usuarios
        $check_friends_query = "SELECT * FROM Amistades 
                                WHERE ((usuario_id = '$usuario_id' AND amigo_id = '$amigo_id') 
                                OR (usuario_id = '$amigo_id' AND amigo_id = '$usuario_id')) 
                                AND estado = 'aceptada'";
        $check_friends_result = mysqli_query($conn, $check_friends_query);

        // Si ya son amigos, muestra un mensaje de error y redirige al dashboard
        if (mysqli_num_rows($check_friends_result) > 0) {
            $_SESSION['error_mensaje'] = "Este usuario ya es tu amigo.";
            header("Location: ../public/dashboard.php");
            exit();
        }

        // Consulta para comprobar si ya existe una solicitud de amistad pendiente
        $check_query = "SELECT * FROM Amistades WHERE usuario_id = '$usuario_id' AND amigo_id = '$amigo_id' AND estado = 'pendiente'";
        $check_result = mysqli_query($conn, $check_query);

        // Si no existe una solicitud pendiente
        if (mysqli_num_rows($check_result) == 0) {
            // Consulta para comprobar si la última solicitud fue rechazada en las últimas 24 horas
            $check_rejected_query = "SELECT * FROM Amistades 
                                    WHERE usuario_id = '$usuario_id' 
                                    AND amigo_id = '$amigo_id' 
                                    AND estado = 'rechazada' 
                                    AND fecha_actualizacion > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $check_rejected_result = mysqli_query($conn, $check_rejected_query);

            // Si no hubo una solicitud rechazada en las últimas 24 horas
            if (mysqli_num_rows($check_rejected_result) == 0) {
                // Inserta una nueva solicitud de amistad en estado "pendiente"
                $query = "INSERT INTO Amistades (usuario_id, amigo_id, estado, fecha_actualizacion) 
                            VALUES ('$usuario_id', '$amigo_id', 'pendiente', NOW())";
                if (mysqli_query($conn, $query)) {
                    // Redirige al dashboard si la solicitud se envió con éxito
                    header("Location: ../public/dashboard.php");
                } else {
                    echo "Error al enviar la solicitud de amistad.";
                }
            } else {
                // Si se rechazó recientemente, muestra un mensaje de error
                $_SESSION['error_mensaje'] = "No puedes enviar una solicitud de amistad a este usuario hasta que pasen 24 horas desde la última solicitud rechazada.";
                header("Location: ../public/dashboard.php");
                exit();
            }
        } else {
            // Si ya existe una solicitud pendiente, muestra un mensaje de error
            $_SESSION['error_mensaje'] = "Ya existe una solicitud de amistad pendiente.";
            header("Location: ../public/dashboard.php");
            exit();
        }

    // Verifica si se ha enviado una acción sobre una solicitud existente
    } elseif (isset($_POST['solicitud_id'])) {
        // Obtiene el ID de la solicitud y la acción (aceptar o rechazar) del formulario
        $solicitud_id = $_POST['solicitud_id'];
        $accion = $_POST['accion'];

        // Actualiza el estado de la solicitud con la acción proporcionada
        $query = "UPDATE Amistades SET estado = '$accion', fecha_actualizacion = NOW() WHERE id = '$solicitud_id'";
        if (mysqli_query($conn, $query)) {
            // Redirige al dashboard si se actualizó la solicitud con éxito
            header("Location: ../public/dashboard.php");
        } else {
            echo "Error al gestionar la solicitud de amistad.";
        }
    }
}
?>
