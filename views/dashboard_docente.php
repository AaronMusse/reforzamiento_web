<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/conexion.php";

// Verificación de sesión
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'docente') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$nombre_usuario = $_SESSION['usuario'];

// Inicializar contadores en 0
$num_cursos = 0;
$num_examenes = 0;
$num_alertas = 0;
$num_notificaciones = 0;

/**
 * Función segura para obtener conteos sin romper la página si la tabla/columna no existe
 */
function get_count($conn, $query, $id) {
    try {
        $stmt = @$conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                $data = $res->fetch_assoc();
                return $data['total'] ?? 0;
            }
        }
    } catch (Exception $e) {
        return 0;
    }
    return 0;
}

// Intentar obtener datos reales
$num_cursos = get_count($conn, "SELECT COUNT(*) as total FROM cursos WHERE docente_id = ?", $user_id);
$num_examenes = get_count($conn, "SELECT COUNT(*) as total FROM examenes e JOIN cursos c ON e.curso_id = c.id WHERE c.docente_id = ?", $user_id);
$num_alertas = get_count($conn, "SELECT COUNT(*) as total FROM entregas e JOIN actividades a ON e.actividad_id = a.id JOIN cursos c ON a.curso_id = c.id WHERE c.docente_id = ? AND e.estado = 'pendiente'", $user_id);
$num_notificaciones = get_count($conn, "SELECT COUNT(*) as total FROM notificaciones WHERE usuario_id = ? AND leido = 0", $user_id);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Docente - INIF 48</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e3a8a;
            --secondary-color: #3b82f6;
            --bg-color: #f8fafc;
            --sidebar-color: #0f172a;
            --text-color: #334155;
            --white: #ffffff;
            --danger: #ef4444;
            --success: #22c55e;
            --warning: #f59e0b;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-color);
            display: flex;
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
        }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); color: var(--white); }
        .sidebar-menu a i { margin-right: 12px; width: 20px; text-align: center; }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            margin-left: 260px;
            transition: 0.3s;
            width: calc(100% - 260px);
            min-height: 100vh;
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
        }

        .toggle-btn { font-size: 20px; cursor: pointer; color: var(--text-color); padding: 10px; }

        .content-padding { padding: 30px; }

        /* STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box {
            background: var(--white);
            padding: 20px;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }
        .stat-box i { font-size: 24px; margin-bottom: 10px; display: block; }
        .stat-box p { font-size: 24px; font-weight: 800; margin: 0; color: var(--sidebar-color); }
        .stat-box span { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600; }

        /* GRID */
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .card { background: var(--white); padding: 25px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 25px; }
        .card h2 { color: var(--primary-color); font-size: 20px; margin: 0 0 20px 0; display: flex; align-items: center; gap: 10px; }

        .calendar-widget { background: var(--primary-color); color: white; padding: 25px; border-radius: 24px; text-align: center; }
    </style>
</head>
<body>

    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../assets/img/logob.jpg" class="logo-img" alt="Logo">
            <h3>INIF 48</h3>
            <small>Panel Docente</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard_docente.php" class="active"><i class="fas fa-th-large"></i> Inicio</a>
            <a href="cursos.php"><i class="fas fa-chalkboard-teacher"></i> Cursos</a>
            <a href="actividades.php"><i class="fas fa-edit"></i> Actividades</a>
            <a href="examenes.php"><i class="fas fa-file-alt"></i> Exámenes</a>
            <a href="mensajes.php"><i class="fas fa-envelope"></i> Mensajes</a>
            <a href="notificaciones.php"><i class="fas fa-bell"></i> Notificaciones</a>
            <a href="reportes.php"><i class="fas fa-chart-bar"></i> Reportes</a>
            <a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </nav>

    <main class="main-content" id="main-content">
        <header class="topbar">
            <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
            <div style="display:flex; align-items:center; gap:20px;">
                <span>Hola, Prof. <b><?php echo htmlspecialchars($nombre_usuario); ?></b></span>
                <a href="../logout.php" style="background:#fee2e2; color:#ef4444; padding:8px 16px; border-radius:8px; text-decoration:none; font-weight:600;">Salir</a>
            </div>
        </header>

        <div class="content-padding">
            <h1 style="color:var(--primary-color); font-weight:800; margin:0;">Panel de Control</h1>
            <p style="color:#64748b; margin:5px 0 30px 0;">Bienvenido a la gestión académica del INIF 48</p>

            <div class="stats-grid">
                <div class="stat-box">
                    <i class="fas fa-book" style="color:#3b82f6;"></i>
                    <p><?php echo $num_cursos; ?></p>
                    <span>Cursos</span>
                </div>
                <div class="stat-box">
                    <i class="fas fa-file-alt" style="color:#a855f7;"></i>
                    <p><?php echo $num_examenes; ?></p>
                    <span>Exámenes</span>
                </div>
                <div class="stat-box">
                    <i class="fas fa-clock" style="color:#ef4444;"></i>
                    <p><?php echo $num_alertas; ?></p>
                    <span>Por Calificar</span>
                </div>
                <div class="stat-box">
                    <i class="fas fa-bell" style="color:#22c55e;"></i>
                    <p><?php echo $num_notificaciones; ?></p>
                    <span>Notificaciones</span>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="left-col">
                    <div class="card">
                        <h2><i class="fas fa-bolt"></i> Calificación Rápida</h2>
                        <p style="color:#64748b;">Tienes <b><?php echo $num_alertas; ?></b> actividades esperando revisión.</p>
                        <a href="actividades.php" style="color:var(--secondary-color); text-decoration:none; font-weight:600;">Ir a calificar →</a>
                    </div>
                    <div class="card">
                        <h2><i class="fas fa-history"></i> Actividad Reciente</h2>
                        <p style="color:#64748b; font-size:14px;">No hay actividades recientes para mostrar.</p>
                    </div>
                </div>
                <div class="right-col">
                    <div class="calendar-widget">
                        <i class="fas fa-calendar-day" style="font-size:30px; margin-bottom:10px;"></i>
                        <h2 style="margin:0;"><?php echo date('d'); ?></h2>
                        <p style="margin:0; opacity:0.8;"><?php echo date('F, Y'); ?></p>
                    </div>
                    <div class="card" style="margin-top:25px; text-align:center;">
                        <button style="width:100%; padding:12px; background:#16a34a; color:white; border:none; border-radius:10px; font-weight:600; cursor:pointer;">
                            <i class="fas fa-file-excel"></i> Reporte Excel
                        </button>
                    </div>
                </div>
            </div>
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
