<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../config/conexion.php");

// PROTEGER ACCESO DIRECTO (solo POST)
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../index.php");
    exit();
}

// VERIFICAR QUE LLEGUE INFORMACIÓN
if (empty($_POST)) {
    echo "No se enviaron datos desde el formulario";
    exit();
}

// RECIBIR DATOS
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$dni = $_POST['dni'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

$correo = $_POST['correo'] ?? '';
$repetir_correo = $_POST['repetir_correo'] ?? '';

$password = $_POST['password'] ?? '';
$repetir_password = $_POST['repetir_password'] ?? '';

$rol = $_POST['rol'] ?? '';

// VALIDACIONES 🔥

// Campos obligatorios
if (
    empty($nombre) || empty($apellido) || empty($dni) || empty($fecha_nacimiento) ||
    empty($correo) || empty($repetir_correo) || empty($password) || empty($repetir_password) || empty($rol)
) {
    echo "Todos los campos son obligatorios";
    exit();
}

// Fecha no futura
$hoy = date("Y-m-d");
if ($fecha_nacimiento > $hoy) {
    echo "La fecha de nacimiento no puede ser futura";
    exit();
}

// Edad mínima
$edad = date_diff(date_create($fecha_nacimiento), date_create('today'))->y;
if ($edad < 10) {
    echo "Debe tener al menos 10 años";
    exit();
}

// Correos iguales
if ($correo !== $repetir_correo) {
    echo "Los correos no coinciden";
    exit();
}

// Contraseñas iguales
if ($password !== $repetir_password) {
    echo "Las contraseñas no coinciden";
    exit();
}

// Seguridad contraseña
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
    echo "La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un símbolo";
    exit();
}

// ENCRIPTAR
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// VERIFICAR SI EXISTE CORREO
$sqlCheck = "SELECT * FROM usuarios WHERE correo='$correo'";
$result = $conn->query($sqlCheck);

if ($result->num_rows > 0) {
    echo "El correo ya está registrado";
    exit();
}

// INSERTAR
$sql = "INSERT INTO usuarios 
(nombre, apellido, dni, fecha_nacimiento, correo, password, rol)
VALUES 
('$nombre', '$apellido', '$dni', '$fecha_nacimiento', '$correo', '$passwordHash', '$rol')";

if ($conn->query($sql) === TRUE) {
    header("Location: ../index.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>