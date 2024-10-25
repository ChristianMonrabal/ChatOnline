<?php
include('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search = mysqli_real_escape_string($conn, trim($_POST['search']));
    $query = "SELECT * FROM Usuarios WHERE username LIKE '%$search%' OR nombre_real LIKE '%$search%'";
    $result = mysqli_query($conn, $query);

    while ($user = mysqli_fetch_assoc($result)) {
        echo $user['username'] . " - " . $user['nombre_real'];
        echo "<form action='../actions/friend_request_action.php' method='POST'>
                <input type='hidden' name='amigo_id' value='" . $user['id'] . "'>
                <button type='submit'>Enviar solicitud de amistad</button>
              </form>";
    }
}
?>

