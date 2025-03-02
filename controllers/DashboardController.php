<?php
require_once __DIR__ . '/../config/Database.php';

class DashboardController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Obtener el número total de usuarios
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Obtener el número total de usuarios, excluyendo al usuario actual
    public function getTotalUsersExcludingCurrent($currentUserId) {
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE id != :currentUserId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':currentUserId', $currentUserId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Obtener el número total de archivos
    public function getTotalFiles() {
        $query = "SELECT COUNT(*) as total FROM archivos";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Obtener los últimos usuarios registrados
    public function getLatestUsers($limit = 5) {
        $query = "SELECT * FROM usuarios ORDER BY id DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener los últimos usuarios registrados, excluyendo al usuario actual
    public function getLatestUsersExcludingCurrent($currentUserId, $limit = 5) {
        $query = "SELECT * FROM usuarios WHERE id != :currentUserId ORDER BY id DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':currentUserId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener los últimos archivos subidos
    public function getLatestFiles($limit = 5) {
        $query = "SELECT archivos.*, usuarios.nombre AS nombre_usuario 
                  FROM archivos 
                  JOIN usuarios ON archivos.usuario_id = usuarios.id 
                  ORDER BY archivos.id DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
