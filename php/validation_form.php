<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['section'] = isset($_POST['signup_username']) ? 'signup' : 'signin';

    switch ($_SESSION['section']) {
        case 'signup':
            if (empty(trim($_POST['signup_username']))) {
                $_SESSION['error'] = "El nombre de usuario es obligatorio.";
                header("Location: ../views/form.php?section=signup");
                exit();
            } else {
                $username = htmlspecialchars(trim($_POST['signup_username']));
                $_SESSION['signup_username'] = $username;
            }

            if (empty(trim($_POST['signup_email']))) {
                $_SESSION['error'] = "El correo electrónico es obligatorio.";
                header("Location: ../views/form.php?section=signup");
                exit();
            } elseif (!filter_var(trim($_POST['signup_email']), FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Formato de correo electrónico inválido.";
                header("Location: ../views/form.php?section=signup");
                exit();
            } else {
                $email = htmlspecialchars(trim($_POST['signup_email']));
                $_SESSION['signup_email'] = $email;
            }

            if (empty(trim($_POST['signup_password']))) {
                $_SESSION['error'] = "La contraseña es obligatoria.";
                header("Location: ../views/form.php?section=signup");
                exit();
            } elseif (strlen(trim($_POST['signup_password'])) < 6) {
                $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres.";
                header("Location: ../views/form.php?section=signup");
                exit();
            } elseif (!preg_match('/[A-Z]/', $_POST['signup_password'])) {
                $_SESSION['error'] = "La contraseña debe contener al menos una letra mayúscula.";
                header("Location: ../views/form.php?section=signup");
                exit();
            } elseif (!preg_match('/[0-9]/', $_POST['signup_password'])) {
                $_SESSION['error'] = "La contraseña debe contener al menos un número.";
                header("Location: ../views/form.php?section=signup");
                exit();
            }

            if (empty(trim($_POST['signup_confirm_password']))) {
                $_SESSION['error'] = "La confirmación de contraseña es obligatoria.";
                header("Location: ../views/form.php?section=signup");
                exit();
            } elseif ($_POST['signup_password'] !== trim($_POST['signup_confirm_password'])) {
                $_SESSION['error'] = "Las contraseñas no coinciden.";
                header("Location: ../views/form.php?section=signup");
                exit();
            }

            include 'conexion.php';

            $sql_username_check = "SELECT id FROM usuarios WHERE username = ?";
            $stmt_username = mysqli_prepare($conn, $sql_username_check);
            mysqli_stmt_bind_param($stmt_username, "s", $username);
            mysqli_stmt_execute($stmt_username);
            mysqli_stmt_store_result($stmt_username);

            if (mysqli_stmt_num_rows($stmt_username) > 0) {
                $_SESSION['error'] = "El nombre de usuario ya existe.";
                header("Location: ../views/form.php?section=signup");
                exit();
            }
            mysqli_stmt_close($stmt_username);

            $sql_email_check = "SELECT id FROM usuarios WHERE email = ?";
            $stmt_email = mysqli_prepare($conn, $sql_email_check);
            mysqli_stmt_bind_param($stmt_email, "s", $email);
            mysqli_stmt_execute($stmt_email);
            mysqli_stmt_store_result($stmt_email);

            if (mysqli_stmt_num_rows($stmt_email) > 0) {
                $_SESSION['error'] = "El correo electrónico ya existe.";
                header("Location: ../views/form.php?section=signup");
                exit();
            }
            mysqli_stmt_close($stmt_email);
            break;

        case 'signin':
            if (empty(trim($_POST['email']))) {
                $_SESSION['error'] = "El correo electrónico es obligatorio.";
                header("Location: ../views/form.php?section=signin");
                exit();
            } else {
                $_SESSION['signin_email'] = htmlspecialchars(trim($_POST['email']));
            }

            if (empty(trim($_POST['password']))) {
                $_SESSION['error'] = "La contraseña es obligatoria.";
                header("Location: ../views/form.php?section=signin");
                exit();
            }
            break;
    }
}

?>
