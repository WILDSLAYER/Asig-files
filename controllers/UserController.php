<?php
require_once __DIR__ . '/../config/Database.php';

class UserController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection(); // Obtén la conexión a la base de datos
    }

    // Obtener todos los usuarios con paginación
    public function getAllUsers($limit = null, $offset = null) {
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

    // Obtener el número total de usuarios
    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Obtener un usuario por su ID
    public function getUserById($id) {
        $query = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un usuario
    public function createUser($nombre, $username, $email, $password, $rol) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO usuarios (nombre, username, email, password, rol) VALUES (:nombre, :username, :email, :password, :rol)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':rol', $rol);
        return $stmt->execute();
    }

    // Actualizar un usuario
    public function updateUser($id, $nombre, $username, $email, $rol, $password = null) {
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE usuarios SET nombre = :nombre, username = :username, email = :email, password = :password, rol = :rol WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            $query = "UPDATE usuarios SET nombre = :nombre, username = :username, email = :email, rol = :rol WHERE id = :id";
            $stmt = $this->db->prepare($query);
        }
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':rol', $rol);
        return $stmt->execute();
    }

    // Eliminar un usuario
    public function deleteUser($id) {
        $query = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

// Manejo de acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    $userController = new UserController();

    // Crear un usuario
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $userController->createUser($_POST['nombre'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['rol']);
        header("Location: ../public/usuarios.php"); // Redirige a la vista correcta
        exit();
    }

    // Eliminar un usuario
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $userController->deleteUser($_GET['id']);
        header("Location: ../public/usuarios.php"); // Redirige a la vista correcta
        exit();
    }

    // Actualizar un usuario
    if (isset($_GET['action']) && $_GET['action'] === 'update') {
        // Asegúrate de que los parámetros necesarios estén presentes
        if (isset($_POST['nombre']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['rol'])) {
            $password = isset($_POST['password']) ? $_POST['password'] : null;
            $userController->updateUser($_GET['id'], $_POST['nombre'], $_POST['username'], $_POST['email'], $_POST['rol'], $password);
            header("Location: ../public/usuarios.php"); // Redirige a la vista correcta
            exit();
        } else {
            echo "Faltan parámetros para actualizar el usuario.";
        }
    }
}
?>