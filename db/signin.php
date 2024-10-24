<?php
session_start();

include 'conexion.php';
include '../php/validation_form.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['signin_email'] = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['signin_email']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['pwd'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nombre_real'] = $user['nombre_real'];
            $_SESSION['email'] = $user['email'];
            header("Location: ../index.php");
            exit();
        }
    }

    $_SESSION['error'] = "El email o la contraseña son incorrectos.";
    $_SESSION['section'] = 'signin';
    header("Location: ../views/form.php?section=signin");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
