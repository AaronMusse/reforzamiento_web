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
        height: 100vh;

        background: url('assets/img/fondo.jpg') no-repeat center center/cover;

        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    /* Capa oscura sobre la imagen */
    body::before {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        top: 0;
        left: 0;
    }

    /* Contenedor tipo glass */
    .container {
        position: relative;
        z-index: 1;

        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);

        padding: 40px;
        border-radius: 20px;
        width: 350px;
        text-align: center;

        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);

        animation: fadeIn 1s ease;
    }

    h2, p {
        color: white;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 10px;
        border: none;
        outline: none;
        background: rgba(255,255,255,0.8);
    }

    .btn {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 8px;
        margin-top: 10px;
        cursor: pointer;
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
        text-decoration: none;
        display: block;
        padding: 10px;
        margin-top: 10px;
        border-radius: 8px;
        transition: 0.3s;
    }

    .registro:hover {
        background: #27ae60;
        transform: scale(1.05);
    }

    /* Animación */
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


    /* LOGO */

.logo {
    width: 70px;
    border-radius: 50%;
}

/* Animación flotante */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
    100% { transform: translateY(0px); }
}

</style>
</head>

<body>

<div class="container">
    <h2>INIF 48</h2>
     <img src="assets/img/logo.jpg" class="logo">
    <p>Iniciar Sesión</p>

    <form method="POST" action="controllers/authController.php">
        <input type="email" name="correo" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>

        <button type="submit" class="btn login">Ingresar</button>
    </form>

    <a href="views/registro.php" class="registro">Crear cuenta</a>
</div>

</body>
</html>