<?php
require_once '../config/config.php';
require_once '../controllers/FileController.php';

session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'trabajador') {
    header("Location: login.php");
    exit();
}

$fileController = new FileController();

// Parámetros de paginación
$limit = 10; // Número de archivos por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Obtener historial de archivos asignados al usuario actual con paginación
$historial = $fileController->obtenerHistorial($_SESSION['usuario_id'], null, null, null, $limit, $offset);
$totalFiles = $fileController->getTotalFiles(null, null, $_SESSION['usuario_id']);
$totalPages = ceil($totalFiles / $limit);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Archivos | Integral Salud</title>
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
                    <h1>Mis Archivos</h1>
                    <p>Archivos asignados a ti</p>
                </div>
            </div>
            
            <section>
                <h2 class="section-title"><i class="fas fa-file-alt"></i> Lista de Archivos Asignados</h2>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-file-alt"></i> Nombre del Archivo</th>
                            <th><i class="fas fa-calendar-alt"></i> Fecha de Asignación</th>
                            <th><i class="fas fa-cogs"></i> Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historial)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No tienes archivos asignados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historial as $archivo): ?>
                                <tr>
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
                        <?php endif; ?>
                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </section>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
