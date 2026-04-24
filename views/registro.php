<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro - INIF 48</title>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        height: 100vh;
        background: url('../assets/img/fondo.jpg') no-repeat center center/cover;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    body::before {
        content: "";
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        top: 0;
        left: 0;
    }

    .container {
        position: relative;
        z-index: 1;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding: 30px;
        border-radius: 20px;
        width: 380px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        animation: fadeIn 0.8s ease;
    }

    h2 {
        color: white;
        margin-bottom: 5px;
    }

    p {
        color: white;
        margin-bottom: 15px;
        font-size: 14px;
    }

    input, select {
        width: 100%;
        padding: 10px;
        margin: 6px 0;
        border-radius: 10px;
        border: none;
        outline: none;
        background: rgba(255,255,255,0.9);
    }

    .btn {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 10px;
        margin-top: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn:hover {
        transform: scale(1.03);
    }

    .registrar {
        background: #2ecc71;
        color: white;
    }

    .volver {
        display: block;
        margin-top: 10px;
        color: white;
        text-decoration: none;
        font-size: 14px;
    }

    .logo {
        width: 60px;
        margin-top: 15px;
        border-radius: 50%;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-6px); }
        100% { transform: translateY(0px); }
    }
</style>

</head>

<body>

<div class="container">

    <h2>Crear Cuenta</h2>
    <p>Registro de estudiante INIF 48</p>

    <form method="POST" action="../controllers/registroController.php">

        <input type="text" name="nombre" placeholder="Nombre completo" required>
        <input type="text" name="apellido" placeholder="Apellido completo" required>
        <input type="text" name="dni" placeholder="DNI" required maxlength="8" minlength="8" onkeypress="return event.charCode >= 48 && event.charCode <= 57">

        <input type="date" name="fecha_nacimiento" required>

        <input type="email" name="correo" placeholder="Correo electrónico" required>
        <input type="email" name="repetir_correo" placeholder="Repetir correo" required>

        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="password" name="repetir_password" placeholder="Repetir contraseña" required>

        <button type="submit" class="btn registrar">Registrarse</button>

    </form>

    <a href="../index.php" class="volver">← Volver al inicio</a>

    <img src="../assets/img/logo.jpg" class="logo">

</div>

<?php if (isset($_SESSION['error'])): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: '<?php echo $_SESSION['error']; ?>',
        confirmButtonColor: '#3498db'
    });
</script>
<?php unset($_SESSION['error']); endif; ?>

</body>
</html>