<?php
include('../config/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty(trim($_POST['search']))) {
        $search = mysqli_real_escape_string($conn, trim($_POST['search']));
        $query = "SELECT * FROM Usuarios WHERE username LIKE '%$search%' OR nombre_real LIKE '%$search%'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($user = mysqli_fetch_assoc($result)) {
                echo "<div class='search-result'>";
                echo $user['username'] . " - " . $user['nombre_real'];
                echo "<form action='../actions/friend_request_action.php' method='POST' class='d-inline'>
                        <input type='hidden' name='amigo_id' value='" . $user['id'] . "'>
                        <br>
                        <button type='submit' class='btn btn-success btn-sm' style= 'background-color: #25D366;'>Enviar solicitud de amistad</button>
                    </form>";
                echo "</div>";
            }
        } else {
            echo "<p style= 'color: red;' >No se encontraron usuarios.</p>";
        }
    } else {
        echo "<p style= 'color: red;'>Introduce un usuario para buscar.</p>";
    }
}
?>
