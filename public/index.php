<?php 
require_once '../config/config.php'; 

session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php"); // Cambiado a ruta relativa
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/styles.css">
</head>
<body>
    <div class="logo-container">
        <img src="<?php echo BASE_URL; ?>/public/assets/images/logo.webp" alt="Integral Salud" class="logo">
        <div class="company-name">INTEGRAL SALUD</div>
        <div class="company-slogan">Seguridad y salud en el trabajo de Urabá</div>
    </div>
    
    <form action="../controllers/AuthController.php" method="POST">
        <label for="username">Nombre de usuario</label>
        <input type="text" name="username" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Ingresar</button>
    </form>


    <style>
        /* Base styles */
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #f0f9ff 0%, #d4f0f9 100%);
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    color: #004166;
}

h2 {
    color: #004166;
    text-align: center;
    margin-bottom: 20px;
    font-size: 28px;
}

form {
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    border-top: 5px solid #3EB5B0;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #004166;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type="text"]:focus,
input[type="password"]:focus {
    border-color: #3EB5B0;
    outline: none;
    box-shadow: 0 0 5px rgba(62, 181, 176, 0.3);
}

button {
    background-color: #3EB5B0;
    color: white;
    border: none;
    padding: 12px 20px;
    width: 100%;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #004166;
}

/* Logo styles */
.logo-container {
    text-align: center;
    margin-bottom: 20px;
}

.logo {
    max-width: 200px;
    height: auto;
}

/* Add this to your HTML above the form */
.company-name {
    color: #004166;
    font-size: 18px;
    margin-bottom: 5px;
    font-weight: bold;
}

.company-slogan {
    color: #3EB5B0;
    font-size: 14px;
    margin-bottom: 20px;
}

/* Responsive adjustments */
@media (max-width: 480px) {
    form {
        width: 90%;
        padding: 20px;
    }
}
    </style>
</body>
</html>