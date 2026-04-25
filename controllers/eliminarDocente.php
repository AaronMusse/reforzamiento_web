<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../views/lista_docentes.php");
    exit();
}

$id = $_GET['id'];

// Evitar eliminar administradores por seguridad
$stmtCheck = $conn->prepare("SELECT rol FROM usuarios WHERE id = ?");
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Docente no encontrado";
    header("Location: ../views/lista_docentes.php");
    exit();
}

$user = $result->fetch_assoc();

if ($user['rol'] == 'admin') {
    $_SESSION['error'] = "No se puede eliminar un administrador";
    header("Location: ../views/lista_docentes.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ? AND rol = 'docente'");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Docente eliminado correctamente";
} else {
    $_SESSION['error'] = "Error al eliminar docente";
}

header("Location: ../views/lista_docentes.php");
exit();
?>