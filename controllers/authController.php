<?php
session_start();
include("../config/conexion.php");

$correo = $_POST['correo'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($correo) || empty($password)) {
    $_SESSION['error'] = "Todos los campos son obligatorios";
    header("Location: ../index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['usuario'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];

        // REDIRECCIÓN SEGÚN ROL
        if ($_SESSION['rol'] == 'admin') {
            header("Location: ../views/dashboard_admin.php");
        } 
        elseif ($_SESSION['rol'] == 'docente') {
            header("Location: ../views/dashboard_docente.php");
        } 
        else {
            header("Location: ../views/dashboard.php");
        }

        exit();

    } else {
        $_SESSION['error'] = "Contraseña incorrecta";
        header("Location: ../index.php");
        exit();
    }

} else {
    $_SESSION['error'] = "Usuario no encontrado";
    header("Location: ../index.php");
    exit();
}
?>