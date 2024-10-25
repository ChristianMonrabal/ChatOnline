<?php
include('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['amigo_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
        $amigo_id = $_POST['amigo_id'];

        // Verificar si ya existe una solicitud de amistad
        $check_query = "SELECT * FROM Amistades WHERE usuario_id = '$usuario_id' AND amigo_id = '$amigo_id' AND estado = 'pendiente'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) == 0) {
            $query = "INSERT INTO Amistades (usuario_id, amigo_id, estado) VALUES ('$usuario_id', '$amigo_id', 'pendiente')";
            if (mysqli_query($conn, $query)) {
                header("Location: ../public/dashboard.php");
            } else {
                echo "Error al enviar la solicitud de amistad.";
            }
        } else {
            echo "Ya existe una solicitud de amistad pendiente.";
        }
    } elseif (isset($_POST['solicitud_id'])) {
        $solicitud_id = $_POST['solicitud_id'];
        $accion = $_POST['accion'];

        $query = "UPDATE Amistades SET estado = '$accion' WHERE id = '$solicitud_id'";
        if (mysqli_query($conn, $query)) {
            header("Location: ../public/dashboard.php");
        } else {
            echo "Error al gestionar la solicitud de amistad.";
        }
    }
}
?>
