<?php
session_start();
include("../config/conexion.php");

// SOLO ADMIN
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// VALIDAR POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/lista_cursos_admin.php");
    exit();
}

// RECIBIR DATOS
$id = $_POST['id'];
$nombre = $_POST['nombre_curso'];
$descripcion = $_POST['descripcion'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];
$estado = $_POST['estado'];

// VALIDACIÓN BÁSICA
if (empty($id) || empty($nombre) || empty($descripcion) || empty($fecha_inicio) || empty($fecha_fin)) {
    $_SESSION['error'] = "Todos los campos son obligatorios";
    header("Location: ../views/lista_cursos_admin.php");
    exit();
}

// VALIDAR FECHAS
if ($fecha_fin < $fecha_inicio) {
    $_SESSION['error'] = "La fecha final no puede ser menor que la inicial";
    header("Location: ../views/lista_cursos_admin.php");
    exit();
}

// ACTUALIZAR CURSO
$stmt = $conn->prepare("
    UPDATE cursos 
    SET nombre_curso = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?, estado = ?
    WHERE id = ?
");

$stmt->bind_param("sssssi", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado, $id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Curso actualizado correctamente";
} else {
    $_SESSION['error'] = "Error al actualizar curso";
}

header("Location: ../views/lista_cursos_admin.php");
exit();
?>