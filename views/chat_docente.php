<?php
session_start();
include("../config/conexion.php");

/* -------------------------
   VALIDAR SESIÓN
--------------------------*/
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'docente') {
    header("Location: ../index.php");
    exit();
}

$docente_id = $_SESSION['user_id'];

/* -------------------------
   ALUMNO SELECCIONADO
--------------------------*/
$alumno_id = isset($_GET['alumno_id']) ? intval($_GET['alumno_id']) : 0;

/* -------------------------
   LISTA DE ALUMNOS
--------------------------*/
$alumnos = $conn->query("
    SELECT id, nombre, apellido 
    FROM usuarios 
    WHERE rol = 'alumna'
");

/* -------------------------
   MENSAJES (SI HAY ALUMNO)
--------------------------*/
$mensajes = null;
$alumno = null;

if ($alumno_id > 0) {

    // datos alumno
    $stmt = $conn->prepare("SELECT nombre, apellido FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $alumno_id);
    $stmt->execute();
    $alumno = $stmt->get_result()->fetch_assoc();

    // chat
    $stmt = $conn->prepare("
        SELECT m.*, u.nombre AS emisor_nombre
        FROM mensajes m
        INNER JOIN usuarios u ON m.emisor_id = u.id
        WHERE (m.emisor_id = ? AND m.receptor_id = ?)
           OR (m.emisor_id = ? AND m.receptor_id = ?)
        ORDER BY m.fecha ASC
    ");

    $stmt->bind_param("iiii", $docente_id, $alumno_id, $alumno_id, $docente_id);
    $stmt->execute();
    $mensajes = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Chat Docente</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:Poppins;
    background:linear-gradient(135deg,#0f172a,#1e3a8a);
    color:white;
}

/* CONTENEDOR */
.container{
    width:90%;
    max-width:1100px;
    margin:40px auto;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns: 1fr 2fr;
    gap:20px;
}

/* LISTA ALUMNOS */
.list{
    background:rgba(255,255,255,0.08);
    padding:15px;
    border-radius:20px;
    height:520px;
    overflow-y:auto;
}

.alumno{
    display:block;
    padding:12px;
    margin-bottom:8px;
    background:rgba(255,255,255,0.1);
    border-radius:12px;
    text-decoration:none;
    color:white;
    transition:0.3s;
}

.alumno:hover{
    background:#3b82f6;
    transform:scale(1.02);
}

/* CHAT */
.chat{
    background:rgba(255,255,255,0.08);
    padding:15px;
    border-radius:20px;
    height:520px;
    display:flex;
    flex-direction:column;
}

/* MENSAJES */
.box{
    flex:1;
    overflow-y:auto;
    padding:10px;
}

.msg{
    max-width:70%;
    padding:10px 12px;
    margin-bottom:10px;
    border-radius:15px;
    font-size:14px;
}

/* FORM */
form{
    margin-top:10px;
}

textarea{
    width:100%;
    height:80px;
    border:none;
    border-radius:10px;
    padding:10px;
    outline:none;
    font-family:Poppins;
}

button{
    width:100%;
    margin-top:8px;
    padding:10px;
    border:none;
    border-radius:10px;
    background:#3b82f6;
    color:white;
    font-weight:600;
    cursor:pointer;
}

button:hover{
    background:#2563eb;
}

/* SCROLL */
.box::-webkit-scrollbar,
.list::-webkit-scrollbar{
    width:6px;
}

.box::-webkit-scrollbar-thumb,
.list::-webkit-scrollbar-thumb{
    background:#3b82f6;
    border-radius:10px;
}
</style>
</head>

<body>

<div class="container">

<div class="grid">

<!-- LISTA ALUMNOS -->
<div class="list">
    <h3>👨‍🎓 Alumnos</h3>

    <?php while($a = $alumnos->fetch_assoc()): ?>
        <a class="alumno" href="chat_docente.php?alumno_id=<?= $a['id'] ?>">
            <?= $a['nombre'] . " " . $a['apellido'] ?>
        </a>
    <?php endwhile; ?>

</div>

<!-- CHAT -->
<div class="chat">

    <h3>
        💬 Chat
        <?php if($alumno): ?>
            con <?= $alumno['nombre'] ?>
        <?php else: ?>
            (selecciona alumno)
        <?php endif; ?>
    </h3>

    <div class="box">

    <?php if($mensajes): ?>

        <?php while($m = $mensajes->fetch_assoc()): ?>

            <div class="msg"
            style="
                background: <?= ($m['emisor_id'] == $docente_id) ? '#3b82f6' : '#fff' ?>;
                color: <?= ($m['emisor_id'] == $docente_id) ? '#fff' : '#000' ?>;
                margin-left: <?= ($m['emisor_id'] == $docente_id) ? 'auto' : '0' ?>;
            ">

                <b><?= $m['emisor_nombre'] ?></b><br>
                <?= $m['mensaje'] ?>

            </div>

        <?php endwhile; ?>

    <?php else: ?>
        <p>Selecciona un alumno para ver el chat</p>
    <?php endif; ?>

    </div>

    <?php if($alumno_id > 0): ?>
    <form method="POST" action="../controllers/enviar_mensaje.php">

        <input type="hidden" name="receptor_id" value="<?= $alumno_id ?>">

        <textarea name="mensaje" placeholder="Responder..." required></textarea>

        <button>Enviar</button>

    </form>
    <?php endif; ?>

</div>

</div>

</div>

</body>
</html>