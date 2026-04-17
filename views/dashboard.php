<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard - INIF 48</title>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #f4f6f9;
        display: flex;
    }

    /* SIDEBAR */
    .sidebar {
        width: 220px;
        height: 100vh;
        background: #2c3e50;
        color: white;
        padding: 20px;
        position: fixed;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
    }

    .sidebar a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: 0.3s;
    }

    .sidebar a:hover {
        background: #34495e;
    }

    /* MAIN */
    .main {
        margin-left: 220px;
        padding: 20px;
        width: 100%;
    }

    /* HEADER */
    .header {
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .header h3 {
        margin: 0;
    }

    .logout {
        background: #e74c3c;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
    }

    .logout:hover {
        background: #c0392b;
    }

    /* CARDS */
    .cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        transition: 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card h4 {
        margin: 0;
        color: #555;
    }

    .card p {
        font-size: 22px;
        font-weight: bold;
        margin-top: 10px;
    }

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>INIF 48</h2>

    <a href="#">📊 Dashboard</a>
    <a href="#">📚 Cursos</a>
    <a href="#">📝 Evaluaciones</a>
    <a href="#">👩‍🎓 Estudiantes</a>
    <a href="#">⚙ Configuración</a>
</div>

<!-- MAIN -->
<div class="main">

    <!-- HEADER -->
    <div class="header">
        <h3>Bienvenido, <?php echo $_SESSION['usuario']; ?> 👋</h3>
        <a href="../logout.php" class="logout">Cerrar sesión</a>
    </div>

    <!-- CARDS -->
    <div class="cards">
        <div class="card">
            <h4>Cursos</h4>
            <p>5</p>
        </div>

        <div class="card">
            <h4>Evaluaciones</h4>
            <p>3</p>
        </div>

        <div class="card">
            <h4>Alumnas</h4>
            <p>20</p>
        </div>

        <div class="card">
            <h4>Progreso</h4>
            <p>80%</p>
        </div>
    </div>

</div>

</body>
</html>