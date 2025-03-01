<?php
if (isset($_GET['archivo'])) {
    $archivo = basename($_GET['archivo']); // Previene ataques de directorio
    $ruta = realpath(__DIR__ . '/../uploads/' . $archivo);

    if (file_exists($ruta)) {
        // Configurar encabezados para forzar la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $archivo . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($ruta));

        // Leer y enviar el archivo al usuario
        readfile($ruta);
        exit;
    } else {
        echo "Error: Archivo no encontrado.";
    }
} else {
    echo "Error: No se especificó un archivo.";
}
