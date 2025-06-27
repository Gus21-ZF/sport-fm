<?php
session_start();
require_once 'conexion.php'; // Archivo con la conexión a la BD

$usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';

// Obtener productos destacados (primeros 6 productos)
$query_destacados = "SELECT id, nombre, precio, imagen FROM productos LIMIT 6";
$destacados = $conn->query($query_destacados);

// Obtener productos en oferta
$query_ofertas = "SELECT p.id, p.nombre, p.precio, p.imagen, 
                  o.precio_oferta, o.fecha_inicio, o.fecha_fin 
                  FROM productos p
                  JOIN ofertas o ON p.id = o.producto_id
                  WHERE o.fecha_inicio <= CURDATE() AND o.fecha_fin >= CURDATE()
                  LIMIT 4";
$ofertas = $conn->query($query_ofertas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sport-FM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <!-- ... (todo el HTML tal como lo tienes, incluyendo los bloques PHP integrados para los productos) ... -->
</body>
</html>
