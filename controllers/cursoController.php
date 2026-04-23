<?php
require_once "../config/conexion.php";

class CursoController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarCursosDocente($docente_id) {
        $stmt = $this->conn->prepare("SELECT * FROM cursos WHERE docente_id = ?");
        $stmt->bind_param("i", $docente_id);
        $stmt->execute();
        return $stmt->get_result();
    }

public function crearCurso($nombre, $descripcion, $fecha_inicio, $fecha_fin, $docente_id, $imagen_url = 'default_curso.jpg')
{
    $stmt = $this->conn->prepare("
        INSERT INTO cursos 
        (nombre_curso, descripcion, fecha_inicio, fecha_fin, docente_id, imagen_url, estado) 
        VALUES (?, ?, ?, ?, ?, ?, 'activo')
    ");

    $stmt->bind_param("ssssis", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $docente_id, $imagen_url);

    return $stmt->execute();
}

    public function eliminarCurso($id, $docente_id) {
        $stmt = $this->conn->prepare("DELETE FROM cursos WHERE id = ? AND docente_id = ?");
        $stmt->bind_param("ii", $id, $docente_id);
        return $stmt->execute();
    }
}

// Lógica de manejo de peticiones POST
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id']) && $_SESSION['rol'] == 'docente') {
    $controller = new CursoController($conn);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] == 'crear') {
                $nombre = $_POST['nombre_curso'];
                $descripcion = $_POST['descripcion'];
                $fecha_inicio = $_POST['fecha_inicio'];
                $fecha_fin = $_POST['fecha_fin'];
                $docente_id = $_SESSION['user_id'];

                 // imagen por defecto
                 $imagen_url = 'default_curso.jpg';
                   // VALIDACIÓN DE FECHAS
    if ($fecha_fin < $fecha_inicio) {
        header("Location: ../views/cursos.php?error=La fecha final no puede ser menor que la inicial");
        exit();
    }

                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                    $target_dir = "../assets/img/cursos/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $file_extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
                    $new_filename = uniqid() . "." . $file_extension;
                    $target_file = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
                        $imagen_url = $new_filename;
                    }
                }
                
                if ($controller->crearCurso($nombre, $descripcion, $fecha_inicio, $fecha_fin, $docente_id, $imagen_url)) {
                    header("Location: ../views/cursos.php?msg=Curso creado con éxito");
                } else {
                    header("Location: ../views/cursos.php?error=Error al crear curso");
                }
            }
            
            if ($_POST['action'] == 'eliminar') {
                $id = $_POST['id'];
                $docente_id = $_SESSION['user_id'];
                
                if ($controller->eliminarCurso($id, $docente_id)) {
                    header("Location: ../views/cursos.php?msg=Curso eliminado");
                } else {
                    header("Location: ../views/cursos.php?error=Error al eliminar");
                }
            }
        }
    }
}
?>