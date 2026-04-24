<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear Docente - INIF 48</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f8fafc;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.container {
    background: white;
    padding: 35px;
    border-radius: 18px;
    width: 420px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
}

h2 {
    margin-top: 0;
    color: #1e3a8a;
    text-align: center;
}

input {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #dbeafe;
    border-radius: 10px;
    box-sizing: border-box;
}

button {
    width: 100%;
    padding: 12px;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

button:hover {
    background: #1d4ed8;
}

.back {
    display: block;
    text-align: center;
    margin-top: 15px;
    text-decoration: none;
    color: #64748b;
}
</style>
</head>
<body>

<div class="container">
    <h2>Registrar Nuevo Docente</h2>

    <form method="POST" action="../controllers/docenteController.php">

        <input type="text" name="nombre" placeholder="Nombre" required>

        <input type="text" name="apellido" placeholder="Apellido" required>

        <input 
            type="text" 
            name="dni" 
            placeholder="DNI" 
            maxlength="8" 
            minlength="8"
            required
        >

        <input 
            type="date" 
            name="fecha_nacimiento" 
            required
        >

        <input 
            type="email" 
            name="correo" 
            placeholder="Correo institucional" 
            required
        >

        <input 
            type="password" 
            name="password" 
            placeholder="Contraseña" 
            required
        >

        <button type="submit">
            Crear Docente
        </button>

    </form>

    <a href="dashboard_admin.php" class="back">
        ← Volver al Panel Admin
    </a>
</div>

</body>
</html>