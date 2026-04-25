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

$user_id = $_SESSION['user_id'];

/* -------------------------
   OBTENER DOCENTE (1 SOLO)
--------------------------*/
$stmt = $conn->prepare("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'docente' LIMIT 1");
$stmt->execute();
$docente = $stmt->get_result()->fetch_assoc();

if (!$docente) {
    die("No hay docente registrado");
}

$docente_id = $docente['id'];

/* -------------------------
   MENSAJES CHAT
--------------------------*/
$sql = $conn->prepare("
    SELECT m.*, u.nombre AS emisor_nombre
    FROM mensajes m
    INNER JOIN usuarios u ON m.emisor_id = u.id
    WHERE (m.emisor_id = ? AND m.receptor_id = ?)
       OR (m.emisor_id = ? AND m.receptor_id = ?)
    ORDER BY m.fecha ASC
");

$sql->bind_param("iiii", $user_id, $docente_id, $docente_id, $user_id);
$sql->execute();
$mensajes = $sql->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Chat Alumna</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:Poppins;
    background:linear-gradient(135deg,#0f172a,#1e3a8a);
    color:white;
}

/* CONTENEDOR GENERAL */
.container{
    width:80%;
    max-width:900px;
    margin:40px auto;
}

/* HEADER */
.header{
    text-align:center;
    margin-bottom:20px;
}

.header h2{
    margin:0;
    font-size:26px;
}

.header p{
    opacity:0.7;
}

/* CHAT BOX */
.chat-box{
    background:rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
    padding:20px;
    border-radius:20px;
    height:450px;
    overflow-y:auto;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

/* MENSAJES */
.msg{
    max-width:70%;
    padding:12px 15px;
    margin-bottom:10px;
    border-radius:15px;
    font-size:14px;
    line-height:1.4;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

/* FORMULARIO */
form{
    margin-top:15px;
}

textarea{
    width:100%;
    height:90px;
    border-radius:12px;
    border:none;
    padding:10px;
    outline:none;
    font-family:Poppins;
}

button{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:none;
    border-radius:12px;
    background:#3b82f6;
    color:white;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#2563eb;
    transform:scale(1.02);
}

/* SCROLL BONITO */
.chat-box::-webkit-scrollbar{
    width:6px;
}

.chat-box::-webkit-scrollbar-thumb{
    background:#3b82f6;
    border-radius:10px;
}
</style>
</head>

<body>

<div class="container">

<div class="header">
    <h2>💬 Chat con Docente</h2>
    <p><?= $docente['nombre'] . " " . $docente['apellido'] ?></p>
</div>

<div class="chat-box">

<?php while($m = $mensajes->fetch_assoc()): ?>

    <div class="msg"
    style="
        background: <?= ($m['emisor_id'] == $user_id) ? '#3b82f6' : '#ffffff' ?>;
        color: <?= ($m['emisor_id'] == $user_id) ? '#ffffff' : '#111' ?>;
        margin-left: <?= ($m['emisor_id'] == $user_id) ? 'auto' : '0' ?>;
    ">

        <b><?= $m['emisor_nombre'] ?></b><br>
        <?= $m['mensaje'] ?>

    </div>

<?php endwhile; ?>

</div>

<!-- ENVIAR MENSAJE -->
<form method="POST" action="../controllers/enviar_mensaje.php">

    <input type="hidden" name="receptor_id" value="<?= $docente_id ?>">

    <textarea name="mensaje" placeholder="Escribe tu mensaje..." required></textarea>

    <button>Enviar mensaje</button>

</form>

</div>

</body>
</html>