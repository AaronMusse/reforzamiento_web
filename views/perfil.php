<?php
session_start();
include("../config/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Perfil</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:Poppins;
    background:#f1f5f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

/* CARD PRINCIPAL */
.card{
    width:800px;
    background:white;
    display:flex;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

/* IZQUIERDA */
.left{
    width:35%;
    background:#1e3a8a;
    color:white;
    text-align:center;
    padding:30px;
}

.avatar{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid white;
}

.role{
    background:#3b82f6;
    display:inline-block;
    padding:5px 10px;
    border-radius:10px;
    font-size:12px;
    margin-top:10px;
}

/* DERECHA */
.right{
    width:65%;
    padding:30px;
}

h2{
    margin-top:0;
}

input{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
}

button{
    width:100%;
    padding:10px;
    background:#3b82f6;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    margin-top:10px;
    transition:0.3s;
}

button:hover{
    background:#2563eb;
    transform:scale(1.02);
}

.cancel{
    display:block;
    text-align:center;
    margin-top:10px;
    color:#ef4444;
    text-decoration:none;
    font-size:14px;
}

.upload{
    font-size:12px;
    color:#555;
}
</style>
</head>

<body>

<div class="card">

    <!-- IZQUIERDA -->
    <div class="left">

        <img src="../assets/img/usuarios/<?php echo $user['foto'] ?? 'default.png'; ?>" class="avatar">

        <h3><?php echo $user['nombre']." ".$user['apellido']; ?></h3>

        <div class="role">
            <?php echo strtoupper($user['rol']); ?>
        </div>

        <p style="font-size:12px;margin-top:20px;">
            INIF 48 - Sistema Educativo
        </p>

    </div>

    <!-- DERECHA -->
    <div class="right">

        <h2>Mi Perfil</h2>

        <form method="POST" action="../controllers/update_perfil.php" enctype="multipart/form-data">

            <input type="text" name="nombre" value="<?php echo $user['nombre']; ?>" required>

            <input type="text" name="apellido" value="<?php echo $user['apellido']; ?>" required>

            <input type="email" value="<?php echo $user['correo']; ?>" disabled>

            <label class="upload">Cambiar foto</label>
            <input type="file" name="foto">

            <hr>

            <input type="password" name="password_actual" placeholder="Contraseña actual" required>

            <input type="password" name="password_nueva" placeholder="Nueva contraseña" required>

            <input type="password" name="password_repetir" placeholder="Repetir nueva contraseña" required>

            <button type="submit">Actualizar Perfil</button>

            <a href="javascript:history.back()" class="cancel">← Retroceder</a>

        </form>

    </div>

</div>

</body>
</html>