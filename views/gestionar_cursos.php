<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$query = "
    SELECT 
        c.id,
        c.nombre_curso,
        c.descripcion,
        c.fecha_inicio,
        c.fecha_fin,
        u.nombre AS docente_nombre,
        u.apellido AS docente_apellido
    FROM cursos c
    INNER JOIN usuarios u 
        ON c.docente_id = u.id
    ORDER BY c.id DESC
";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestionar Cursos</title>

<style>
body {
    font-family: Arial;
    background: #f8fafc;
    padding: 30px;
}

.container {
    background: white;
    padding: 30px;
    border-radius: 20px;
    max-width: 1200px;
    margin: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th {
    background: #1e3a8a;
    color: white;
    padding: 12px;
}

td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.btn-delete {
    background: #ef4444;
    color: white;
    padding: 8px 14px;
    text-decoration: none;
    border-radius: 8px;
}

.back {
    display: inline-block;
    margin-top: 20px;
}
</style>
</head>
<body>

<div class="container">

    <h2>Gestionar Cursos</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Curso</th>
                <th>Descripción</th>
                <th>Docente</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody>

        <?php while($curso = mysqli_fetch_assoc($result)) : ?>

        <tr>
            <td><?php echo $curso['id']; ?></td>
            <td><?php echo $curso['nombre_curso']; ?></td>
            <td><?php echo $curso['descripcion']; ?></td>
            <td>
                <?php 
                echo $curso['docente_nombre'] . ' ' . $curso['docente_apellido']; 
                ?>
            </td>
            <td><?php echo $curso['fecha_inicio']; ?></td>
            <td><?php echo $curso['fecha_fin']; ?></td>
            <td>
                <a 
                    href="../controllers/eliminarCursoAdmin.php?id=<?php echo $curso['id']; ?>" 
                    class="btn-delete"
                    onclick="return confirm('¿Eliminar curso?')"
                >
                    Eliminar
                </a>
            </td>
        </tr>

        <?php endwhile; ?>

        </tbody>
    </table>

    <a href="dashboard_admin.php" class="back">
        ← Volver
    </a>

</div>

</body>
</html>