<?php
session_start();
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
    $_SESSION['error'] = "No se enviaron datos desde el formulario";
    header("Location: ../views/registro.php");
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
    $_SESSION['error'] = "Todos los campos son obligatorios";
    header("Location: ../views/registro.php");
    exit();
}

// Validación de DNI (8 dígitos)
if (!preg_match('/^[0-9]{8}$/', $dni)) {
    $_SESSION['error'] = "El DNI debe tener exactamente 8 dígitos numéricos";
    header("Location: ../views/registro.php");
    exit();
}

// Fecha no futura
$hoy = date("Y-m-d");
if ($fecha_nacimiento > $hoy) {
    $_SESSION['error'] = "La fecha de nacimiento no puede ser futura";
    header("Location: ../views/registro.php");
    exit();
}

// Edad mínima
$edad = date_diff(date_create($fecha_nacimiento), date_create('today'))->y;
if ($edad < 10) {
    $_SESSION['error'] = "Debe tener al menos 10 años";
    header("Location: ../views/registro.php");
    exit();
}

// Correos iguales
if ($correo !== $repetir_correo) {
    $_SESSION['error'] = "Los correos no coinciden";
    header("Location: ../views/registro.php");
    exit();
}

// Contraseñas iguales
if ($password !== $repetir_password) {
    $_SESSION['error'] = "Las contraseñas no coinciden";
    header("Location: ../views/registro.php");
    exit();
}

// Seguridad contraseña
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
    $_SESSION['error'] = "La contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un símbolo";
    header("Location: ../views/registro.php");
    exit();
}

// ENCRIPTAR
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// VERIFICAR SI EXISTE CORREO
$stmtCheck = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmtCheck->bind_param("s", $correo);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = "El correo ya está registrado";
    header("Location: ../views/registro.php");
    exit();
}

// INSERTAR
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, dni, fecha_nacimiento, correo, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $nombre, $apellido, $dni, $fecha_nacimiento, $correo, $passwordHash, $rol);

if ($stmt->execute()) {
    $_SESSION['success'] = "Cuenta creada exitosamente. Ya puedes iniciar sesión.";
    header("Location: ../index.php");
    exit();
} else {
    $_SESSION['error'] = "Error al registrar: " . $conn->error;
    header("Location: ../views/registro.php");
    exit();
}
?>
