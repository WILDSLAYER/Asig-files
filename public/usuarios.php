<?php
require_once '../config/config.php';
require_once '../controllers/UserController.php';

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Parámetros de paginación
$limit = 10; // Número de usuarios por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Recibir filtro de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener todos los usuarios con paginación, excluyendo al usuario actual
$userController = new UserController();
$users = $userController->getAllUsersExcludingCurrent($limit, $offset, $_SESSION['usuario_id'], $search);
$totalUsers = $userController->getTotalUsersExcludingCurrent($_SESSION['usuario_id'], $search);
$totalPages = ceil($totalUsers / $limit);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios | Integral Salud</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/styles.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <?php require __DIR__ . '/../views/sidebar.php'; ?>
        <main class="main-content">
            <div class="page-header">
                <div class="welcome-text">
                    <h1>Administrar Usuarios</h1>
                    <p>Gestione los accesos al sistema de Integral Salud</p>
                </div>
                <div class="user-controls">
                    <a href="notificaciones.php" class="btn btn-outline">
                        <i class="fas fa-bell"></i> Notificaciones
                    </a>
                </div>
            </div>
            
            <section>
                <h2 class="section-title"><i class="fas fa-users"></i> Lista de Usuarios</h2>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="fas fa-user-plus"></i> Crear Usuario
                    </button>
                    <div class="search-container">
                        <form class="d-flex" method="GET" action="usuarios.php">
                            <input class="form-control" type="search" name="search" placeholder="Buscar usuario" aria-label="Buscar" value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-outline-success search-button" type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> ID</th>
                            <th><i class="fas fa-user"></i> Nombre</th>
                            <th><i class="fas fa-user"></i> Usuario</th>
                            <th><i class="fas fa-envelope"></i> Correo</th>
                            <th><i class="fas fa-user-tag"></i> Rol</th>
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
                                <td>
                                    <span class="badge <?php echo $user['rol'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                        <?php echo $user['rol']; ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['id']; ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <a href="../controllers/UserController.php?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                            <!-- Modal Editar Usuario -->
                            <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editUserModalLabel<?php echo $user['id']; ?>">Editar Usuario</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="../controllers/UserController.php" method="POST" autocomplete="off">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                
                                                <div class="grupo_inputs">
                                                    <div class="form-group">
                                                        <label for="nombre<?php echo $user['id']; ?>"><i class="fas fa-user"></i> Nombre completo </label>
                                                        <input type="text" name="nombre" id="nombre<?php echo $user['id']; ?>" value="<?php echo $user['nombre']; ?>" required>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label for="username<?php echo $user['id']; ?>"><i class="fas fa-user"></i>Usuario</label>
                                                        <input type="text" name="username" id="username<?php echo $user['id']; ?>" value="<?php echo $user['username']; ?>" required>
                                                    </div>
                                                </div>
                                                
                                                <div class="grupo_inputs">
                                                    
                                                    <div class="form-group">
                                                        <label for="email<?php echo $user['id']; ?>"><i class="fas fa-envelope"></i> Correo:</label>
                                                        <input type="email" name="email" id="email<?php echo $user['id']; ?>" value="<?php echo $user['email']; ?>" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="password<?php echo $user['id']; ?>"><i class="fas fa-lock"></i> Contraseña:</label>
                                                        <input type="password" name="password" id="password<?php echo $user['id']; ?>" placeholder="Dejar en blanco para no cambiar" autocomplete="new-password">
                                                    </div>
                                                </div>

                                                <div class="grupo_inputs">
                                                <div class="form-group">
                                                        <label for="rol<?php echo $user['id']; ?>"><i class="fas fa-user-tag"></i> Rol:</label>
                                                        <select name="rol" id="rol<?php echo $user['id']; ?>">
                                                            <option value="trabajador" <?php echo $user['rol'] === 'trabajador' ? 'selected' : ''; ?>>Trabajador</option>
                                                            <option value="admin" <?php echo $user['rol'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Guardar Cambios
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Paginación -->
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li><a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                </ul>
            </section>
        </main>
    </div>

    <!-- Modal Crear Usuario -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../controllers/UserController.php" method="POST" autocomplete="off">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="grupo_inputs">
                            <div class="form-group">
                                <label for="nombre"><i class="fas fa-user"></i> Nombre completo </label>
                                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="username"><i class="fas fa-user"></i>Usuario</label>
                                <input type="text" name="username" id="username" required>
                            </div>
                        </div>
                        
                        <div class="grupo_inputs">
                            
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Correo:</label>
                                <input type="email" name="email" id="email" placeholder="correo@ejemplo.com" required>
                            </div>

                            <div class="form-group">
                                <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
                                <input type="password" name="password" id="password" placeholder="Contraseña segura" autocomplete="new-password" required>
                            </div>
                        </div>

                        <div class="grupo_inputs">
                        <div class="form-group">
                                <label for="rol"><i class="fas fa-user-tag"></i> Rol:</label>
                                <select name="rol" id="rol">
                                    <option value="trabajador">Trabajador</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Crear Usuario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Opcional: Agregar algún JavaScript para mejorar la experiencia de usuario
        document.addEventListener('DOMContentLoaded', function() {
            // Destacar la fila cuando el mouse está sobre ella
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