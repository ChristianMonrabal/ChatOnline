<?php
// Incluir el archivo de configuración de la base de datos
include('../config/db.php');
// Iniciar la sesión para acceder a variables de sesión
session_start();

// Comprobar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que el campo de búsqueda no esté vacío y tenga al menos 4 caracteres
    if (!empty(trim($_POST['search'])) && strlen(trim($_POST['search'])) >= 4) {
        // Limpiar la entrada del usuario para evitar inyecciones SQL
        $search = mysqli_real_escape_string($conn, trim($_POST['search']));
        // Obtener el ID del usuario actualmente logueado
        $usuario_id = $_SESSION['usuario_id'];

        // Construir la consulta SQL para buscar usuarios
        $query = "SELECT * FROM Usuarios 
                WHERE (username LIKE '%$search%' OR nombre_real LIKE '%$search%')
                AND id != '$usuario_id'";
        
        // Ejecutar la consulta SQL
        $result = mysqli_query($conn, $query);

        // Comprobar si se encontraron resultados
        if (mysqli_num_rows($result) > 0) {
            // Iterar sobre los usuarios encontrados
            while ($user = mysqli_fetch_assoc($result)) {
                // Mostrar cada resultado de búsqueda
                echo "<div class='search-result'>";
                echo $user['username'] . " - " . $user['nombre_real']; // Mostrar username y nombre real
                echo "<form action='../actions/friend_request_action.php' method='POST' class='d-inline'>
                        <input type='hidden' name='amigo_id' value='" . $user['id'] . "'> <!-- Campo oculto con el ID del usuario -->
                        <br>
                        <button type='submit' class='btn btn-success btn-sm' style='background-color: #25D366;'>Enviar solicitud de amistad</button> <!-- Botón para enviar solicitud -->
                    </form>";
                echo "</div>";
            }
        } else {
            // Mensaje si no se encontraron usuarios
            echo "<p style='color: red;'>No se encontraron usuarios.</p>";
        }
    } else {
        // Mensaje si la búsqueda no es válida (menos de 4 caracteres)
        echo "<p style='color: red;'>Introduce al menos 4 caracteres para buscar.</p>";
    }
}
?>
