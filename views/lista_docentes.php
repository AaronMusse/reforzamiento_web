<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);session_start();
include("../config/conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$query = "SELECT id, nombre, apellido, dni, correo, fecha_nacimiento 
          FROM usuarios 
          WHERE rol = 'docente'
          ORDER BY id DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Lista de Docentes - INIF 48</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f8fafc;
    padding: 30px;
}

.container {
    max-width: 1100px;
    margin: auto;
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

h2 {
    margin-top: 0;
    color: #1e3a8a;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
}

table th {
    background: #1e3a8a;
    color: white;
    padding: 14px;
    text-align: left;
}

table td {
    padding: 14px;
    border-bottom: 1px solid #e2e8f0;
}

.btn-delete {
    background: #ef4444;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
}

.btn-back {
    display: inline-block;
    margin-top: 25px;
    text-decoration: none;
    color: #334155;
    font-weight: 600;
}
</style>
</head>
<body>

<div class="container">

    <h2>Lista de Docentes</h2>
    <p>Gestión general de docentes registrados</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>DNI</th>
                <th>Correo</th>
                <th>Fecha nacimiento</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody>

        <?php while ($docente = mysqli_fetch_assoc($result)) : ?>

            <tr>
                <td><?php echo $docente['id']; ?></td>

                <td>
                    <?php echo $docente['nombre'] . ' ' . $docente['apellido']; ?>
                </td>

                <td><?php echo $docente['dni']; ?></td>

                <td><?php echo $docente['correo']; ?></td>

                <td><?php echo $docente['fecha_nacimiento']; ?></td>

                <td>
                    <a 
                        href="../controllers/eliminarDocente.php?id=<?php echo $docente['id']; ?>"
                        class="btn-delete"
                        onclick="return confirm('¿Eliminar este docente?')"
                    >
                        Eliminar
                    </a>
                </td>
            </tr>

        <?php endwhile; ?>

        </tbody>
    </table>

    <a href="dashboard_admin.php" class="btn-back">
        ← Volver al panel administrador
    </a>

</div>

</body>
</html>