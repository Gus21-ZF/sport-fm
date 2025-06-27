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
    <link rel="stylesheet" href="custom.css"><!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <header>
        <div class="anuncio-barra">
            Ofertas de invierno
        </div>
        <nav>
            <div class="logo">SPORT-FM</div>
            <div class="nav-links">
                <a href="#" class="active">Inicio</a>
                <a href="productos.php">Productos</a>
                <a href="ofertas.php">Ofertas</a>
                <a href="#" id="buscar-btn">Buscar</a>
                <a href="#">Contacto</a>
                <a href="#" id="perfil-link" class="perfil-link">Perfil</a>
                <a href="carrito.php" class="cart-icon" title="Carrito">
                    <!-- Icono carrito SVG color rojo -->
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;">
                        <path d="M7 20c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm10 0c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2-.896 2 2 2zM7.334 16h9.359c.828 0 1.554-.522 1.841-1.303l3.09-7.787A1 1 0 0 0 20.7 6H6.215l-.94-2.36A1 1 0 0 0 4.333 3H2v2h1.333l3.6 9.06-1.35 2.44C5.21 16.37 5.97 17 6.834 17h12v-2h-11.5l.5-1z" fill="#e53935"/>
                    </svg>
                </a>
            </div>
        </nav>
    </header>

    <section class="hero">
        <h1>Bienvenido a Sport-FM</h1>
        <p>Tu tienda deportiva favorita con los mejores productos</p>
        <a href="productos.php" class="btn">Ver Productos</a>
    </section>

    <section class="featured-products">
        <h2>Productos Destacados</h2>
        <div class="image-grid">
            <?php while($producto = $destacados->fetch_assoc()): ?>
            <div class="carousel-item" style="position:relative;" id="producto-<?= $producto['id'] ?>">
                <?php
                    $query_cantidad = "SELECT cantidad FROM productos WHERE id = " . intval($producto['id']);
                    $res_cantidad = $conn->query($query_cantidad);
                    $cantidad = $res_cantidad ? $res_cantidad->fetch_assoc()['cantidad'] : 0;

                    $query_calif = "SELECT calificacion FROM productos_destacados WHERE producto_id = " . intval($producto['id']);
                    $res_calif = $conn->query($query_calif);
                    $calificacion = $res_calif && $res_calif->num_rows > 0 ? $res_calif->fetch_assoc()['calificacion'] : null;
                ?>
                <?php if($calificacion): ?>
                <div class="calificacion-letrero">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#e53935" style="vertical-align:middle;">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <?= number_format($calificacion, 1) ?>
                </div>
                <?php endif; ?>
                <div id="cantidad-<?= $producto['id'] ?>" class="cantidad-disponible">
                    <?= $cantidad ?> disponibles
                </div>
                <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                <div class="product-title"><?= htmlspecialchars($producto['nombre']) ?></div>
                <div class="product-price">$<?= number_format($producto['precio'], 2) ?></div>
                <div class="acciones-producto">
                    <input type="number" min="1" max="<?= $cantidad ?>" value="1" id="input-<?= $producto['id'] ?>">
                    <button class="btn-comprar">Comprar</button>
                    <button class="btn-comprar btn-carrito" data-id="<?= $producto['id'] ?>" data-max="<?= $cantidad ?>">Añadir al carrito</button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="sale-carousel">
        <h2 style="color:#e53935;">Productos en Rebaja</h2>
        <div class="carousel-container">
            <button class="carousel-btn prev">&#10094;</button>
            <div class="carousel-track">
                <?php if ($ofertas && $ofertas->num_rows > 0): ?>
                    <?php while($oferta = $ofertas->fetch_assoc()): ?>
                    <div class="carousel-item" style="position:relative;">
                        <img src="<?= htmlspecialchars($oferta['imagen']) ?>" alt="<?= htmlspecialchars($oferta['nombre']) ?>">
                        <div class="precios-rebaja">
                            <span class="sale-price">$<?= number_format($oferta['precio_oferta'], 2) ?></span>
                            <span class="old-price">$<?= number_format($oferta['precio'], 2) ?></span>
                        </div>
                        <div class="product-title"><?= htmlspecialchars($oferta['nombre']) ?></div>
                        <?php
                            $query_cantidad = "SELECT cantidad FROM productos WHERE id = " . intval($oferta['id']);
                            $res_cantidad = $conn->query($query_cantidad);
                            $cantidad = $res_cantidad ? $res_cantidad->fetch_assoc()['cantidad'] : 0;
                        ?>
                        <div class="cantidad-disponible"><?= $cantidad ?> disponibles</div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-offers">
                        <p>Actualmente no hay ofertas disponibles</p>
                    </div>
                <?php endif; ?>
            </div>
            <button class="carousel-btn next">&#10095;</button>
        </div>
    </section>

    <footer>
        <p>&copy; 2023 Sport-FM. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="main.js"></script>
    <script src="custom.js"></script><!-- Tu JS personalizado -->
    <script>
        // Variable global para JS con el usuario actual
        window.usuarioActual = "<?= htmlspecialchars($usuario) ?>";
    </script>
</body>
</html>
