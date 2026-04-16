<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: views/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>INIF 48</title>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #4facfe, #00f2fe);
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .container {
        background: white;
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        width: 350px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        animation: fadeIn 1s ease;
    }

    /* Animación de entrada */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .logo {
        width: 80px;
        margin-bottom: 15px;
        animation: float 3s ease-in-out infinite;
    }

    /* Animación flotante del logo */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
        100% { transform: translateY(0px); }
    }

    h1 {
        margin: 10px 0;
        color: #2c3e50;
    }

    p {
        color: #7f8c8d;
        margin-bottom: 25px;
    }

    .btn {
        display: block;
        text-decoration: none;
        margin: 10px 0;
        padding: 12px;
        border-radius: 10px;
        font-weight: bold;
        transition: 0.3s;
    }

    .login {
        background: #3498db;
        color: white;
    }

    .login:hover {
        background: #2980b9;
        transform: scale(1.05);
    }

    .registro {
        background: #2ecc71;
        color: white;
    }

    .registro:hover {
        background: #27ae60;
        transform: scale(1.05);
    }

</style>
</head>

<body>

<div class="container">

    <img src="assets/img/logo.JPG" class="logo">

    <h1>INIF 48</h1>
    <p>Sistema de Reforzamiento Académico</p>

    <a href="views/login.php" class="btn login">Iniciar Sesión</a>
    <a href="views/registro.php" class="btn registro">Registrarse</a>

</div>

</body>
</html>