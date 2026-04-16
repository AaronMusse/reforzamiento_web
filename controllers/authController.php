<?php
include("../config/conexion.php");

$correo = $_POST['correo'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE correo='$correo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['usuario'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];

        header("Location: ../views/dashboard.php");
    } else {
        echo "Contraseña incorrecta";
    }
} else {
    echo "Usuario no encontrado";
}
?>