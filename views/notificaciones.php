<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$alumna_id = $_SESSION['user_id'];

/*
========================================
OBTENER NOTIFICACIONES DE LA ALUMNA
========================================
*/

$stmt = $conn->prepare("
    SELECT *
    FROM notificaciones
    WHERE alumno_id = ?
    ORDER BY fecha DESC
");

$stmt->bind_param("i", $alumna_id);
$stmt->execute();
$notificaciones = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Notificaciones</title>

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
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    min-height:100vh;
    color:white;
}

/* CAPA OSCURA */

body::before{
    content:"";
    position:fixed;
    inset:0;
    background:rgba(15,23,42,.78);
    z-index:-1;
}

/* ICONOS DECORATIVOS */

.icon-bg{
    position:fixed;
    opacity:.08;
    z-index:-1;
    pointer-events:none;
}

.icon1{
    top:70px;
    left:50px;
    width:120px;
}

.icon2{
    bottom:70px;
    right:70px;
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
    max-width:1300px;
    margin:30px auto;
}

/* HEADER */

.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.top h1{
    font-size:28px;
}

.btn-volver{
    text-decoration:none;
    color:white;
    background:#3b82f6;
    padding:12px 22px;
    border-radius:14px;
    font-weight:600;
    transition:.3s;
}

.btn-volver:hover{
    background:#2563eb;
}

/* GRID */

.grid{
    display:grid;
    gap:20px;
}

/* CARD */

.card{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(14px);
    border-radius:20px;
    padding:25px;
    box-shadow:0 15px 40px rgba(0,0,0,.2);
    border-left:6px solid #3b82f6;
}

.card.leida{
    opacity:.75;
    border-left:6px solid #10b981;
}

.card h2{
    font-size:20px;
    margin-bottom:10px;
}

.card p{
    line-height:1.6;
    margin-bottom:10px;
}

.meta{
    font-size:14px;
    opacity:.8;
    margin-bottom:15px;
}

.estado{
    display:inline-block;
    padding:6px 14px;
    border-radius:30px;
    font-size:13px;
    font-weight:600;
    margin-bottom:15px;
}

.pendiente{
    background:#f59e0b;
    color:#111827;
}

.leido{
    background:#10b981;
    color:white;
}

.actions{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}

.actions a{
    text-decoration:none;
    padding:10px 18px;
    border-radius:12px;
    font-weight:600;
    transition:.3s;
}

.btn-read{
    background:#3b82f6;
    color:white;
}

.btn-delete{
    background:#ef4444;
    color:white;
}

.actions a:hover{
    transform:translateY(-2px);
}

.empty{
    text-align:center;
    margin-top:100px;
    font-size:18px;
    opacity:.8;
}

</style>
</head>
<body>

<!-- ICONOS -->
<img src="../assets/img/libro.png" class="icon-bg icon1">
<img src="../assets/img/graduacion.png" class="icon-bg icon2">
<img src="../assets/img/chat.png" class="icon-bg icon3">

<div class="container">

    <div class="top">
        <h1>🔔 Mis Notificaciones</h1>

        <a href="dashboard.php" class="btn-volver">
            ← Volver al Panel
        </a>
    </div>

    <div class="grid">

        <?php if($notificaciones->num_rows > 0): ?>

            <?php while($n = $notificaciones->fetch_assoc()): ?>

                <div class="card <?= $n['estado'] == 'leido' ? 'leida' : '' ?>">

                    <h2><?= $n['titulo'] ?></h2>

                    <p><?= $n['mensaje'] ?></p>

                    <div class="meta">
                        📚 Tipo: <?= strtoupper($n['tipo']) ?>
                        <br>
                        🕒 Fecha: <?= date("d/m/Y H:i", strtotime($n['fecha'])) ?>
                    </div>

                    <div class="estado <?= $n['estado'] ?>">
                        <?= strtoupper($n['estado']) ?>
                    </div>

                    <div class="actions">

                        <?php if($n['estado'] != 'leido'): ?>

                            <a
                                href="../controllers/marcar_leido.php?id=<?= $n['id'] ?>"
                                class="btn-read"
                            >
                                Marcar como leído
                            </a>

                        <?php endif; ?>

                        <a
                            href="../controllers/eliminar_notificacion.php?id=<?= $n['id'] ?>"
                            class="btn-delete"
                            onclick="return confirm('¿Eliminar notificación?')"
                        >
                            Eliminar
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="empty">
                No tienes notificaciones por el momento 📭
            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>