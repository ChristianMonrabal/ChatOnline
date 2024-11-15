<?php
// Inicia la sesión para utilizar variables de sesión
session_start();

// Incluye el archivo de configuración de la base de datos
include '../config/db.php';

// Verifica que la solicitud sea de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Inicia una transacción
    mysqli_begin_transaction($conn);

    try {
        // Obtiene y valida el correo electrónico del formulario
        $signin_email = isset($_POST['email']) ? trim($_POST['email']) : '';
        if (empty($signin_email)) {
            $_SESSION['error'] = "El correo electrónico es obligatorio.";
            $_SESSION['section'] = 'signin';
            mysqli_rollback($conn); // Reversión por posible error
            header("Location: ../public/login.php?section=signin");
            exit();
        } else {
            $_SESSION['signin_email'] = htmlspecialchars($signin_email);
        }

        // Obtiene y valida la contraseña del formulario
        $signin_password = isset($_POST['password']) ? $_POST['password'] : '';
        if (empty($signin_password)) {
            $_SESSION['error'] = "La contraseña es obligatoria.";
            $_SESSION['section'] = 'signin';
            mysqli_rollback($conn);
            header("Location: ../public/login.php?section=signin");
            exit();
        }

        // Prepara la consulta para obtener el usuario con el correo proporcionado
        $query = "SELECT * FROM Usuarios WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $signin_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Verifica si el usuario existe y, de ser así, valida la contraseña
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verifica si la contraseña coincide usando `password_verify`
            if (password_verify($signin_password, $user['password'])) {
                // Guarda datos del usuario en la sesión
                $_SESSION['loggedin'] = true;
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nombre_real'] = $user['nombre_real'];
                $_SESSION['email'] = $user['email'];

                // Confirma la transacción
                mysqli_commit($conn);
                header("Location: ../index.php");
                exit();
            }
        }

        // Si el correo o la contraseña no son correctos
        $_SESSION['error'] = "El correo electrónico o la contraseña son incorrectos.";
        $_SESSION['section'] = 'signin';
        mysqli_rollback($conn); // Reversión de la transacción
        header("Location: ../public/login.php?section=signin");
        exit();
    } catch (Exception $e) {
        // Si ocurre algún error, se revierte la transacción
        mysqli_rollback($conn);
        $_SESSION['error'] = "Ocurrió un error inesperado.";
        header("Location: ../public/login.php?section=signin");
        exit();
    }
}

// Cierra la declaración preparada y la conexión si están abiertas
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>
