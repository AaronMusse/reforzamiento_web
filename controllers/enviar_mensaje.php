<?php
session_start();
include("../config/conexion.php");

/* -------------------------
   VALIDAR SESIÓN
--------------------------*/
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

/* -------------------------
   VALIDAR DATOS
--------------------------*/
if (!isset($_POST['receptor_id']) || !isset($_POST['mensaje'])) {
    die("Datos incompletos");
}

$emisor_id = $_SESSION['user_id'];
$receptor_id = intval($_POST['receptor_id']);
$mensaje = trim($_POST['mensaje']);

if ($mensaje == "") {
    die("Mensaje vacío");
}

/* -------------------------
   INSERTAR MENSAJE
--------------------------*/
$stmt = $conn->prepare("
    INSERT INTO mensajes (emisor_id, receptor_id, mensaje)
    VALUES (?, ?, ?)
");

$stmt->bind_param("iis", $emisor_id, $receptor_id, $mensaje);

if ($stmt->execute()) {

    /* volver a la página anterior */
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

} else {
    echo "Error al enviar mensaje";
}