<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$docente_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $alumno_id = intval($_POST['alumno_id']);
    $titulo = trim($_POST['titulo']);
    $mensaje = trim($_POST['mensaje']);
    $tipo = trim($_POST['tipo']);
    $destino = isset($_POST['destino']) ? trim($_POST['destino']) : 'individual';

    if (empty($titulo) || empty($mensaje) || empty($tipo)) {
        die("Datos incompletos");
    }

        $stmt = $conn->prepare("
            INSERT INTO notificaciones
            (
                docente_id,
                alumno_id,
                titulo,
                mensaje,
                tipo,
                destino,
                estado,
                fecha
            )
            VALUES
            (?, ?, ?, ?, ?, ?, 'pendiente', NOW())
        ");

    $stmt->bind_param(
        "iissss",
        $docente_id,
        $alumno_id,
        $titulo,
        $mensaje,
        $tipo,
        $destino
    );

    if (!$stmt->execute()) {
        die("ERROR SQL: " . $stmt->error);
    }

    header("Location: ../views/notificaciones_docente.php?ok=1");
    exit();
}
?>