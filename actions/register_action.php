<?php
// Inicia la sesión para almacenar y recuperar datos de sesión
session_start();

// Incluye el archivo de configuración para la conexión a la base de datos
include '../config/db.php';

// Comprueba si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Asigna y valida cada campo del formulario, aplicando saneamiento para prevenir inyecciones de código
    $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
    $nombre_real = isset($_POST['nombre_real']) ? htmlspecialchars(trim($_POST['nombre_real'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Validación del nombre de usuario
    if (empty($username)) {
        $_SESSION['error'] = "El nombre de usuario es obligatorio.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    } elseif (strlen($username) < 3 || !preg_match('/^[A-Z]/', $username)) {
        $_SESSION['error'] = "El nombre de usuario debe tener al menos 3 caracteres y comenzar con una letra mayúscula.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }
    $_SESSION['signup_username'] = $username; // Guarda el nombre de usuario en la sesión para reutilizar en caso de error

    // Validación del nombre real
    if (empty($nombre_real)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }
    $_SESSION['nombre_real'] = $nombre_real;

    // Validación del correo electrónico
    if (empty($email)) {
        $_SESSION['error'] = "El correo electrónico es obligatorio.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Formato de correo electrónico inválido.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }
    $_SESSION['signup_email'] = $email;

    // Validación de la contraseña
    if (empty($password)) {
        $_SESSION['error'] = "La contraseña es obligatoria.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $_SESSION['error'] = "La contraseña debe contener al menos una letra mayúscula.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    } elseif (!preg_match('/[0-9]/', $password)) {
        $_SESSION['error'] = "La contraseña debe contener al menos un número.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }

    // Validación de la confirmación de la contraseña
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }

    // Encripta la contraseña para almacenarla de forma segura
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Inicia una transacción
    mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);

    try {
        // Verificación de unicidad del nombre de usuario
        $sql_username_check = "SELECT id FROM Usuarios WHERE username = ?";
        $stmt_username = mysqli_prepare($conn, $sql_username_check);
        mysqli_stmt_bind_param($stmt_username, "s", $username);
        mysqli_stmt_execute($stmt_username);
        mysqli_stmt_store_result($stmt_username);

        if (mysqli_stmt_num_rows($stmt_username) > 0) {
            throw new Exception("El nombre de usuario ya existe.");
        }
        mysqli_stmt_close($stmt_username);

        // Verificación de unicidad del correo electrónico
        $sql_email_check = "SELECT id FROM Usuarios WHERE email = ?";
        $stmt_email = mysqli_prepare($conn, $sql_email_check);
        mysqli_stmt_bind_param($stmt_email, "s", $email);
        mysqli_stmt_execute($stmt_email);
        mysqli_stmt_store_result($stmt_email);

        if (mysqli_stmt_num_rows($stmt_email) > 0) {
            throw new Exception("El correo electrónico ya existe.");
        }
        mysqli_stmt_close($stmt_email);

        // Inserta el nuevo usuario en la base de datos
        $sql = "INSERT INTO Usuarios (username, nombre_real, email, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $username, $nombre_real, $email, $hashed_password);

        // Ejecuta la inserción
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al registrar el usuario: " . mysqli_error($conn));
        }

        // Confirma la transacción
        mysqli_commit($conn);

        // Redirige al usuario después de un registro exitoso
        $usuario_id = mysqli_insert_id($conn);
        $_SESSION['loggedin'] = true;
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['username'] = $username;
        header("Location: ../index.php");
        exit();
    } catch (Exception $e) {
        // Revierte la transacción en caso de error
        mysqli_rollback($conn);

        // Almacena el error en la sesión y redirige
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    } finally {
        if (isset($stmt)) {
            mysqli_stmt_close($stmt);
        }
    }
}

// Cierra la conexión a la base de datos
mysqli_close($conn);
?>
