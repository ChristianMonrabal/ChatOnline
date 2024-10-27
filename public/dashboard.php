<?php
include('../config/db.php');
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$query_usuario = "SELECT username FROM Usuarios WHERE id = '$usuario_id'";
$result_usuario = mysqli_query($conn, $query_usuario);
$usuario = mysqli_fetch_assoc($result_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatPro</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div id="sidebar">
        <div id="user-info" class="text-center mb-4">
            <h4><?php echo $usuario['username']; ?></h4>
        </div>
        <h2>Buscar Usuarios</h2>
        <form id="search-form">
            <input type="text" name="search" placeholder="Buscar usuarios" class="form-control">
            <button type="submit" class="btn btn-success mt-2">Buscar</button>
        </form>
        <div id="search-results"></div>
        <?php
        if (isset($_SESSION['error_mensaje'])) {
            echo "<span style='color: red;'>" . htmlspecialchars($_SESSION['error_mensaje']) . "</span>";
            unset($_SESSION['error_mensaje']);
        }
        ?>

        <h2>Solicitudes de Amistad Pendientes</h2>
        <?php
        $query = "SELECT DISTINCT a.id AS solicitud_id, u.username 
                    FROM Amistades a 
                    JOIN Usuarios u ON a.usuario_id = u.id 
                    WHERE a.amigo_id = '$usuario_id' AND a.estado = 'pendiente'";
        $result = mysqli_query($conn, $query);

        while ($solicitud = mysqli_fetch_assoc($result)) {
            echo "<div class='request p-2'>";
            echo "Usuario: " . $solicitud['username'];
            echo "<form action='../actions/friend_request_action.php' method='POST' class='d-inline'>
                    <input type='hidden' name='solicitud_id' value='" . $solicitud['solicitud_id'] . "'>
                    <button name='accion' value='aceptada' type='submit' class='btn btn-primary btn-sm mx-1'>Aceptar</button>
                    <button name='accion' value='rechazada' type='submit' class='btn btn-danger btn-sm mx-1'>Rechazar</button>
                    </form>";
            echo "</div>";
        }
        ?>

        <h2>Lista de Amigos</h2>
        <?php
        $query = "SELECT u.id, u.username, 
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

        while ($amigo = mysqli_fetch_assoc($result)) {
            echo "<div class='friend p-2' data-id='" . $amigo['id'] . "'>";
            echo $amigo['username'];
            echo " <span id='unread-" . $amigo['id'] . "' class='badge badge-primary'>";
            if ($amigo['mensajes_sin_leer'] > 0) {
                echo $amigo['mensajes_sin_leer'];
            }
            echo "</span>";
            echo "</div>";
        }
        ?>
        
        <form action="../actions/logout.php" method="POST" class="mt-4">
            <button type="submit" class="btn btn-danger btn-block">Cerrar Sesión</button>
        </form>
    </div>

    <div id="chat-area">
        <div id="chat-header">
            Selecciona un amigo para chatear
        </div>
        <div id="chat-box">
        </div>
        <form id="chat-form">
            <textarea name="mensaje" maxlength="250" required class="form-control" placeholder="Escribe tu mensaje..."></textarea>
            <button type="submit" class="btn btn-success mt-2">Enviar</button>
        </form>
    </div>
    <script src="../js/chat.js"></script>
    <script src="../js/unread_message.js"></script>
</body>
</html>
