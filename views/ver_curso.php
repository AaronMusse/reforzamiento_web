<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'alumna') {
    header("Location: ../index.php");
    exit();
}

$curso_id = $_GET['id'] ?? null;
if (!$curso_id) { header("Location: mis_cursos.php"); exit(); }

// Verificar que esté matriculada
$stmt = $conn->prepare("SELECT c.* FROM cursos c JOIN matriculas m ON c.id = m.curso_id WHERE c.id = ? AND m.alumna_id = ?");
$stmt->bind_param("ii", $curso_id, $_SESSION['user_id']);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) { header("Location: mis_cursos.php"); exit(); }

// Obtener unidades y sesiones
$unidades = [];
$res_unidades = $conn->query("SELECT * FROM unidades WHERE curso_id = $curso_id ORDER BY orden ASC, id ASC");
while($u = $res_unidades->fetch_assoc()) {
    $u_id = $u['id'];
    $res_sesiones = $conn->query("SELECT * FROM sesiones WHERE unidad_id = $u_id ORDER BY orden ASC, id ASC");
    $sesiones = [];
    while($s = $res_sesiones->fetch_assoc()) {
        $s_id = $s['id'];
        $res_mat = $conn->query("SELECT * FROM materiales WHERE sesion_id = $s_id");
        $s['materiales'] = $res_mat->fetch_all(MYSQLI_ASSOC);
        $sesiones[] = $s;
    }
    $u['sesiones'] = $sesiones;
    $unidades[] = $u;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($curso['nombre_curso']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #1e3a8a; --secondary-color: #3b82f6; --bg-color: #f8fafc; --white: #ffffff; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-color); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: #0f172a; color: white; position: fixed; }
        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        .unidad-card { background: white; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); overflow: hidden; }
        .unidad-header { background: #f8fafc; padding: 15px 25px; border-bottom: 1px solid #f1f5f9; }
        .sesion-row { padding: 20px 25px; border-bottom: 1px solid #f8fafc; }
        .sesion-row:last-child { border-bottom: none; }
        .material-link { display: inline-flex; align-items: center; background: #eff6ff; color: #1e40af; padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 13px; margin-top: 10px; margin-right: 10px; transition: 0.2s; }
        .material-link:hover { background: #dbeafe; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: #64748b; text-decoration: none; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div style="padding: 20px; text-align: center;">
            <img src="../assets/img/logob.jpg" style="width:60px; border-radius:50%;" alt="Logo">
            <h3>INIF 48</h3>
            <a href="mis_cursos.php" style="color: #94a3b8; text-decoration: none;"><i class="fas fa-arrow-left"></i> Mis Cursos</a>
        </div>
    </nav>

    <main class="main-content">
        <a href="mis_cursos.php" class="btn-back"><i class="fas fa-chevron-left"></i> Volver a la lista</a>
        <h1 style="color: var(--primary-color); margin: 0;"><?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>
       <p style="color: #64748b; margin-bottom: 15px;">
    <?php echo htmlspecialchars($curso['descripcion']); ?>
</p>

<p style="color: #334155; margin-bottom: 40px; font-size: 14px;">
    <strong>Fecha de inicio:</strong>
    <?php echo date("d/m/Y", strtotime($curso['fecha_inicio'])); ?>

    &nbsp;&nbsp;|&nbsp;&nbsp;

    <strong>Fecha de finalización:</strong>
    <?php echo date("d/m/Y", strtotime($curso['fecha_fin'])); ?>
</p>

        <?php foreach($unidades as $u): ?>
            <div class="unidad-card">
                <div class="unidad-header">
                    <h3 style="margin:0; color: var(--primary-color);"><i class="fas fa-layer-group" style="margin-right:10px; color: var(--secondary-color);"></i> <?php echo htmlspecialchars($u['titulo']); ?></h3>
                </div>
                <div class="sesiones-container">
                    <?php if(count($u['sesiones']) > 0): ?>
                        <?php foreach($u['sesiones'] as $s): ?>
                            <div class="sesion-row">
                                <h4 style="margin:0 0 5px 0;"><i class="far fa-circle" style="font-size:10px; margin-right:10px; vertical-align:middle;"></i> <?php echo htmlspecialchars($s['titulo']); ?></h4>
                                <p style="margin:0; font-size:14px; color: #64748b;"><?php echo htmlspecialchars($s['contenido']); ?></p>
                                
                                <div class="materiales">
                                    <?php foreach($s['materiales'] as $m): 
                                        $target = ($m['tipo'] == 'archivo') ? "../assets/materiales/" . $m['ruta'] : $m['ruta'];
                                    ?>
                                        <a href="<?php echo $target; ?>" target="_blank" class="material-link">
                                            <i class="fas <?php echo $m['tipo'] == 'archivo' ? 'fa-download' : 'fa-external-link-alt'; ?>" style="margin-right:8px;"></i>
                                            <?php echo htmlspecialchars($m['titulo']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="padding: 20px; color: #94a3b8; font-style: italic;">No hay contenido disponible en esta unidad.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if(count($unidades) == 0): ?>
            <div style="text-align:center; padding: 50px; background:white; border-radius:15px;">
                <p style="color: #64748b;">El profesor aún no ha publicado contenido para este curso.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
