# Bienvenido a Sport-FM

Esta es la tienda deportiva con los mejores productos y ofertas.

## ¿Qué contiene este sistema?

- Visualización de productos destacados y productos en rebaja.
- Carrito de compras.
- Sistema de ofertas y calificaciones.
- Panel de usuario y perfil.
- Integración con base de datos MySQL.

## Vista previa de la página principal

![Captura de pantalla de Sport-FM](ruta/a/una/captura.png)

## Estructura principal del archivo `index.php`

El archivo principal de la tienda es `index.php`, que realiza:

- Conexión a la base de datos (usando `conexion.php`).
- Consulta de productos destacados y en rebaja.
- Renderizado dinámico de productos y ofertas usando PHP.

### Ejemplo de bloque de código PHP:
```php
<?php
$query_destacados = "SELECT id, nombre, precio, imagen FROM productos LIMIT 6";
$destacados = $conn->query($query_destacados);
?>
