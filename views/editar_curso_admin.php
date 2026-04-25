<?php
session_start();
include("../config/conexion.php");

// SOLO ADMIN
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// VALIDAR ID
if (!isset($_GET['id'])) {
    header("Location: lista_cursos_admin.php");
    exit();
}

$id = $_GET['id'];

// TRAER DATOS DEL CURSO
$stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: lista_cursos_admin.php");
    exit();
}

$curso = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Curso</title>

<style>
body {
    font-family: Arial;
    background: #f1f5f9;
}

.container {
    width: 500px;
    margin: 50px auto;
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    color: #1e3a8a;
}

input, select, textarea {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
}

button {
    width: 100%;
    padding: 10px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #2563eb;
}

a {
    display: block;
    text-align: center;
    margin-top: 10px;
    color: #ef4444;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="container">

    <h2>Editar Curso</h2>

    <form method="POST" action="../controllers/update_curso_admin.php">

        <input type="hidden" name="id" value="<?php echo $curso['id']; ?>">

        <label>Nombre del curso</label>
        <input type="text" name="nombre_curso" value="<?php echo $curso['nombre_curso']; ?>" required>

        <label>Descripción</label>
        <textarea name="descripcion" required><?php echo $curso['descripcion']; ?></textarea>

        <label>Fecha inicio</label>
        <input type="date" name="fecha_inicio" value="<?php echo $curso['fecha_inicio']; ?>" required>

        <label>Fecha fin</label>
        <input type="date" name="fecha_fin" value="<?php echo $curso['fecha_fin']; ?>" required>

        <label>Estado</label>
        <select name="estado">
            <option value="activo" <?php if($curso['estado']=="activo") echo "selected"; ?>>Activo</option>
            <option value="inactivo" <?php if($curso['estado']=="inactivo") echo "selected"; ?>>Inactivo</option>
        </select>

        <button type="submit">Guardar Cambios</button>

    </form>

    <a href="lista_cursos_admin.php">← Volver</a>

</div>

</body>
</html>