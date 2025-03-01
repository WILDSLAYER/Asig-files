<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/config.php';
require_once '../controllers/FileController.php';
require_once '../controllers/UserController.php';

$fileController = new FileController();
$userController = new UserController();

// Recibir filtros desde el formulario
$filtroNombre = $_GET['filtro_nombre'] ?? null;
$filtroFecha = $_GET['filtro_fecha'] ?? null;
$filtroNombreUsuario = $_GET['filtro_nombre_usuario'] ?? null;

// Obtener historial de archivos con filtros
$historial = $fileController->obtenerHistorial(null, $filtroNombre, $filtroFecha, $filtroNombreUsuario);

// Obtener todos los usuarios
$usuarios = $userController->getAllUsers();

// Filtrar el usuario administrador que ha iniciado sesión
$usuarios = array_filter($usuarios, function($usuario) {
    return $usuario['id'] !== $_SESSION['usuario_id'];
});

// Manejar la subida de archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $usuario_id = $_POST['usuario_id'];
    $archivo = $_FILES['archivo'];
    if ($fileController->asignarArchivo($usuario_id, $archivo)) {
        $mensaje = "Archivo asignado correctamente.";
    } else {
        $mensaje = "Error al asignar el archivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Archivos | Integral Salud</title>
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
                    <h1>Administrar Archivos</h1>
                    <p>Gestione los archivos asignados a los usuarios</p>
                </div>
                <div class="user-controls">
                    <a href="notificaciones.php" class="btn btn-outline">
                        <i class="fas fa-bell"></i> Notificaciones
                    </a>
                </div>
            </div>
            
            <section>
                <h2 class="section-title"><i class="fas fa-file-upload"></i> Asignar Archivo</h2>
                <?php if (isset($mensaje)): ?>
                    <div class="alert alert-info"><?php echo $mensaje; ?></div>
                <?php endif; ?>
                <form action="asignar_archivo.php" method="POST" enctype="multipart/form-data" class="form-container mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usuario_id"><i class="fas fa-user"></i> Seleccionar Usuario</label>
                                <select name="usuario_id" id="usuario_id" class="form-select" required>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="archivo"><i class="fas fa-file"></i> Seleccionar Archivo</label>
                                <input type="file" name="archivo" id="archivo" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Asignar Archivo
                    </button>
                </form>
                
                <h2 class="section-title"><i class="fas fa-file-alt"></i> Lista de Archivos asignados</h2>
                <form method="GET" class="form-container mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filtro_nombre" class="form-label">
                                <i class="fas fa-search"></i> Filtrar por nombre de archivo:
                            </label>
                            <input type="text" name="filtro_nombre" id="filtro_nombre" class="form-control" 
                                   value="<?php echo htmlspecialchars($filtroNombre); ?>" placeholder="Ejemplo: reporte.pdf">
                        </div>

                        <div class="col-md-4">
                            <label for="filtro_nombre_usuario" class="form-label">
                                <i class="fas fa-user"></i> Filtrar por nombre de usuario:
                            </label>
                            <input type="text" name="filtro_nombre_usuario" id="filtro_nombre_usuario" class="form-control" 
                                   value="<?php echo htmlspecialchars($filtroNombreUsuario); ?>" placeholder="Ejemplo: Juan Pérez">
                        </div>

                        <div class="col-md-4">
                            <label for="filtro_fecha" class="form-label">
                                <i class="fas fa-calendar"></i> Filtrar por fecha:
                            </label>
                            <input type="date" name="filtro_fecha" id="filtro_fecha" class="form-control" 
                                   value="<?php echo htmlspecialchars($filtroFecha); ?>">
                        </div>

                        <div class="col-md-4 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <?php if ($filtroNombre || $filtroFecha || $filtroNombreUsuario): ?>
                                <a href="asignar_archivo.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Limpiar filtros
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Nombre del Usuario</th>
                            <th><i class="fas fa-file-alt"></i> Nombre del Archivo</th>
                            <th><i class="fas fa-calendar-alt"></i> Fecha de Asignación</th>
                            <th><i class="fas fa-cogs"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historial as $archivo): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($archivo['nombre_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($archivo['nombre_archivo']); ?></td>
                                <td><?php echo htmlspecialchars($archivo['fecha_subida']); ?></td>
                                <td class="action-buttons">
                                    <a href="../public/descarga.php?archivo=<?php echo urlencode($archivo['nombre_archivo']); ?>" 
                                       class="btn btn-primary btn-sm">
                                       <i class="fas fa-download"></i> Descargar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
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
