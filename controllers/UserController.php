<?php
require_once __DIR__ . '/../config/Database.php';

class UserController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection(); // Obtén la conexión a la base de datos
    }

    // Obtener todos los usuarios
    public function getAllUsers() {
        $query = "SELECT * FROM usuarios";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function createUser($nombre, $email, $password, $rol) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO usuarios (nombre, username, email, password, rol) VALUES (:nombre, :username, :email, :password, :rol)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':username', $nombre); // Asume que el username es igual al nombre
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':rol', $rol);
        return $stmt->execute();
    }

    // Actualizar un usuario
    public function updateUser($id, $nombre, $email, $rol) {
        $query = "UPDATE usuarios SET nombre = :nombre, username = :username, email = :email, rol = :rol WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':username', $nombre); // Asume que el username es igual al nombre
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
        $userController->createUser($_POST['nombre'], $_POST['email'], $_POST['password'], $_POST['rol']);
        header("Location: ../views/usuarios.php"); // Redirige a la vista correcta
        exit();
    }

    // Eliminar un usuario
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $userController->deleteUser($_GET['id']);
        header("Location: ../views/usuarios.php"); // Redirige a la vista correcta
        exit();
    }

    // Actualizar un usuario
    if (isset($_GET['action']) && $_GET['action'] === 'update') {
        // Asegúrate de que los parámetros necesarios estén presentes
        if (isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['rol'])) {
            $userController->updateUser($_GET['id'], $_POST['nombre'], $_POST['email'], $_POST['rol']);
            header("Location: ../views/usuarios.php"); // Redirige a la vista correcta
            exit();
        } else {
            echo "Faltan parámetros para actualizar el usuario.";
        }
    }
}
?>