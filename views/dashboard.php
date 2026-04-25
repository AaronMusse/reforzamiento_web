<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'alumna') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$rol = $_SESSION['rol'];

// --- CONSULTAS DINÁMICAS ALUMNA ---
$num_cursos = 0;
$num_actividades = 0;
$num_notificaciones = 0;

// Cursos matriculados
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM matriculas WHERE alumno_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$num_cursos = $stmt->get_result()->fetch_assoc()['total'];

// Entregas pendientes
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM entregas WHERE alumno_id = ? AND estado = 'pendiente'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$num_actividades = $stmt->get_result()->fetch_assoc()['total'];

// Notificaciones no leídas
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE alumno_id = ? AND estado = 'pendiente'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$num_notificaciones = $stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Alumna - INIF 48</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-color: #1e3a8a;
        --secondary-color: #3b82f6;
        --bg-color: #f8fafc;
        --sidebar-color: #0f172a;
        --text-color: #334155;
        --white: #ffffff;
    }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background: var(--bg-color);
        display: flex;
        overflow-x: hidden;
    }

    /* SIDEBAR */
    .sidebar {
        width: 260px;
        height: 100vh;
        background: var(--sidebar-color);
        color: var(--white);
        position: fixed;
        transition: 0.3s;
        z-index: 1000;
        display: flex;
        flex-direction: column;
    }

    .sidebar.collapsed { margin-left: -260px; }

    .sidebar-header {
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .logo-img { width: 60px; height: 60px; border-radius: 50%; border: 2px solid var(--secondary-color); margin-bottom: 10px; }

    .sidebar-menu { flex: 1; padding: 20px 15px; overflow-y: auto; }
    .sidebar-menu a {
        display: flex;
        align-items: center;
        color: #94a3b8;
        text-decoration: none;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 5px;
        transition: 0.2s;
        font-size: 15px;
    }
    .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); color: var(--white); }
    .sidebar-menu a i { margin-right: 12px; width: 20px; text-align: center; }

    /* MAIN CONTENT */
    .main-content {
        flex: 1;
        margin-left: 260px;
        transition: 0.3s;
        width: calc(100% - 260px);
    }
    .main-content.expanded { margin-left: 0; width: 100%; }

    /* TOP BAR */
    .topbar {
        height: 70px;
        background: var(--white);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        position: sticky;
        top: 0;
        z-index: 900;
    }

    .toggle-btn { font-size: 20px; cursor: pointer; color: var(--text-color); padding: 10px; }

    .content-padding { padding: 30px; }

    /* HERO GRID */
    .hero-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 40px;
    }

    .hero-item {
        height: 350px;
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .hero-item:hover .hero-img { transform: scale(1.05); }

    .hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(15, 23, 42, 0.8));
        padding: 25px;
        color: var(--white);
    }

    /* CARDS */
    .history-card {
        background: var(--white);
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        margin-bottom: 40px;
    }

    .history-card h2 { color: var(--primary-color); font-size: 24px; margin-top: 0; display: flex; align-items: center; gap: 10px; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        border-top: 1px solid #f1f5f9;
        padding-top: 25px;
        margin-top: 20px;
    }

    .info-item { display: flex; gap: 15px; align-items: flex-start; }
    .info-icon { width: 40px; height: 40px; background: #f1f5f9; color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .info-content h5 { margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase; }
    .info-content p { margin: 3px 0 0 0; font-size: 15px; color: var(--text-color); line-height: 1.4; }

    /* STATS */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
    .stat-box {
        background: var(--white);
        padding: 25px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        transition: 0.3s;
    }
    .stat-box:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
</style>
</head>

<body>

    <!-- SIDEBAR -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../assets/img/logob.jpg" class="logo-img" alt="Logo">
            <h3>INIF 48</h3>
            <small>Panel Alumna</small>
        </div>

        <div class="sidebar-menu">
            <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Inicio</a>
            <a href="mis_cursos.php"><i class="fas fa-book"></i> Mis cursos</a>
            <a href="actividades.php"><i class="fas fa-tasks"></i> Actividades</a>
            <a href="mensajes.php"><i class="fas fa-comments"></i> Mensajes</a>
            <a href="notificaciones.php"><i class="fas fa-bell"></i> Notificaciones</a>
            <a href="perfil.php"><i class="fas fa-user"></i> Perfil</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content" id="main-content">
        
        <header class="topbar">
            <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
            <div class="user-info">
                <span>Hola, <b><?php echo $_SESSION['usuario']; ?></b></span>
                <a href="../logout.php" style="background: #fee2e2; color: #ef4444; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </header>

        <div class="content-padding">
            
            <section class="hero-grid">
                <div class="hero-item">
                    <img src="../assets/img/fondob.JPG" class="hero-img">
                    <div class="hero-overlay">
                        <h2 style="margin:0; font-size: 22px;">Instalaciones Modernas</h2>
                    </div>
                </div>
                <div class="hero-item">
                    <img src="../assets/img/fondoc.jpg" class="hero-img">
                    <div class="hero-overlay">
                        <h2 style="margin:0; font-size: 22px;">Excelencia Académica</h2>
                    </div>
                </div>
            </section>

            <h1 style="color: var(--primary-color); font-weight: 800; margin-bottom: 30px;">Bienvenidos al INIF 48</h1>

            <section class="history-card">
                <h2><i class="fas fa-landmark"></i> Nuestra Historia</h2>
                <p>La I.E. INIF 48 de Sullana es un emblemático colegio público de mujeres con más de 60 años de antigüedad, destacado por su enfoque técnico. En 2024, fue inaugurada su modernización con una inversión superior a S/ 12 millones, beneficiando a más de mil alumnas con laboratorios y talleres de última generación.</p>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marked-alt"></i></div>
                        <div class="info-content"><h5>Ubicación</h5><p>Av. José de Lama 2302, Sullana.</p></div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-university"></i></div>
                        <div class="info-content"><h5>Gestión</h5><p>Pública, Secundaria Técnica.</p></div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-box">
                    <div class="stat-icon" style="background: #e0f2fe; color: #0ea5e9;"><i class="fas fa-graduation-cap"></i></div>
                    <div><p><?php echo $num_cursos; ?></p><span>Cursos</span></div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon" style="background: #f3e8ff; color: #a855f7;"><i class="fas fa-tasks"></i></div>
                    <div><p><?php echo $num_actividades; ?></p><span>Tareas</span></div>
                </div>
                <div class="stat-box">
                    <div class="stat-icon" style="background: #dcfce7; color: #22c55e;"><i class="fas fa-bell"></i></div>
                    <div><p><?php echo $num_notificaciones; ?></p><span>Avisos</span></div>
                </div>
            </section>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('main-content').classList.toggle('expanded');
        }
    </script>
</body>
</html>
