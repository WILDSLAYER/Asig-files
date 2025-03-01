<?php
require_once __DIR__ . '/../config/Database.php';

class FileController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Asignar un archivo a un usuario
    public function asignarArchivo($usuario_id, $archivo) {
        $uploadDir = __DIR__ . '/../uploads/'; // Carpeta donde se guardan los archivos
        $nombreArchivo = basename($archivo['name']);
        $ruta = $uploadDir . $nombreArchivo;

        // Validar extensión del archivo
        $extensionesPermitidas = ['pdf', 'doc', 'docx'];
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            return false; // Extensión no permitida
        }

        // Mover el archivo al servidor
        if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
            $query = "INSERT INTO archivos (usuario_id, nombre_archivo, ruta, fecha_asignacion) 
                      VALUES (:usuario_id, :nombre_archivo, :ruta, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':nombre_archivo', $nombreArchivo);
            $stmt->bindParam(':ruta', $ruta);
            return $stmt->execute();
        }
        return false;
    }

    // Obtener historial de archivos con filtros
    public function obtenerHistorial($usuario_id = null, $filtroNombreArchivo = null, $filtroFecha = null, $filtroNombreUsuario = null) {
        $sql = "SELECT archivos.*, usuarios.nombre AS nombre_usuario 
                FROM archivos 
                JOIN usuarios ON archivos.usuario_id = usuarios.id 
                WHERE 1=1"; // Permite construir la consulta dinámicamente
    
        // Aplicar filtros opcionales
        if ($usuario_id) {
            $sql .= " AND archivos.usuario_id = :usuario_id";
        }
        if ($filtroNombreArchivo) {
            $sql .= " AND archivos.nombre_archivo LIKE :filtroNombreArchivo";
        }
        if ($filtroFecha) {
            $sql .= " AND DATE(archivos.fecha_asignacion) = :filtroFecha";
        }
        if ($filtroNombreUsuario) {
            $sql .= " AND usuarios.nombre LIKE :filtroNombreUsuario"; // Filtra por nombre del usuario
        }
    
        $stmt = $this->db->prepare($sql);
    
        // Asignar valores a los parámetros
        if ($usuario_id) {
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
        }
        if ($filtroNombreArchivo) {
            $stmt->bindValue(':filtroNombreArchivo', "%$filtroNombreArchivo%", PDO::PARAM_STR);
        }
        if ($filtroFecha) {
            $stmt->bindValue(':filtroFecha', $filtroFecha, PDO::PARAM_STR);
        }
        if ($filtroNombreUsuario) {
            $stmt->bindValue(':filtroNombreUsuario', "%$filtroNombreUsuario%", PDO::PARAM_STR);
        }
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    }
?>
