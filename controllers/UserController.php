<?php
require_once __DIR__ . '/../config/Database.php';

class UserController {
    private $db;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    private function checkConnection() {
        try {
            $this->db->query('SELECT 1');
        } catch (PDOException $e) {
            if ($e->getCode() == '2006') { // MySQL server has gone away
                $this->connect();
            } else {
                throw $e;
            }
        }
    }

    // Obtener todos los usuarios con paginación
    public function getAllUsers($limit = null, $offset = null) {
        $this->checkConnection();
        $query = "SELECT * FROM usuarios";
        if ($limit !== null && $offset !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->db->prepare($query);
        if ($limit !== null && $offset !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los usuarios excluyendo al usuario actual
    public function getAllUsersExcludingCurrent($limit, $offset, $currentUserId, $search = '') {
        $this->checkConnection();
        $query = "SELECT * FROM usuarios WHERE id != :currentUserId AND (nombre LIKE :search OR username LIKE :search OR email LIKE :search)";
        if ($limit !== null && $offset !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':currentUserId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        if ($limit !== null && $offset !== null) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener el número total de usuarios excluyendo al usuario actual
    public function getTotalUsersExcludingCurrent($currentUserId, $search = '') {
        $this->checkConnection();
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE id != :currentUserId AND (nombre LIKE :search OR username LIKE :search OR email LIKE :search)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':currentUserId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Obtener el número total de usuarios
    public function getTotalUsers() {
        $this->checkConnection();
        $query = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Obtener un usuario por su ID
    public function getUserById($id) {
        $this->checkConnection();
        $query = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un usuario
    public function createUser($nombre, $username, $email, $password, $rol) {
        $this->checkConnection();
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO usuarios (nombre, username, email, password, rol) VALUES (:nombre, :username, :email, :password, :rol)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Actualizar un usuario
    public function updateUser($id, $nombre, $username, $email, $rol, $password = null) {
        $this->checkConnection();
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE usuarios SET nombre = :nombre, username = :username, email = :email, password = :password, rol = :rol WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        } else {
            $query = "UPDATE usuarios SET nombre = :nombre, username = :username, email = :email, rol = :rol WHERE id = :id";
            $stmt = $this->db->prepare($query);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Eliminar un usuario
    public function deleteUser($id) {
        $this->checkConnection();
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

// Manejo de acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    $userController = new UserController();

    // Crear un usuario
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        if ($userController->createUser($_POST['nombre'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['rol'])) {
            header("Location: ../public/usuarios.php?mensaje=Usuario creado exitosamente&tipoMensaje=success");
        } else {
            header("Location: ../public/usuarios.php?mensaje=Error al crear el usuario&tipoMensaje=error");
        }
        exit();
    }

    // Eliminar un usuario
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        if ($userController->deleteUser($_GET['id'])) {
            header("Location: ../public/usuarios.php?mensaje=Usuario eliminado exitosamente&tipoMensaje=success");
        } else {
            header("Location: ../public/usuarios.php?mensaje=Error al eliminar el usuario&tipoMensaje=error");
        }
        exit();
    }

    // Actualizar un usuario
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        if ($userController->updateUser($_POST['id'], $_POST['nombre'], $_POST['username'], $_POST['email'], $_POST['rol'], $_POST['password'])) {
            header("Location: ../public/usuarios.php?mensaje=Usuario actualizado exitosamente&tipoMensaje=success");
        } else {
            header("Location: ../public/usuarios.php?mensaje=Error al actualizar el usuario&tipoMensaje=error");
        }
        exit();
    }
}
?>