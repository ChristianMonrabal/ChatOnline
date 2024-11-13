<?php
// Incluye el archivo de configuración para la conexión a la base de datos
include('../config/db.php');

// Inicia la sesión para gestionar la información del usuario
session_start();

// Verifica si el usuario está logueado; si no, lo redirige a la página de inicio de sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtiene el ID del usuario de la sesión actual
$usuario_id = $_SESSION['usuario_id'];

// Realiza una consulta para obtener el nombre de usuario del ID en la sesión
$query_usuario = "SELECT username FROM Usuarios WHERE id = '$usuario_id'";
$result_usuario = mysqli_query($conn, $query_usuario);
$usuario = mysqli_fetch_assoc($result_usuario);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración básica del encabezado HTML -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatPro</title>
    
    <!-- Enlaces a hojas de estilo y scripts externos -->
    <link rel="stylesheet" href="../css/index.css">
    <link rel="shortcut icon" href="../img/icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div id="sidebar">
        <!-- Muestra el nombre de usuario del usuario logueado -->
        <div id="user-info" class="text-center mb-4">
            <h4><?php echo $usuario['username']; ?></h4>
        </div>
        
        <!-- Formulario de búsqueda de usuarios -->
        <h2>Buscar Usuarios</h2>
        <form id="search-form">
            <input type="text" name="search" placeholder="Buscar usuarios" class="form-control">
            <button type="submit" class="btn btn-success mt-2">Buscar</button>
        </form>
        <div id="search-results"></div>
        
        <!-- Muestra cualquier mensaje de error almacenado en la sesión -->
        <?php
        if (isset($_SESSION['error_mensaje'])) {
            echo "<span style='color: red;'>" . htmlspecialchars($_SESSION['error_mensaje']) . "</span>";
            unset($_SESSION['error_mensaje']);
        }
        ?>

        <!-- Lista de solicitudes de amistad pendientes -->
        <h2>Solicitudes de Amistad Pendientes</h2>
        <?php
        // Consulta para obtener las solicitudes de amistad pendientes para el usuario actual
        $query = "SELECT DISTINCT a.id AS solicitud_id, u.username 
                    FROM Amistades a 
                    JOIN Usuarios u ON a.usuario_id = u.id 
                    WHERE a.amigo_id = '$usuario_id' AND a.estado = 'pendiente'";
        $result = mysqli_query($conn, $query);

        // Muestra cada solicitud pendiente con opciones para aceptar o rechazar
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

        <!-- Lista de amigos del usuario con mensajes sin leer -->
        <h2>Lista de Amigos</h2>
        <?php
        // Consulta para obtener la lista de amigos y contar los mensajes sin leer
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

        // Muestra cada amigo con la cantidad de mensajes sin leer y opción para eliminar
        while ($amigo = mysqli_fetch_assoc($result)) {
            echo "<div class='friend p-2 d-flex justify-content-between align-items-center' data-id='" . $amigo['id'] . "'>";
            echo "<div>";
            echo htmlspecialchars($amigo['username']);
            echo " <span id='unread-" . $amigo['id'] . "' class='badge badge-primary'>";
            if ($amigo['mensajes_sin_leer'] > 0) {
                echo $amigo['mensajes_sin_leer'];
            }
            echo "</span>";
            echo "</div>";
            echo "<div class='dropdown'>";
            echo "<button class='btn btn-secondary btn-sm dropdown-toggle' type='button' id='dropdownMenuButton-" . $amigo['id'] . "' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>";
            echo "</button>";
            echo "<div class='dropdown-menu' aria-labelledby='dropdownMenuButton-" . $amigo['id'] . "'>";
            echo "<a class='dropdown-item' href='../actions/delete_friend.php?id=" . $amigo['id'] . "'>Borrar amigo</a>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        ?>
        
        <!-- Botón de cierre de sesión -->
        <form action="../actions/logout.php" method="POST" class="mt-4">
            <button type="submit" class="btn btn-danger btn-block">Cerrar sesión</button>
        </form>
    </div>

    <!-- Área de chat para mostrar mensajes y enviar mensajes a amigos -->
    <div id="chat-area">
        <div id="chat-header">
            Selecciona un amigo para chatear
        </div>
        <div id="chat-box">
        </div>
        
        <!-- Formulario para escribir y enviar mensajes en el chat -->
        <form id="chat-form">
            <textarea name="mensaje" maxlength="250" required class="form-control" placeholder="Escribe tu mensaje..."></textarea>
            <button type="submit" class="btn btn-success mt-2">Enviar</button>
        </form>
    </div>
    
    <!-- Scripts para manejar la funcionalidad del chat y los mensajes sin leer -->
    <script src="../js/chat.js"></script>
    <script src="../js/unread_message.js"></script>
</body>
</html>
