<?php
// Inicia la sesión para utilizar variables de sesión
session_start();

// Incluye el archivo de configuración de la base de datos
include '../config/db.php';

// Verifica que la solicitud sea de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene y valida el correo electrónico del formulario
    $signin_email = isset($_POST['email']) ? trim($_POST['email']) : '';
    if (empty($signin_email)) {
        // Si el correo está vacío, guarda un mensaje de error en la sesión y redirige al formulario
        $_SESSION['error'] = "El correo electrónico es obligatorio.";
        $_SESSION['section'] = 'signin';
        header("Location: ../public/login.php?section=signin");
        exit();
    } else {
        // Guarda el correo en la sesión para mantenerlo en el campo del formulario si hay error
        $_SESSION['signin_email'] = htmlspecialchars($signin_email);
    }

    // Obtiene y valida la contraseña del formulario
    $signin_password = isset($_POST['password']) ? $_POST['password'] : '';
    if (empty($signin_password)) {
        // Si la contraseña está vacía, guarda un mensaje de error en la sesión y redirige
        $_SESSION['error'] = "La contraseña es obligatoria.";
        $_SESSION['section'] = 'signin';
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
            // Guarda datos del usuario en la sesión y redirige al inicio
            $_SESSION['loggedin'] = true;
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nombre_real'] = $user['nombre_real'];
            $_SESSION['email'] = $user['email'];
            header("Location: ../index.php");
            exit();
        }
    }

    // Si el correo o la contraseña no son correctos, guarda un mensaje de error y redirige
    $_SESSION['error'] = "El correo electrónico o la contraseña son incorrectos.";
    $_SESSION['section'] = 'signin';
    header("Location: ../public/login.php?section=signin");
    exit();
}

// Cierra la declaración preparada y la conexión si están abiertas
if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>
