<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$nombre_admin = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - INIF 48</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --primary-color: #1e3a8a;
    --secondary-color: #3b82f6;
    --bg-color: #f8fafc;
    --sidebar-color: #0f172a;
    --white: #ffffff;
    --text-color: #334155;
}

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: var(--bg-color);
    display: flex;
}

/* SIDEBAR */
.sidebar {
    width: 260px;
    height: 100vh;
    background: var(--sidebar-color);
    color: var(--white);
    position: fixed;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.logo-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 2px solid var(--secondary-color);
    margin-bottom: 10px;
}

.sidebar-menu {
    flex: 1;
    padding: 20px 15px;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    color: #94a3b8;
    text-decoration: none;
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 5px;
    transition: 0.2s;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: rgba(255,255,255,0.1);
    color: var(--white);
}

.sidebar-menu a i {
    margin-right: 12px;
    width: 20px;
    text-align: center;
}

/* MAIN */
.main-content {
    flex: 1;
    margin-left: 260px;
    padding: 30px;
}

.topbar {
    background: white;
    padding: 20px 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.card h3 {
    margin-top: 0;
    color: var(--primary-color);
}

.quick-btn {
    display: inline-block;
    margin-top: 15px;
    background: var(--secondary-color);
    color: white;
    padding: 10px 18px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
}
</style>
</head>
<body>

<nav class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/img/logob.jpg" class="logo-img" alt="Logo">
        <h3>INIF 48</h3>
        <small>Panel Administrador</small>
    </div>

    <div class="sidebar-menu">
        <a href="dashboard_admin.php" class="active">
            <i class="fas fa-home"></i> Inicio
        </a>

        <a href="crear_docente.php">
            <i class="fas fa-user-plus"></i> Crear Docente
        </a>

        <a href="lista_docentes.php">
            <i class="fas fa-users"></i> Docentes
        </a>

        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i> Salir
        </a>
    </div>
</nav>

<main class="main-content">

    <div class="topbar">
        <div>
            <h2 style="margin:0;">Bienvenido, <?php echo $nombre_admin; ?></h2>
            <p style="margin:5px 0 0 0; color:#64748b;">
                Panel de administración general
            </p>
        </div>

        <a href="../logout.php" style="
            background:#fee2e2;
            color:#ef4444;
            padding:10px 16px;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
        ">
            Salir
        </a>
    </div>

    <div class="card-grid">

        <div class="card">
            <h3><i class="fas fa-user-plus"></i> Registrar Docente</h3>
            <p>Crear nuevas cuentas para docentes del sistema.</p>
            <a href="crear_docente.php" class="quick-btn">
                Ir ahora
            </a>
        </div>

        <div class="card">
            <h3><i class="fas fa-users"></i> Gestionar Docentes</h3>
            <p>Visualizar y administrar docentes registrados.</p>
            <a href="lista_docentes.php" class="quick-btn">
                Ver lista
            </a>
        </div>

    </div>

</main>

</body>
</html>