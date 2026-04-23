<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'docente') {
    header("Location: ../index.php");
    exit();
}

$curso_id = $_GET['id'] ?? null;
if (!$curso_id) { header("Location: cursos.php"); exit(); }

// Obtener info del curso
$stmt = $conn->prepare("SELECT * FROM cursos WHERE id = ? AND docente_id = ?");
$stmt->bind_param("ii", $curso_id, $_SESSION['user_id']);
$stmt->execute();
$curso = $stmt->get_result()->fetch_assoc();

if (!$curso) { header("Location: cursos.php"); exit(); }

// Manejo de acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add_unidad') {
            $titulo = $_POST['titulo'];
            $stmt = $conn->prepare("INSERT INTO unidades (curso_id, titulo) VALUES (?, ?)");
            $stmt->bind_param("is", $curso_id, $titulo);
            $stmt->execute();
        } elseif ($_POST['action'] == 'add_sesion') {
            $unidad_id = $_POST['unidad_id'];
            $titulo = $_POST['titulo'];
            $contenido = $_POST['contenido'];
            $stmt = $conn->prepare("INSERT INTO sesiones (unidad_id, titulo, contenido) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $unidad_id, $titulo, $contenido);
            $stmt->execute();
        } elseif ($_POST['action'] == 'add_material') {
            $sesion_id = $_POST['sesion_id'];
            $titulo = $_POST['titulo'];
            $tipo = $_POST['tipo'];
            $ruta = "";

            if ($tipo == 'archivo') {
                if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
                    $target_dir = "../assets/materiales/";
                    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
                    $filename = uniqid() . "_" . basename($_FILES["archivo"]["name"]);
                    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $target_dir . $filename)) {
                        $ruta = $filename;
                    }
                }
            } else {
                $ruta = $_POST['enlace'];
            }

            if ($ruta != "") {
                $stmt = $conn->prepare("INSERT INTO materiales (sesion_id, titulo, tipo, ruta) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $sesion_id, $titulo, $tipo, $ruta);
                $stmt->execute();
            }
        }
    }
}

// Obtener unidades y sesiones
$unidades = [];
$res_unidades = $conn->query("SELECT * FROM unidades WHERE curso_id = $curso_id ORDER BY orden ASC, id ASC");
while($u = $res_unidades->fetch_assoc()) {
    $u_id = $u['id'];
    $res_sesiones = $conn->query("SELECT * FROM sesiones WHERE unidad_id = $u_id ORDER BY orden ASC, id ASC");
    $sesiones = [];
    while($s = $res_sesiones->fetch_assoc()) {
        $s_id = $s['id'];
        $res_mat = $conn->query("SELECT * FROM materiales WHERE sesion_id = $s_id");
        $s['materiales'] = $res_mat->fetch_all(MYSQLI_ASSOC);
        $sesiones[] = $s;
    }
    $u['sesiones'] = $sesiones;
    $unidades[] = $u;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($curso['nombre_curso']); ?> - Panel Docente</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #1e3a8a; --secondary-color: #3b82f6; --bg-color: #f8fafc; --white: #ffffff; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-color); margin: 0; display: flex; }
        .sidebar { width: 260px; height: 100vh; background: #0f172a; color: white; position: fixed; }
        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        .unidad-item { border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; background: white; overflow: hidden; }
        .unidad-header { background: #f8fafc; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
        .sesion-item { padding: 15px 20px; border-bottom: 1px solid #f1f5f9; }
        .sesion-item:last-child { border-bottom: none; }
        .material-tag { display: inline-flex; align-items: center; background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 12px; margin-top: 10px; margin-right: 10px; color: #475569; }
        .btn { padding: 8px 15px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; text-decoration: none; font-size: 13px; }
        .btn-primary { background: var(--secondary-color); color: white; }
        .btn-outline { border: 1px solid #e2e8f0; color: var(--text-color); background: white; }
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 30px; border-radius: 20px; width: 450px; }
        input, textarea, select { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div style="padding: 20px; text-align: center;">
            <h3>INIF 48</h3>
            <a href="cursos.php" style="color: #94a3b8; text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver a Cursos</a>
        </div>
    </nav>

    <main class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1><?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>
                <p style="color: #64748b;">Gestión de contenido, materiales y sesiones</p>
            </div>
            <button class="btn btn-primary" onclick="openUnidadModal()"><i class="fas fa-plus"></i> Nueva Unidad</button>
        </div>

        <?php foreach($unidades as $u): ?>
            <div class="unidad-item">
                <div class="unidad-header">
                    <h3 style="margin:0;"><i class="fas fa-folder-open" style="color: var(--secondary-color); margin-right: 10px;"></i> <?php echo htmlspecialchars($u['titulo']); ?></h3>
                    <button class="btn btn-outline" onclick="openSesionModal(<?php echo $u['id']; ?>, '<?php echo addslashes($u['titulo']); ?>')">
                        <i class="fas fa-plus"></i> Nueva Sesión
                    </button>
                </div>
                <div class="sesiones-list">
                    <?php if(count($u['sesiones']) > 0): ?>
                        <?php foreach($u['sesiones'] as $s): ?>
                            <div class="sesion-item">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div>
                                        <h4 style="margin:0 0 5px 0;"><i class="fas fa-file-alt" style="color: #94a3b8; margin-right: 10px;"></i> <?php echo htmlspecialchars($s['titulo']); ?></h4>
                                        <p style="margin:0; font-size: 13px; color: #64748b;"><?php echo htmlspecialchars($s['contenido']); ?></p>
                                        
                                        <div class="materiales-box">
                                            <?php foreach($s['materiales'] as $m): ?>
                                                <span class="material-tag">
                                                    <i class="fas <?php echo $m['tipo'] == 'archivo' ? 'fa-paperclip' : 'fa-link'; ?>" style="margin-right: 5px;"></i>
                                                    <?php echo htmlspecialchars($m['titulo']); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline" style="font-size: 11px;" onclick="openMaterialModal(<?php echo $s['id']; ?>, '<?php echo addslashes($s['titulo']); ?>')">
                                        <i class="fas fa-upload"></i> Subir Material
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="padding: 15px 20px; color: #94a3b8; font-style: italic; margin: 0;">No hay sesiones en esta unidad.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </main>

    <!-- Modal Unidad -->
    <div id="modalUnidad" class="modal">
        <div class="modal-content">
            <h2>Nueva Unidad</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_unidad">
                <label>Título de la Unidad</label>
                <input type="text" name="titulo" required placeholder="Ej: Unidad 1: Introducción">
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar</button>
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalUnidad')" style="flex: 1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Sesión -->
    <div id="modalSesion" class="modal">
        <div class="modal-content">
            <h2>Nueva Sesión en <span id="unidadNombre"></span></h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_sesion">
                <input type="hidden" name="unidad_id" id="unidadIdInput">
                <label>Título de la Sesión</label>
                <input type="text" name="titulo" required placeholder="Ej: Sesión 1: Conceptos Básicos">
                <label>Contenido / Breve descripción</label>
                <textarea name="contenido" rows="3"></textarea>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar</button>
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalSesion')" style="flex: 1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Material -->
    <div id="modalMaterial" class="modal">
        <div class="modal-content">
            <h2>Subir Material a <span id="sesionNombre"></span></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_material">
                <input type="hidden" name="sesion_id" id="sesionIdInput">
                
                <label>Título del Material</label>
                <input type="text" name="titulo" required placeholder="Ej: Lectura Obligatoria">
                
                <label>Tipo de Material</label>
                <select name="tipo" id="tipoMaterial" onchange="toggleMaterialInput()">
                    <option value="archivo">Archivo (PDF, Imagen, etc)</option>
                    <option value="enlace">Enlace Externo (YouTube, Drive, etc)</option>
                </select>

                <div id="inputArchivo">
                    <label>Seleccionar Archivo</label>
                    <input type="file" name="archivo">
                </div>

                <div id="inputEnlace" style="display:none;">
                    <label>URL del Enlace</label>
                    <input type="url" name="enlace" placeholder="https://...">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Subir Material</button>
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalMaterial')" style="flex: 1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUnidadModal() { document.getElementById('modalUnidad').style.display = 'flex'; }
        function openSesionModal(id, nombre) {
            document.getElementById('unidadIdInput').value = id;
            document.getElementById('unidadNombre').innerText = nombre;
            document.getElementById('modalSesion').style.display = 'flex';
        }
        function openMaterialModal(id, nombre) {
            document.getElementById('sesionIdInput').value = id;
            document.getElementById('sesionNombre').innerText = nombre;
            document.getElementById('modalMaterial').style.display = 'flex';
        }
        function closeModal(id) { document.getElementById(id).style.display = 'none'; }
        
        function toggleMaterialInput() {
            const tipo = document.getElementById('tipoMaterial').value;
            document.getElementById('inputArchivo').style.display = tipo === 'archivo' ? 'block' : 'none';
            document.getElementById('inputEnlace').style.display = tipo === 'enlace' ? 'block' : 'none';
        }
    </script>
</body>
</html>
