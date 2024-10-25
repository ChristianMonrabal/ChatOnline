<?php
session_start();

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cambia 'username' por 'email' para recoger el correo electrónico
    $signin_email = isset($_POST['email']) ? trim($_POST['email']) : '';
    if (empty($signin_email)) {
        $_SESSION['error'] = "El correo electrónico es obligatorio.";
        $_SESSION['section'] = 'signin';
        header("Location: ../public/login.php?section=signin");
        exit();
    } else {
        $_SESSION['signin_email'] = htmlspecialchars($signin_email);
    }

    $signin_password = isset($_POST['password']) ? $_POST['password'] : '';
    if (empty($signin_password)) {
        $_SESSION['error'] = "La contraseña es obligatoria.";
        $_SESSION['section'] = 'signin';
        header("Location: ../public/login.php?section=signin");
        exit();
    }

    $query = "SELECT * FROM Usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $signin_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($signin_password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nombre_real'] = $user['nombre_real'];
            $_SESSION['email'] = $user['email'];
            header("Location: ../index.php");
            exit();
        }
    }

    $_SESSION['error'] = "El correo electrónico o la contraseña son incorrectos.";
    $_SESSION['section'] = 'signin';
    header("Location: ../public/login.php?section=signin");
    exit();
}

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>
