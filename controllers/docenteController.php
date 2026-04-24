<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/crear_docente.php");
    exit();
}

$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$dni = $_POST['dni'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$correo = $_POST['correo'] ?? '';
$password = $_POST['password'] ?? '';

$rol = 'docente';

if (
    empty($nombre) ||
    empty($apellido) ||
    empty($dni) ||
    empty($fecha_nacimiento) ||
    empty($correo) ||
    empty($password)
) {
    $_SESSION['error'] = "Todos los campos son obligatorios";
    header("Location: ../views/crear_docente.php");
    exit();
}

if (!preg_match('/^[0-9]{8}$/', $dni)) {
    $_SESSION['error'] = "El DNI debe tener 8 dígitos";
    header("Location: ../views/crear_docente.php");
    exit();
}

$stmtCheck = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmtCheck->bind_param("s", $correo);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = "Ese correo ya está registrado";
    header("Location: ../views/crear_docente.php");
    exit();
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO usuarios 
    (nombre, apellido, dni, fecha_nacimiento, correo, password, rol)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssss",
    $nombre,
    $apellido,
    $dni,
    $fecha_nacimiento,
    $correo,
    $passwordHash,
    $rol
);

if ($stmt->execute()) {
    $_SESSION['success'] = "Docente creado correctamente";
} else {
    $_SESSION['error'] = "Error al crear docente";
}

header("Location: ../views/crear_docente.php");
exit();
?>