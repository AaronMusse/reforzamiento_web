<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'docente') {
    header("Location: ../index.php");
    exit();
}

$curso_id = $_GET['id'] ?? null;
if (!$curso_id) { header("Location: cursos.php"); exit(); }

// Obtener info del curso
$stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ? AND docente_id = ?");
$stmt->bind_param("ii", $curso_id, $_SESSION['user_id']);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) { header("Location: cursos.php"); exit(); }

// Manejo de acciones (Matricular/Desmatricular)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'matricular') {
            $alumna_id = $_POST['alumna_id'];
            $stmt = $conn->prepare("INSERT IGNORE INTO matriculas (alumna_id, curso_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $alumna_id, $curso_id);
            $stmt->execute();
        } elseif ($_POST['action'] == 'desmatricular') {
            $alumna_id = $_POST['alumna_id'];
            $stmt = $conn->prepare("DELETE FROM matriculas WHERE alumna_id = ? AND curso_id = ?");
            $stmt->bind_param("ii", $alumna_id, $curso_id);
            $stmt->execute();
        }
    }
}

// Obtener alumnos matriculados
$stmt = $conn->prepare("SELECT u.* FROM usuarios u JOIN matriculas m ON u.id = m.alumna_id WHERE m.curso_id = ?");
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$matriculados = $stmt->get_result();

// Obtener todos los alumnos (para el buscador)
$alumnos_todos = $conn->query("SELECT id, nombre, apellido, dni FROM usuarios WHERE rol = 'alumna' ORDER BY apellido ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alumnos - <?php echo htmlspecialchars($curso['nombre_curso']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #1e3a8a; --secondary-color: #3b82f6; --bg-color: #f8fafc; --white: #ffffff; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-color); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: #0f172a; color: white; position: fixed; }
        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        .card { background: white; padding: 25px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        .btn { padding: 8px 15px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; text-decoration: none; }
        .btn-primary { background: var(--secondary-color); color: white; }
        .btn-danger { background: #fee2e2; color: #ef4444; }
        .search-box { margin-bottom: 20px; display: flex; gap: 10px; }
        input { padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; flex: 1; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div style="padding: 20px; text-align: center;">
            <h3>INIF 48</h3>
            <a href="cursos.php" style="color: #94a3b8; text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver a Cursos</a>
        </div>
    </nav>

    <main class="main-content">
        <h1>Alumnos en: <?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>

        <div class="card">
            <h2>Matricular Alumna</h2>
            <form method="POST" class="search-box">
                <input type="hidden" name="action" value="matricular">
                <select name="alumna_id" required style="flex:1; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <option value="">Seleccione una alumna...</option>
                    <?php while($a = $alumnos_todos->fetch_assoc()): ?>
                        <option value="<?php echo $a['id']; ?>"><?php echo $a['apellido'] . ", " . $a['nombre'] . " (" . $a['dni'] . ")"; ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary">Matricular</button>
            </form>
        </div>

        <div class="card">
            <h2>Lista de Alumnas Matriculadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($matriculados->num_rows > 0): ?>
                        <?php while($m = $matriculados->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $m['dni']; ?></td>
                            <td><?php echo $m['apellido'] . ", " . $m['nombre']; ?></td>
                            <td><?php echo $m['correo']; ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('¿Remover alumna del curso?');">
                                    <input type="hidden" name="action" value="desmatricular">
                                    <input type="hidden" name="alumna_id" value="<?php echo $m['id']; ?>">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-user-minus"></i> Quitar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 20px; color: #64748b;">No hay alumnas matriculadas aún.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
