<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$docente_id = $_SESSION['user_id'];

/*
========================================
OBTENER ALUMNAS
========================================
*/

$alumnas = $conn->query("
    SELECT id, nombre, apellido
    FROM usuarios
    WHERE rol = 'alumna'
    ORDER BY nombre ASC
");

/*
========================================
NOTIFICACIONES ENVIADAS
========================================
*/

$stmt = $conn->prepare("
    SELECT n.*, u.nombre, u.apellido
    FROM notificaciones n
    INNER JOIN usuarios u
        ON n.alumno_id = u.id
    WHERE n.docente_id = ?
    ORDER BY n.fecha DESC
");

$stmt->bind_param("i", $docente_id);
$stmt->execute();
$historial = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Notificaciones</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    background:
        linear-gradient(135deg, #0f172a, #1e3a8a),
        url('../assets/img/fondo-educativo.png');
    background-size: cover;
    background-attachment: fixed;
    min-height:100vh;
    color:white;
}

.container{
    width:95%;
    max-width:1400px;
    margin:30px auto;
    display:flex;
    gap:25px;
}

/* FORMULARIO */

.left{
    width:35%;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(15px);
    border-radius:20px;
    padding:25px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
}

.left h2{
    margin-bottom:20px;
}

input, select, textarea{
    width:100%;
    padding:14px;
    border:none;
    border-radius:14px;
    margin-bottom:15px;
    outline:none;
    font-size:14px;
}

textarea{
    height:140px;
    resize:none;
}

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:14px;
    background:#3b82f6;
    color:white;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#2563eb;
    transform:translateY(-2px);
}

.btn-back{
    display:block;
    text-align:center;
    text-decoration:none;
    margin-bottom:20px;
    padding:12px;
    border-radius:14px;
    background:rgba(255,255,255,0.08);
    color:white;
}

/* HISTORIAL */

.right{
    width:65%;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(15px);
    border-radius:20px;
    padding:25px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
}

.right h2{
    margin-bottom:20px;
}

.card{
    background:rgba(255,255,255,0.07);
    padding:20px;
    border-radius:16px;
    margin-bottom:15px;
}

.card h3{
    margin-bottom:8px;
    font-size:18px;
}

.card p{
    margin-bottom:8px;
    opacity:.9;
}

.meta{
    font-size:13px;
    opacity:.7;
}

.badge{
    display:inline-block;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    background:#10b981;
    margin-top:10px;
}

</style>
</head>
<body>

<div class="container">

    <!-- FORMULARIO -->
    <div class="left">

        <a href="dashboard_docente.php" class="btn-back">
            ← Volver al Panel
        </a>

        <h2>🔔 Nueva Notificación</h2>

        <form method="POST" action="../controllers/guardar_notificacion.php">

            <select name="alumno_id" required>
                <option value="">Seleccionar alumna</option>

                <?php while($a = $alumnas->fetch_assoc()): ?>
                    <option value="<?= $a['id'] ?>">
                        <?= $a['nombre'] . " " . $a['apellido'] ?>
                    </option>
                <?php endwhile; ?>

            </select>

            <input
                type="text"
                name="titulo"
                placeholder="Título de la notificación"
                required>

            <textarea
                name="mensaje"
                placeholder="Escribe el mensaje..."
                required></textarea>

            <select name="tipo" required>
                <option value="">Tipo de notificación</option>
                <option value="tarea">Nueva tarea 📚</option>
                <option value="horario">Cambio de horario 🕒</option>
                <option value="aviso">Aviso institucional 📢</option>
                <option value="recordatorio">Recordatorio ⏰</option>
                <option value="nota">Calificación 📝</option>
                <option value="material">Material nuevo 📂</option>
                <option value="evento">Evento 🎉</option>
            </select>

            <button type="submit">
                Enviar Notificación
            </button>

        </form>

    </div>

    <!-- HISTORIAL -->
    <div class="right">

        <h2>📋 Historial Enviado</h2>

        <?php while($n = $historial->fetch_assoc()): ?>

            <div class="card">

                <h3><?= $n['titulo'] ?></h3>

                <p><?= $n['mensaje'] ?></p>

                <div class="meta">
                    Para:
                    <?= $n['nombre'] . " " . $n['apellido'] ?>
                    <br>
                    Fecha:
                    <?= date("d/m/Y H:i", strtotime($n['fecha'])) ?>
                </div>

                <div class="badge">
                    <?= strtoupper($n['tipo']) ?>
                </div>

            </div>

        <?php endwhile; ?>

    </div>

</div>

</body>
</html>