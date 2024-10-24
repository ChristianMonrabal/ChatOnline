<?php
session_start();

include 'conexion.php';
include '../php/validation_form.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['signup_username'];
    $email = $_SESSION['signup_email'];
    $hashed_password = password_hash($_POST['signup_password'], PASSWORD_BCRYPT);
    $nombre_real = $_SESSION['nombre_real'];

    $sql = "INSERT INTO usuarios (username, email, pwd, nombre_real) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $nombre_real);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        unset($_SESSION['signup_username']);
        unset($_SESSION['signup_email']);
        unset($_SESSION['nombre_real']);
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
        header("Location: ../views/form.php?section=signup");
        exit();
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
