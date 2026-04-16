<?php
include("../config/conexion.php");

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$password = $_POST['password'];
$rol = $_POST['rol'];

// Encriptar contraseña (MUY IMPORTANTE)
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Verificar si ya existe el usuario
$sqlCheck = "SELECT * FROM usuarios WHERE correo='$correo'";
$result = $conn->query($sqlCheck);

if ($result->num_rows > 0) {
    echo "El correo ya está registrado";
} else {
    $sql = "INSERT INTO usuarios (nombre, correo, password, rol) 
            VALUES ('$nombre', '$correo', '$passwordHash', '$rol')";

    if ($conn->query($sql) === TRUE) {
        echo "Registro exitoso";
        header("Location: ../views/login.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>