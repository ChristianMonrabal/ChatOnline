<?php
// Incluir el archivo de configuración de la base de datos
include('../config/db.php');
// Iniciar la sesión para acceder a variables de sesión
session_start();

// Comprobar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validar que el campo de búsqueda no esté vacío y tenga al menos 4 caracteres
    if (!empty(trim($_POST['search'])) && strlen(trim($_POST['search'])) >= 4) {
        // Limpiar la entrada del usuario
        $search = trim($_POST['search']);
        // Obtener el ID del usuario actualmente logueado
        $usuario_id = $_SESSION['usuario_id'];

        // Iniciar transacción
        mysqli_begin_transaction($conn);

        try {
            // Construir la consulta SQL con prepared statement
            $query = "SELECT * FROM Usuarios 
                    WHERE (username LIKE CONCAT('%', ?, '%') OR nombre_real LIKE CONCAT('%', ?, '%'))
                    AND id != ?";
            $stmt = mysqli_prepare($conn, $query);

            // Asignar parámetros y ejecutar la consulta
            mysqli_stmt_bind_param($stmt, 'ssi', $search, $search, $usuario_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Comprobar si se encontraron resultados
            if (mysqli_num_rows($result) > 0) {
                // Iterar sobre los usuarios encontrados
                while ($user = mysqli_fetch_assoc($result)) {
                    // Mostrar cada resultado de búsqueda
                    echo "<div class='search-result'>";
                    echo $user['username'] . " - " . $user['nombre_real']; // Mostrar username y nombre real
                    echo "<form action='../actions/friend_request_action.php' method='POST' class='d-inline'>
                            <input type='hidden' name='amigo_id' value='" . htmlspecialchars($user['id']) . "'> <!-- Campo oculto con el ID del usuario -->
                            <br>
                            <button type='submit' class='btn btn-success btn-sm' style='background-color: #25D366;'>Enviar solicitud de amistad</button> <!-- Botón para enviar solicitud -->
                        </form>";
                    echo "</div>";
                }
            } else {
                // Mensaje si no se encontraron usuarios
                echo "<p style='color: red;'>No se encontraron usuarios.</p>";
            }

            // Confirmar la transacción
            mysqli_commit($conn);
        } catch (Exception $e) {
            // Si ocurre un error, revertir la transacción
            mysqli_rollback($conn);
            echo "<p style='color: red;'>Ocurrió un error durante la búsqueda: " . htmlspecialchars($e->getMessage()) . "</p>";
        } finally {
            // Cerrar el statement
            mysqli_stmt_close($stmt);
        }
    } else {
        // Mensaje si la búsqueda no es válida (menos de 4 caracteres)
        echo "<p style='color: red;'>Introduce al menos 4 caracteres para buscar.</p>";
    }
}
?>
