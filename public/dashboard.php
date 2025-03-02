<?php
require_once '../config/config.php';
require_once '../controllers/DashboardController.php';

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$dashboardController = new DashboardController();
$totalUsers = $dashboardController->getTotalUsersExcludingCurrent($_SESSION['usuario_id']);
$totalFiles = $dashboardController->getTotalFiles();
$latestUsers = $dashboardController->getLatestUsersExcludingCurrent($_SESSION['usuario_id']);
$latestFiles = $dashboardController->getLatestFiles();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Asig-files</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php require __DIR__ . '/../views/sidebar.php'; ?>
        <div class="main-content">
            <div class="page-header">
                <div class="welcome-text">
                    <h1>Panel de Control</h1>
                    <p>Bienvenido a Asig-files</p>
                </div>
            </div>
            
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Usuarios</h3>
                    <p>Total: <?php echo $totalUsers; ?></p>
                    <a href="usuarios.php" class="btn btn-primary">Administrar</a>
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Archivos</h3>
                    <p>Total: <?php echo $totalFiles; ?></p>
                    <a href="archivos.php" class="btn btn-primary">Administrar</a>
                </div>
            </div>

            <div class="latest-section">
                <h2 class="section-title"><i class="fas fa-user-clock"></i> Últimos Usuarios Registrados</h2>
                <ul class="latest-list">
                    <?php foreach ($latestUsers as $user): ?>
                        <li><?php echo htmlspecialchars($user['nombre']); ?> (<?php echo htmlspecialchars($user['username']); ?>)</li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="latest-section">
                <h2 class="section-title"><i class="fas fa-file-upload"></i> Últimos Archivos Subidos</h2>
                <ul class="latest-list">
                    <?php foreach ($latestFiles as $file): ?>
                        <li><?php echo htmlspecialchars($file['nombre_archivo']); ?> (<?php echo htmlspecialchars($file['nombre_usuario']); ?>)</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>