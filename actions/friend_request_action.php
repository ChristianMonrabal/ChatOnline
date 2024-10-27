<?php
include('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['amigo_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
        $amigo_id = $_POST['amigo_id'];

        $check_friends_query = "SELECT * FROM Amistades 
                                WHERE ((usuario_id = '$usuario_id' AND amigo_id = '$amigo_id') 
                                OR (usuario_id = '$amigo_id' AND amigo_id = '$usuario_id')) 
                                AND estado = 'aceptada'";
        $check_friends_result = mysqli_query($conn, $check_friends_query);

        if (mysqli_num_rows($check_friends_result) > 0) {
            $_SESSION['error_mensaje'] = "Este usuario ya es tu amigo.";
            header("Location: ../public/dashboard.php");
            exit();
        }

        $check_query = "SELECT * FROM Amistades WHERE usuario_id = '$usuario_id' AND amigo_id = '$amigo_id' AND estado = 'pendiente'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) == 0) {
            $check_rejected_query = "SELECT * FROM Amistades 
                                    WHERE usuario_id = '$usuario_id' 
                                    AND amigo_id = '$amigo_id' 
                                    AND estado = 'rechazada' 
                                    AND fecha_actualizacion > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $check_rejected_result = mysqli_query($conn, $check_rejected_query);

            if (mysqli_num_rows($check_rejected_result) == 0) {
                $query = "INSERT INTO Amistades (usuario_id, amigo_id, estado, fecha_actualizacion) 
                            VALUES ('$usuario_id', '$amigo_id', 'pendiente', NOW())";
                if (mysqli_query($conn, $query)) {
                    header("Location: ../public/dashboard.php");
                } else {
                    echo "Error al enviar la solicitud de amistad.";
                }
            } else {
                $_SESSION['error_mensaje'] = "No puedes enviar una solicitud de amistad a este usuario hasta que pasen 24 horas desde la última solicitud rechazada.";
                header("Location: ../public/dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error_mensaje'] = "Ya existe una solicitud de amistad pendiente.";
            header("Location: ../public/dashboard.php");
            exit();
        }
    } elseif (isset($_POST['solicitud_id'])) {
        $solicitud_id = $_POST['solicitud_id'];
        $accion = $_POST['accion'];

        $query = "UPDATE Amistades SET estado = '$accion', fecha_actualizacion = NOW() WHERE id = '$solicitud_id'";
        if (mysqli_query($conn, $query)) {
            header("Location: ../public/dashboard.php");
        } else {
            echo "Error al gestionar la solicitud de amistad.";
        }
    }
}
?>
