<?php
session_start();

include 'conexion.php';
include '../php/validation_form.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['signup_username'];
    $email = $_SESSION['signup_email'];
    $nombre_real = $_SESSION['nombre_real'];
    $hashed_password = password_hash($_POST['signup_password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (username, nombre_real, email, pwd) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $nombre_real, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
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
