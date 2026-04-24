<?php
session_start();
require_once "../config/conexion.php";
require_once "../controllers/cursoController.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'docente') {
    header("Location: ../index.php");
    exit();
}

$controller = new CursoController($conn);
$cursos = $controller->listarCursosDocente($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cursos - Panel Docente</title>
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
        
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* GRID DE CURSOS */
        .cursos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .curso-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .curso-card:hover { transform: translateY(-5px); }

        .curso-image {
            width: 100%;
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .curso-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-activo { background: #dcfce7; color: #16a34a; }

        .curso-info { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        .curso-info h3 { margin: 0 0 10px 0; color: var(--primary-color); font-size: 1.25rem; }
        .curso-info p { color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px; flex: 1; }

        .curso-footer {
            padding: 15px 20px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn { padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; font-size: 14px; }
        .btn-primary { background: var(--secondary-color); color: white; }
        .btn-outline { border: 1px solid #e2e8f0; color: var(--text-color); background: white; }
        .btn-danger { background: #fee2e2; color: #ef4444; }
        .btn-danger:hover { background: #ef4444; color: white; }

        /* MODAL */
        .modal {
            display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); align-items: center; justify-content: center;
        }
        .modal-content { background: white; padding: 30px; border-radius: 20px; width: 450px; max-width: 90%; }
        input, textarea, select { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        label { font-weight: 600; color: var(--text-color); font-size: 14px; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/img/logob.jpg" class="logo-img" alt="Logo">
            <h3>INIF 48</h3>
            <small>Panel Docente</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard_docente.php"><i class="fas fa-th-large"></i> Inicio</a>
            <a href="cursos.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Cursos</a>
            <a href="actividades.php"><i class="fas fa-edit"></i> Actividades</a>
            <a href="examenes.php"><i class="fas fa-file-alt"></i> Exámenes</a>
            <a href="mensajes.php"><i class="fas fa-envelope"></i> Mensajes</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="header-actions">
            <div>
                <h1 style="color: var(--primary-color); margin: 0;">Gestión de Cursos</h1>
                <p style="color: #64748b; margin: 5px 0 0 0;">Administra tus cursos y alumnos inscritos</p>
            </div>
            <button class="btn btn-primary" onclick="showModal()"><i class="fas fa-plus"></i> Nuevo Curso</button>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div style="background: #dcfce7; color: #16a34a; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="cursos-grid">
            <?php while($curso = $cursos->fetch_assoc()): 
                $img = !empty($curso['imagen_url']) ? $curso['imagen_url'] : 'default_curso.jpg';
                $imgPath = "../assets/img/cursos/" . $img;
                if (!file_exists($imgPath) || $img == 'default_curso.jpg') {
                    $imgPath = "https://images.unsplash.com/photo-1501504905252-473c47e087f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80";
                }
            ?>
            <div class="curso-card">
                <div class="curso-image" style="background-image: url('<?php echo $imgPath; ?>');">
                    <span class="curso-status status-activo"><?php echo $curso['estado']; ?></span>
                </div>
                <div class="curso-info">
                    <h3><?php echo htmlspecialchars($curso['nombre_curso']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($curso['descripcion'], 0, 100)) . (strlen($curso['descripcion']) > 100 ? '...' : ''); ?></p>
                    <div style="margin-bottom: 15px; font-size: 14px; color: #64748b;">
                   <p style="margin: 5px 0;">
                    <i class="fas fa-calendar-alt" style="color: #3b82f6;"></i>
                        Inicio: <?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?>
                           </p>

                      <p style="margin: 5px 0;">
                  <i class="fas fa-calendar-check" style="color: #16a34a;"></i>
                      Fin: <?php echo date('d/m/Y', strtotime($curso['fecha_fin'])); ?>
                        </p>
                        </div>
                    <div style="margin-top: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <a href="curso_detalle.php?id=<?php echo $curso['id']; ?>" class="btn btn-primary" style="text-align:center;">
                            <i class="fas fa-eye"></i> Entrar
                        </a>
                        <a href="curso_alumnos.php?id=<?php echo $curso['id']; ?>" class="btn btn-outline" style="text-align:center;">
                            <i class="fas fa-users"></i> Alumnos
                        </a>
                    </div>
                </div>
                <div class="curso-footer">
                    <small style="color: #94a3b8;"><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($curso['created_at'])); ?></small>
                    <form action="../controllers/cursoController.php" method="POST" onsubmit="return confirm('¿Eliminar este curso?');">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="id" value="<?php echo $curso['id']; ?>">
                        <button type="submit" class="btn btn-danger" style="padding: 5px 10px;"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <!-- Modal Nuevo Curso -->
    <div id="modalCurso" class="modal">
        <div class="modal-content">
            <h2 style="margin-top: 0; color: var(--primary-color);">Crear Nuevo Curso</h2>
            <form action="../controllers/cursoController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="crear">
                
                <label>Nombre del Curso</label>
                <input type="text" name="nombre_curso" required placeholder="Ej: Matemática 1ero">
                
                <label>Descripción</label>
                <textarea name="descripcion" rows="4" placeholder="Breve descripción del curso..."></textarea>
                
                <label>Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" required>

                <label>Fecha de Fin</label>
                <input type="date" name="fecha_fin" required>

                <label>Imagen del Curso</label>
                <input type="file" name="imagen" accept="image/*">
                <small style="color: #64748b; display: block; margin-bottom: 15px;">Formatos permitidos: JPG, PNG. Recomendado: 800x450px</small>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 2;">Guardar Curso</button>
                    <button type="button" class="btn btn-outline" onclick="hideModal()" style="flex: 1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showModal() { document.getElementById('modalCurso').style.display = 'flex'; }
        function hideModal() { document.getElementById('modalCurso').style.display = 'none'; }
    </script>
</body>
</html>
