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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #f8fafc;
}

.container {
    max-width: 700px;
    margin: 40px auto;
    background: white;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

h2 {
    margin-top: 0;
    color: #1e3a8a;
}

p {
    color: #64748b;
    margin-bottom: 25px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

input {
    width: 100%;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    outline: none;
    font-size: 14px;
}

.full {
    grid-column: span 2;
}

.btn {
    margin-top: 20px;
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: #3b82f6;
    color: white;
    font-weight: bold;
    cursor: pointer;
    font-size: 15px;
}

.btn:hover {
    background: #2563eb;
}

.back {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #334155;
    font-weight: 600;
}
</style>
</head>
<body>

<div class="container">

    <h2>Registrar Nuevo Docente</h2>
    <p>Solo el administrador puede crear cuentas de docentes</p>

    <form method="POST" action="../controllers/docenteController.php">

        <div class="form-grid">

            <input type="text" name="nombre" placeholder="Nombre" required>

            <input type="text" name="apellido" placeholder="Apellido" required>

            <input 
                type="text" 
                name="dni" 
                placeholder="DNI" 
                maxlength="8"
                minlength="8"
                required
                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
            >

            <input type="date" name="fecha_nacimiento" required>

            <input 
                type="email" 
                name="correo" 
                placeholder="Correo electrónico" 
                class="full"
                required
            >

            <input 
                type="password" 
                name="password" 
                placeholder="Contraseña"
                class="full"
                required
            >

        </div>

        <button type="submit" class="btn">
            Crear Docente
        </button>

    </form>

    <a href="dashboard_admin.php" class="back">
        ← Volver al panel administrador
    </a>

</div>

<?php if (isset($_SESSION['error'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '<?php echo $_SESSION['error']; ?>'
});
</script>
<?php unset($_SESSION['error']); endif; ?>

<?php if (isset($_SESSION['success'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Éxito',
    text: '<?php echo $_SESSION['success']; ?>'
});
</script>
<?php unset($_SESSION['success']); endif; ?>

</body>
</html>