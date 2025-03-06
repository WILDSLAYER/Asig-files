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
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $nombreArchivo = basename($archivo['name']);
        $ruta = $uploadDir . $nombreArchivo;

        // Validar extensión del archivo
        $extensionesPermitidas = [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'odt',
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg',
            'zip', 'rar', '7z', 'tar', 'gz',
            'mp3', 'wav', 'ogg',
            'mp4', 'avi', 'mkv', 'mov'
        ];
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionesPermitidas)) {
            return "Extensión no permitida: " . $extension;
        }

        // Verificar si el archivo ya está asignado al usuario
        $query = "SELECT COUNT(*) as total FROM archivos WHERE usuario_id = :usuario_id AND nombre_archivo = :nombre_archivo";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre_archivo', $nombreArchivo, PDO::PARAM_STR);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        if ($total > 0) {
            return "El archivo ya está asignado a este usuario.";
        }

        if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
            $query = "INSERT INTO archivos (usuario_id, nombre_archivo, ruta, fecha_subida) 
                      VALUES (:usuario_id, :nombre_archivo, :ruta, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_archivo', $nombreArchivo, PDO::PARAM_STR);
            $stmt->bindParam(':ruta', $ruta, PDO::PARAM_STR);
            
            return $stmt->execute() ? "Archivo asignado correctamente." : "Error al guardar en la base de datos.";
        } else {
            return "Error al mover el archivo.";
        }
    }

    // Obtener historial de archivos con filtros y paginación
    public function obtenerHistorial($usuario_id = null, $filtroNombreArchivo = null, $filtroFecha = null, $filtroNombreUsuario = null, $limit, $offset) {
        $sql = "SELECT archivos.*, usuarios.nombre AS nombre_usuario 
                FROM archivos 
                JOIN usuarios ON archivos.usuario_id = usuarios.id 
                WHERE 1=1";
    
        if ($usuario_id) {
            $sql .= " AND archivos.usuario_id = :usuario_id";
        }
        if ($filtroNombreArchivo) {
            $sql .= " AND archivos.nombre_archivo LIKE :filtroNombreArchivo";
        }
        if ($filtroFecha) {
            $sql .= " AND DATE(archivos.fecha_subida) = :filtroFecha";
        }
        if ($filtroNombreUsuario) {
            $sql .= " AND usuarios.nombre LIKE :filtroNombreUsuario";
        }

        $sql .= " ORDER BY archivos.fecha_subida DESC LIMIT :limit OFFSET :offset";
    
        $stmt = $this->db->prepare($sql);

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
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener número total de archivos considerando filtros
    public function getTotalFiles($filtroNombreArchivo = null, $filtroFecha = null, $filtroNombreUsuario = null) {
        $sql = "SELECT COUNT(*) as total FROM archivos 
                JOIN usuarios ON archivos.usuario_id = usuarios.id 
                WHERE 1=1";
        
        if ($filtroNombreArchivo) {
            $sql .= " AND archivos.nombre_archivo LIKE :filtroNombreArchivo";
        }
        if ($filtroFecha) {
            $sql .= " AND DATE(archivos.fecha_subida) = :filtroFecha";
        }
        if ($filtroNombreUsuario) {
            $sql .= " AND usuarios.nombre LIKE :filtroNombreUsuario";
        }

        $stmt = $this->db->prepare($sql);

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
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Eliminar la asignación de un archivo
    public function eliminarAsignacion($archivo_id) {
        $this->db->beginTransaction();
        try {
            $query = "SELECT ruta FROM archivos WHERE id = :archivo_id FOR UPDATE";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':archivo_id', $archivo_id, PDO::PARAM_INT);
            $stmt->execute();
            $archivo = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if ($archivo) {
                $ruta = $archivo['ruta'];

                $query = "DELETE FROM archivos WHERE id = :archivo_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':archivo_id', $archivo_id, PDO::PARAM_INT);
                $stmt->execute();

                $query = "SELECT COUNT(*) as total FROM archivos WHERE ruta = :ruta";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':ruta', $ruta, PDO::PARAM_STR);
                $stmt->execute();
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                if ($total == 0 && file_exists($ruta)) {
                    unlink($ruta);
                }
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Manejar la subida de archivos
    public function handleFileUpload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
            $usuario_id = $_POST['usuario_id'];
            $archivo = $_FILES['archivo'];
            try {
                return $this->asignarArchivo($usuario_id, $archivo);
            } catch (Exception $e) {
                return "Error al asignar el archivo: " . $e->getMessage();
            }
        }
        return null;
    }

}

// Manejo de acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    $fileController = new FileController();

    // Manejar la subida de archivos
    if (isset($_POST['action']) && $_POST['action'] === 'upload') {
        $mensaje = $fileController->handleFileUpload();
        header("Location: ../views/asignar_archivo.php?mensaje=$mensaje&tipoMensaje=info");
        exit();
    }

    // Eliminar una asignación de archivo
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        if ($fileController->eliminarAsignacion($_GET['id'])) {
            header("Location: ../views/asignar_archivo.php?mensaje=Asignación eliminada correctamente&tipoMensaje=success");
        } else {
            header("Location: ../views/asignar_archivo.php?mensaje=Error al eliminar la asignación&tipoMensaje=error");
        }
        exit();
    }

}
