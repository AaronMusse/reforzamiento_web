<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'alumna') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener cursos donde la alumna está matriculada
$query = "SELECT c.* FROM cursos c 
          JOIN matriculas m ON c.id = m.curso_id 
          WHERE m.alumno_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cursos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cursos - INIF 48</title>
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
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg-color);
            display: flex;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background: var(--sidebar-color);
            color: var(--white);
            position: fixed;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo-img { width: 60px; height: 60px; border-radius: 50%; border: 2px solid var(--secondary-color); margin-bottom: 10px; }
        .sidebar-menu { flex: 1; padding: 20px 15px; }
        .sidebar-menu a {
            display: flex; align-items: center; color: #94a3b8; text-decoration: none;
            padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; transition: 0.2s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); color: var(--white); }
        .sidebar-menu a i { margin-right: 12px; width: 20px; text-align: center; }

        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        
        .cursos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .curso-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; display: flex; flex-direction: column; }
        .curso-card:hover { transform: translateY(-5px); }
        .curso-image {
            width: 100%;
            height: 150px;
            background-size: cover;
            background-position: center;
        }
        .curso-info { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        .curso-info h3 { margin: 0 0 10px 0; color: var(--primary-color); }
        .curso-info p { color: #64748b; font-size: 14px; margin-bottom: 20px; flex: 1; }
        
        .btn { display: block; text-align: center; padding: 12px; background: var(--secondary-color); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/img/logob.jpg" class="logo-img" alt="Logo">
            <h3>INIF 48</h3>
            <small>Panel Alumna</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="mis_cursos.php" class="active"><i class="fas fa-book"></i> Mis cursos</a>
            <a href="actividades.php"><i class="fas fa-tasks"></i> Actividades</a>
            <a href="mensajes.php"><i class="fas fa-comments"></i> Mensajes</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </nav>

    <main class="main-content">
        <h1 style="color: var(--primary-color); margin-bottom: 30px;">Mis Cursos Matriculados</h1>

        <?php if($cursos->num_rows > 0): ?>
            <div class="cursos-grid">
                <?php while($curso = $cursos->fetch_assoc()): 
                    $img = !empty($curso['imagen_url']) ? $curso['imagen_url'] : 'default_curso.jpg';
                    $imgPath = "../assets/img/cursos/" . $img;
                    if (!file_exists($imgPath) || $img == 'default_curso.jpg') {
                        $imgPath = "https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80";
                    }
                ?>
                <div class="curso-card">
                    <div class="curso-image" style="background-image: url('<?php echo $imgPath; ?>');"></div>
                   <div class="curso-info">
    <h3><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>

    <p>
        <?php echo htmlspecialchars(substr($curso['descripcion'], 0, 80)) . (strlen($curso['descripcion']) > 80 ? '...' : ''); ?>
    </p>

    <p style="font-size: 13px; color: #475569;">
        <strong>Inicio:</strong> <?php echo date("d/m/Y", strtotime($curso['fecha_inicio'])); ?><br>
        <strong>Fin:</strong> <?php echo date("d/m/Y", strtotime($curso['fecha_fin'])); ?>
    </p>
                        <a href="ver_curso.php?id=<?php echo $curso['id']; ?>" class="btn">Entrar al Curso</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 20px;">
                <i class="fas fa-folder-open" style="font-size: 50px; color: #cbd5e1; margin-bottom: 20px; display: block;"></i>
                <p style="color: #64748b;">Aún no estás matriculada en ningún curso.</p>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
