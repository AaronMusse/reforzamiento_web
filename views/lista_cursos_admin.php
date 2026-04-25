<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$sql = "
    SELECT 
        c.id,
        c.nombre_curso,
        c.descripcion,
        c.fecha_inicio,
        c.fecha_fin,
        c.estado,
        u.nombre,
        u.apellido
    FROM cursos c
    INNER JOIN usuarios u ON c.docente_id = u.id
    WHERE u.rol = 'docente'
    ORDER BY c.id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Lista de Cursos - Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f8fafc;
}

.container {
    padding: 40px;
}

h2 {
    color: #1e3a8a;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

th {
    background: #1e3a8a;
    color: white;
    padding: 15px;
    text-align: left;
}

td {
    padding: 15px;
    border-bottom: 1px solid #e2e8f0;
}

tr:hover {
    background: #f8fafc;
}

.btn-delete {
    background: #ef4444;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
}

.btn-back {
    display: inline-block;
    margin-bottom: 20px;
    background: #3b82f6;
    color: white;
    padding: 10px 18px;
    border-radius: 10px;
    text-decoration: none;
}
</style>
</head>
<body>

<div class="container">

    <a href="dashboard_admin.php" class="btn-back">
        ← Volver al Dashboard
    </a>

    <h2>Gestión de Cursos</h2>

    <table>
        <thead>
            <tr>
                <th>Curso</th>
                <th>Docente</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>

            <tr>
                <td><?php echo $row['nombre_curso']; ?></td>
                <td><?php echo $row['nombre'] . " " . $row['apellido']; ?></td>
                <td><?php echo $row['fecha_inicio']; ?></td>
                <td><?php echo $row['fecha_fin']; ?></td>
                <td><?php echo ucfirst($row['estado']); ?></td>
               <td>
    <a 
        href="editar_curso_admin.php?id=<?php echo $row['id']; ?>"
        style="background:#3b82f6;color:white;padding:8px 14px;border-radius:8px;text-decoration:none;font-size:14px;margin-right:5px;"
    >
        Editar
    </a>

    <a 
        href="../controllers/eliminar_curso_admin.php?id=<?php echo $row['id']; ?>"
        style="background:#ef4444;color:white;padding:8px 14px;border-radius:8px;text-decoration:none;font-size:14px;"
        onclick="return confirm('¿Eliminar este curso?')"
    >
        Eliminar
    </a>
</td>
            </tr>

            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No hay cursos registrados</td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>

</div>

</body>
</html>