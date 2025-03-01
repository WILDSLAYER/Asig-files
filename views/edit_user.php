<?php
require_once '../config/config.php';
require_once '../controllers/UserController.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$userController = new UserController();
$user = $userController->getUserById($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userController->updateUser($_POST['id'], $_POST['nombre'], $_POST['email'], $_POST['rol']);
    header("Location: ../public/usuarios.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Usuario</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo $user['nombre']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Usuario:</label>
                <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
            <div class="mb-3">
                <label for="email" class="form-label">Correo:</label>
                <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select name="rol" class="form-select">
                    <option value="trabajador" <?php echo $user['rol'] === 'trabajador' ? 'selected' : ''; ?>>Trabajador</option>
                    <option value="admin" <?php echo $user['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>