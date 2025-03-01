<?php
class Database {
    private $pdo;

    public function __construct() {
        $host = 'localhost';
        $dbname = 'integral_salud';
        $username = 'root'; // Cambia esto si tienes otro usuario
        $password = ''; // Cambia esto si tienes una contraseña

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

     // Método para obtener la conexión (SOLUCIÓN)
     public function getConnection() {
        return $this->pdo;
    }
    
    // Método para preparar consultas
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
}
?>
