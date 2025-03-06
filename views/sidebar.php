<?php

if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    // Vista para el administrador
    ?>
    <!-- Sidebar -->
     
    <div class="sidebar">
        <div class="logo-container">
            <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.webp" alt="Integral Salud" class="logo">
            <div class="company-name">ASIG-FILES</div>
        </div>
        
        <div class="user-info">
            <h3>Bienvenido</h3>
            <div class="user-role">
                <i class="fas fa-user-shield"></i>
                <?php echo $_SESSION['rol']; ?>
            </div>
            <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
        
        <nav class="nav-menu">
            <a href="../public/dashboard.php">
                <i class="fas fa-home"></i> Inicio
            </a>
            <a href="../public/usuarios.php">
                <i class="fas fa-users"></i> Administrar Usuarios
            </a>
            <a href="/Asig-files/views/asignar_archivo.php">
                <i class="fas fa-file-alt"></i> Administrar Archivos
            </a>
        </nav>
    </div>
    <?php
} elseif (isset($_SESSION['rol']) && $_SESSION['rol'] === 'trabajador') {
    // Vista para el trabajador
    ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo-container">
            <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.webp" alt="Integral Salud" class="logo">
            <div class="company-name">ASIG-FILES</div>
        </div>
        
        <div class="user-info">
            <h3>Bienvenido</h3>
            <div class="user-role">
                <i class="fas fa-user-shield"></i>
                <?php echo $_SESSION['rol']; ?>
            </div>
            <a href="logout.php" class="btn btn-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
        
        <nav class="nav-menu">
            <a href="dashboard.php">
                <i class="fas fa-home"></i> Inicio
            </a>
            <a href="../public/mis_archivos.php">
                <i class="fas fa-file-alt"></i> Mis Archivos
            </a>
        </nav>
    </div>
    <?php
} else {
    // Si no hay sesión iniciada o el rol no es válido, redirigir al login
    header("Location: login.php");
    exit();
}
?>
