<?php
require_once '../config/database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        header("Location: ../public/index.php?error=Faltan datos");
        exit();
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $db = new Database(); // Aquí creamos la instancia correctamente
    $sql = "SELECT * FROM usuarios WHERE username = :username";
    $stmt = $db->prepare($sql);
    $stmt->execute(['username' => $username]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];

        if ($usuario['rol'] == 'admin') {
            $_SESSION['admin'] = true;
        }
        header("Location: ../public/dashboard.php");
        exit();
    } else {
        header("Location: ../public/index.php?error=Credenciales incorrectas");
        exit();
    }
}
?>
