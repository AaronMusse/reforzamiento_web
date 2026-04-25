<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_SESSION['user_id'];

// DATOS
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$correo = $_POST['correo'] ?? ''; // solo si decides permitirlo
$actual = $_POST['password_actual'] ?? '';
$nueva = $_POST['password_nueva'] ?? '';
$repetir = $_POST['password_repetir'] ?? '';

// OBTENER USUARIO
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// VALIDAR CAMPOS BÁSICOS
if (empty($nombre) || empty($apellido)) {
    $_SESSION['error'] = "Nombre y apellido son obligatorios";
    header("Location: ../views/perfil.php");
    exit();
}

// VALIDAR CONTRASEÑA ACTUAL
if (!password_verify($actual, $user['password'])) {
    $_SESSION['error'] = "La contraseña actual es incorrecta";
    header("Location: ../views/perfil.php");
    exit();
}

// VALIDAR NUEVA CONTRASEÑA
if ($nueva !== $repetir) {
    $_SESSION['error'] = "Las nuevas contraseñas no coinciden";
    header("Location: ../views/perfil.php");
    exit();
}

// ENCRIPTAR NUEVA
$hash = password_hash($nueva, PASSWORD_DEFAULT);

// FOTO
$foto = $user['foto'];

if (!empty($_FILES['foto']['name'])) {

    $dir = "../assets/img/usuarios/";

    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nombreFoto = uniqid() . "." . $ext;

    move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $nombreFoto);

    $foto = $nombreFoto;
}

// UPDATE FINAL
$stmt = $conn->prepare("
UPDATE usuarios 
SET nombre=?, apellido=?, password=?, foto=? 
WHERE id=?
");

$stmt->bind_param("ssssi", 
    $nombre, 
    $apellido, 
    $hash, 
    $foto, 
    $id
);

if ($stmt->execute()) {
    $_SESSION['success'] = "Perfil actualizado correctamente";
} else {
    $_SESSION['error'] = "Error al actualizar perfil";
}

header("Location: ../views/perfil.php");
exit();
?>