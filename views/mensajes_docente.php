<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$docente_id = $_SESSION['user_id'];

/* ===============================
   ALUMNOS (solo los que ya hablaron)
================================= */
$stmt = $conn->prepare("
    SELECT DISTINCT u.id, u.nombre, u.apellido, u.foto
    FROM mensajes m
    INNER JOIN usuarios u ON u.id = m.emisor_id
    WHERE (m.receptor_id = ? OR m.emisor_id = ?)
    AND u.rol = 'alumna'
");

$stmt->bind_param("ii", $docente_id, $docente_id);
$stmt->execute();
$alumnos = $stmt->get_result();

/* ===============================
   ALUMNA SELECCIONADA
================================= */
$alumno_id = isset($_GET['alumno_id']) ? intval($_GET['alumno_id']) : 0;

$mensajes = null;
$alumno = null;

if ($alumno_id > 0) {

    /* DATOS DE LA ALUMNA */
    $a = $conn->prepare("
        SELECT nombre, apellido, foto
        FROM usuarios
        WHERE id = ?
    ");
    $a->bind_param("i", $alumno_id);
    $a->execute();
    $alumno = $a->get_result()->fetch_assoc();

    /* MENSAJES */
    $stmt = $conn->prepare("
        SELECT m.*, u.nombre
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
<title>Mensajes Docente</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

/* FONDO */

body{
    background:
        linear-gradient(135deg, #0f172a, #1e3a8a),
        url('../img/fondo-educativo.png');
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    min-height:100vh;
    color:white;
    overflow-x:hidden;
    position:relative;
}

body::before{
    content:"";
    position:fixed;
    inset:0;
    background:rgba(15,23,42,0.75);
    z-index:-1;
}

/* ICONOS */

.icon-bg{
    position:fixed;
    opacity:0.08;
    z-index:-1;
    pointer-events:none;
}

.icon1{
    top:80px;
    left:60px;
    width:120px;
}

.icon2{
    bottom:80px;
    right:80px;
    width:140px;
}

.icon3{
    top:40%;
    right:35%;
    width:100px;
}

/* CONTENEDOR */

.container{
    width:95%;
    max-width:1400px;
    margin:30px auto;
    display:flex;
    gap:25px;
}

/* SIDEBAR */

.sidebar{
    width:30%;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(15px);
    border-radius:20px;
    padding:25px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
}

.sidebar h2{
    margin-bottom:15px;
    font-size:22px;
}

/* BOTÓN VOLVER */

.btn-volver{
    display:block;
    width:100%;
    text-align:center;
    padding:12px;
    margin-bottom:20px;
    border-radius:14px;
    text-decoration:none;
    color:white;
    font-weight:600;
    background:rgba(255,255,255,0.10);
    border:1px solid rgba(255,255,255,0.15);
    transition:.3s;
}

.btn-volver:hover{
    background:#3b82f6;
    transform:translateY(-2px);
}

/* ALUMNAS */

.alumna-card{
    display:flex;
    align-items:center;
    gap:15px;
    background:rgba(255,255,255,0.06);
    padding:15px;
    border-radius:15px;
    margin-bottom:15px;
    text-decoration:none;
    color:white;
    transition:.3s;
}

.alumna-card:hover{
    transform:translateY(-3px);
    background:rgba(255,255,255,0.12);
}

.alumna-card img{
    width:55px;
    height:55px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid rgba(255,255,255,0.3);
}

.alumna-info h4{
    font-size:15px;
}

.alumna-info p{
    font-size:12px;
    opacity:.7;
}

/* CHAT */

.chat-area{
    width:70%;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(15px);
    border-radius:20px;
    padding:25px;
    display:flex;
    flex-direction:column;
    height:85vh;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
}

/* HEADER */

.chat-header{
    display:flex;
    align-items:center;
    gap:15px;
    padding-bottom:20px;
    border-bottom:1px solid rgba(255,255,255,0.1);
}

.chat-header img{
    width:60px;
    height:60px;
    border-radius:50%;
    object-fit:cover;
}

.chat-header h3{
    font-size:20px;
}

.chat-header p{
    font-size:13px;
    opacity:.7;
}

/* MENSAJES */

.chat-box{
    flex:1;
    overflow-y:auto;
    padding:20px 0;
}

.msg{
    max-width:70%;
    padding:14px 18px;
    border-radius:18px;
    margin-bottom:15px;
    font-size:14px;
    line-height:1.5;
}

.msg strong{
    display:block;
    font-size:13px;
    margin-bottom:5px;
}

.hora{
    display:block;
    margin-top:8px;
    font-size:11px;
    opacity:.7;
}

/* FORM */

form{
    margin-top:10px;
}

textarea{
    width:100%;
    height:90px;
    border:none;
    border-radius:15px;
    padding:15px;
    resize:none;
    outline:none;
    font-size:14px;
}

button{
    width:100%;
    margin-top:12px;
    padding:14px;
    border:none;
    border-radius:14px;
    background:#3b82f6;
    color:white;
    font-weight:600;
    font-size:15px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#2563eb;
    transform:scale(1.02);
}

.empty{
    text-align:center;
    margin-top:100px;
    opacity:.7;
    font-size:15px;
}

</style>
</head>
<body>

<!-- ICONOS -->
<img src="../assets/img/libro.png" class="icon-bg icon1">
<img src="../assets/img/graduacion.png" class="icon-bg icon2">
<img src="../assets/img/chat.png" class="icon-bg icon3">

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <h2>👩‍🎓 Alumnas</h2>

        <a href="dashboard.php" class="btn-volver">
            ← Volver al Panel
        </a>

        <?php while($a = $alumnos->fetch_assoc()): ?>

            <?php
            $fotoNombre = trim($a['foto'] ?? '');
            $rutaFisica = __DIR__ . "/../assets/img/usuarios/" . $fotoNombre;

            $foto = (!empty($fotoNombre) && file_exists($rutaFisica))
                ? $fotoNombre
                : 'default.png';
            ?>

            <a class="alumna-card"
               href="mensajes_docente.php?alumno_id=<?= $a['id'] ?>">

                <img src="../assets/img/usuarios/<?= $foto ?>">

                <div class="alumna-info">
                    <h4><?= $a['nombre'] . " " . $a['apellido'] ?></h4>
                    <p>Conversación activa</p>
                </div>

            </a>

        <?php endwhile; ?>

    </div>

    <!-- CHAT -->
    <div class="chat-area">

        <?php if($alumno): ?>

            <?php
            $fotoHeaderNombre = trim($alumno['foto'] ?? '');
            $rutaHeader = __DIR__ . "/../assets/img/usuarios/" . $fotoHeaderNombre;

            $fotoHeader = (!empty($fotoHeaderNombre) && file_exists($rutaHeader))
                ? $fotoHeaderNombre
                : 'default.png';
            ?>

            <!-- HEADER -->
            <div class="chat-header">

                <img src="../assets/img/usuarios/<?= $fotoHeader ?>">

                <div>
                    <h3>
                        <?= $alumno['nombre'] . " " . $alumno['apellido'] ?>
                    </h3>
                    <p>Chat académico activo</p>
                </div>

            </div>

            <!-- MENSAJES -->
            <div class="chat-box" id="chatBox">

                <?php while($m = $mensajes->fetch_assoc()): ?>

                    <div class="msg"
                        style="
                            background: <?= ($m['emisor_id'] == $docente_id) ? '#3b82f6' : '#ffffff' ?>;
                            color: <?= ($m['emisor_id'] == $docente_id) ? '#ffffff' : '#111827' ?>;
                            margin-left: <?= ($m['emisor_id'] == $docente_id) ? 'auto' : '0' ?>;
                        ">

                        <strong><?= $m['nombre'] ?></strong>

                        <?= $m['mensaje'] ?>

                        <span class="hora">
                            <?= date("d/m/Y H:i", strtotime($m['fecha'])) ?>
                        </span>

                    </div>

                <?php endwhile; ?>

            </div>

            <!-- FORM -->
            <form method="POST" action="../controllers/enviar_mensaje.php">

                <input type="hidden"
                       name="receptor_id"
                       value="<?= $alumno_id ?>">

                <textarea
                    name="mensaje"
                    placeholder="Escribe tu respuesta..."
                    required></textarea>

                <button type="submit">
                    Enviar mensaje
                </button>

            </form>

        <?php else: ?>

            <div class="empty">
                👈 Selecciona una alumna para iniciar conversación
            </div>

        <?php endif; ?>

    </div>

</div>

<script>
let chat = document.getElementById("chatBox");
if(chat){
    chat.scrollTop = chat.scrollHeight;
}
</script>

</body>
</html>