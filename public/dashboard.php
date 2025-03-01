<?php
require_once '../config/config.php'; 

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php"); // Corregido
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Integral Salud</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/styles.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">

        <?php require __DIR__ . '/../views/sidebar.php'; ?> <!-- Corregido -->
        <!-- Main Content -->
        <div class="main-content">
            
            <div class="page-header">
                <div class="welcome-text">
                    <h1>Panel de Control</h1>
                    <p>Bienvenido al sistema de gestión de Integral Salud</p>
                </div>
                <div class="user-controls">
                    <a href="notificaciones.php" class="btn btn-outline"> <!-- Corregido -->
                        <i class="fas fa-bell"></i> Notificaciones
                    </a>
                </div>
            </div>
            
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Usuarios</h3>
                    <p>Gestiona usuarios y permisos del sistema</p>
                    <a href="usuarios.php" class="btn btn-primary">Administrar</a> <!-- Corregido -->
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Archivos</h3>
                    <p>Administra documentos y expedientes</p>
                    <a href="archivos.php" class="btn btn-primary">Administrar</a> <!-- Corregido -->
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Reportes</h3>
                    <p>Visualiza estadísticas y genera informes</p>
                    <a href="reportes.php" class="btn btn-primary">Ver reportes</a> <!-- Corregido -->
                </div>
                
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Citas</h3>
                    <p>Gestiona el calendario de citas médicas</p>
                    <a href="citas.php" class="btn btn-primary">Gestionar</a> <!-- Corregido -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>