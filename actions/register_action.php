<?php
session_start();

include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
    $nombre_real = isset($_POST['nombre_real']) ? htmlspecialchars(trim($_POST['nombre_real'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

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
    $_SESSION['signup_username'] = $username;

    if (empty($nombre_real)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }
    $_SESSION['nombre_real'] = $nombre_real;

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

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql_username_check = "SELECT id FROM Usuarios WHERE username = ?";
    $stmt_username = mysqli_prepare($conn, $sql_username_check);
    mysqli_stmt_bind_param($stmt_username, "s", $username);
    mysqli_stmt_execute($stmt_username);
    mysqli_stmt_store_result($stmt_username);

    if (mysqli_stmt_num_rows($stmt_username) > 0) {
        $_SESSION['error'] = "El nombre de usuario ya existe.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }
    mysqli_stmt_close($stmt_username);

    $sql_email_check = "SELECT id FROM Usuarios WHERE email = ?";
    $stmt_email = mysqli_prepare($conn, $sql_email_check);
    mysqli_stmt_bind_param($stmt_email, "s", $email);
    mysqli_stmt_execute($stmt_email);
    mysqli_stmt_store_result($stmt_email);

    if (mysqli_stmt_num_rows($stmt_email) > 0) {
        $_SESSION['error'] = "El correo electrónico ya existe.";
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }
    mysqli_stmt_close($stmt_email);

    $sql = "INSERT INTO Usuarios (username, nombre_real, email, password) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $nombre_real, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        $usuario_id = mysqli_insert_id($conn);
        $_SESSION['loggedin'] = true;
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['username'] = $username;
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
        $_SESSION['section'] = 'signup';
        header("Location: ../public/login.php?section=signup");
        exit();
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
