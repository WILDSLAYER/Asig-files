<?php
require_once '../config/config.php';
require_once '../controllers/UserController.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Obtener todos los usuarios
$userController = new UserController();
$users = $userController->getAllUsers();

// Filtrar el usuario administrador que ha iniciado sesión
$users = array_filter($users, function($user) {
    return $user['id'] !== $_SESSION['usuario_id'];
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios | Integral Salud</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/styles.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <?php require __DIR__ . '/../views/sidebar.php'; ?>
        <main class="main-content">
            <div class="page-header">
                <div class="welcome-text">
                    <h1>Administrar Usuarios</h1>
                    <p>Gestione los accesos a Asig-files</p>
                </div>
                <div class="user-controls">
                    <a href="notificaciones.php" class="btn btn-outline">
                        <i class="fas fa-bell"></i> Notificaciones
                    </a>
                </div>
            </div>
            
            <section>
                <h2 class="section-title"><i class="fas fa-user-plus"></i> Crear Usuario</h2>
                <form action="../controllers/UserController.php" method="POST" class="form-container">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre"><i class="fas fa-user"></i> Nombre completo</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre completo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username"><i class="fas fa-user"></i> Usuario</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Usuario" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Correo</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="correo@ejemplo.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                                <input type="text" name="password" id="password" class="form-control" placeholder="Contraseña segura" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rol"><i class="fas fa-user-tag"></i> Rol</label>
                                <select name="rol" id="rol" class="form-select">
                                    <option value="" disabled selected>Seleccione un rol</option>
                                    <option value="trabajador">Trabajador</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Crear Usuario
                    </button>
                </form>
                
                <h2 class="section-title"><i class="fas fa-users"></i> Lista de Usuarios</h2>
                <div class="table-responsive">
                    <table class="table table-bordered custom-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-user"></i> Nombre</th>
                                <th><i class="fas fa-user"></i> Usuario</th>
                                <th><i class="fas fa-envelope"></i> Correo</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['nombre']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td class="action-buttons">
                                        <button type="button" class="btn btn-outline btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="<?php echo $user['id']; ?>">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <a href="../controllers/UserController.php?action=delete&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- El contenido del modal se cargará aquí -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editUserModal = document.getElementById('editUserModal');
            editUserModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-id');
                
                // Cargar el contenido del archivo edit_user.php en el modal
                const modalBody = editUserModal.querySelector('.modal-body');
                fetch(`../views/edit_user.php?id=${userId}`)
                    .then(response => response.text())
                    .then(html => {
                        modalBody.innerHTML = html;
                    });
            });

            // Opcional: Agregar algún JavaScript para mejorar la experiencia de usuario
            const rows = document.querySelectorAll('.custom-table tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseover', function() {
                    this.style.backgroundColor = 'rgba(62, 181, 176, 0.1)';
                });
                row.addEventListener('mouseout', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
</body>
</html>