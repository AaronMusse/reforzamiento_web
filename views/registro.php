<form method="POST" action="../controllers/registroController.php">
    <h2>Registro de Usuario</h2>

    <input type="text" name="nombre" placeholder="Nombre completo" required>
    <input type="email" name="correo" placeholder="Correo" required>
    <input type="password" name="password" placeholder="Contraseña" required>

    <select name="rol" required>
        <option value="">Seleccione rol</option>
        <option value="alumna">Alumna</option>
        <option value="docente">Docente</option>
    </select>

    <button type="submit">Registrarse</button>
</form>