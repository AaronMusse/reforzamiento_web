<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../views/notificaciones.php");
    exit();
}

$notificacion_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

/*
========================================
ELIMINAR NOTIFICACIÓN
(Solo la propia del alumno)
========================================
*/

$stmt = $conn->prepare("
    DELETE FROM notificaciones
    WHERE id = ?
    AND alumno_id = ?
");

$stmt->bind_param("ii", $notificacion_id, $user_id);
$stmt->execute();

/*
========================================
REDIRECCIÓN
========================================
*/

header("Location: ../views/notificaciones.php");
exit();
?>