<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$query = "
    SELECT id, nombre, apellido, dni, correo, rol, fecha_nacimiento
    FROM usuarios
    ORDER BY id DESC
";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ver Usuarios</title>

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

.back {
    display: inline-block;
    margin-top: 20px;
}
</style>
</head>
<body>

<div class="container">

    <h2>Usuarios del Sistema</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>DNI</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Fecha nacimiento</th>
            </tr>
        </thead>

        <tbody>

        <?php while($user = mysqli_fetch_assoc($result)) : ?>

        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['nombre'] . ' ' . $user['apellido']; ?></td>
            <td><?php echo $user['dni']; ?></td>
            <td><?php echo $user['correo']; ?></td>
            <td><?php echo ucfirst($user['rol']); ?></td>
            <td><?php echo $user['fecha_nacimiento']; ?></td>
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