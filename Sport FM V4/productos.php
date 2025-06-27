<?php
session_start();
require_once 'conexion.php';

// Consulta para obtener todos los productos
$query = "SELECT * FROM productos";
$result = $conn->query($query);

// Recupera el usuario de la sesión (esto es lo que faltaba)
$usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Sport-FM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="custom.css">
    <link rel="stylesheet" href="productos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div class="anuncio-barra">
            ¡Envío gratis en compras superiores a $5000!
        </div>
        <nav>
             <div class="logo">SPORT-FM</div>
            <div class="nav-links">
                <a href="index.php" >Inicio</a>
                <a href="productos.php" class="active">Productos</a>
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

    <div class="productos-container">
        <main class="productos-grid">
            <?php while($producto = $result->fetch_assoc()): ?>
                <div class="producto-card" id="producto-<?= $producto['id'] ?>">
                    <div class="producto-imagen">
                        <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">
                    </div>
                    <div class="producto-info">
                        <div class="producto-categoria"><?= htmlspecialchars($producto['categoria']) ?></div>
                        <h3 class="producto-nombre"><?= htmlspecialchars($producto['nombre']) ?></h3>
                        <div>
                            <span class="producto-precio">
                                $<?= number_format($producto['precio'], 2) ?>
                            </span>
                        </div>
                        <div style="margin-top:10px;">
                            <input type="number" min="1" max="<?= $producto['cantidad'] ?>" value="1" id="input-<?= $producto['id'] ?>" style="width:50px; padding:4px; border-radius:4px; border:1px solid #ccc; text-align:center;">
                            <span id="cantidad-<?= $producto['id'] ?>" style="margin-left:10px; color:#e53935; font-weight:bold;"><?= $producto['cantidad'] ?> disponibles</span>
                        </div>
                        <div class="producto-acciones">
                            <button class="btn-favorito">♥</button>
                            <button class="btn-carrito" data-id="<?= $producto['id'] ?>" data-max="<?= $producto['cantidad'] ?>">Añadir al carrito</button>
                            <button class="btn-comprar">Comprar</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </main>
    </div>

    <footer>
        <p>&copy; 2023 Sport-FM. Todos los derechos reservados.</p>
    </footer>

    
    <script src="custom.js"></script>
    
    <script>
        window.usuarioActual = "<?= htmlspecialchars($usuario) ?>";
    </script>
</body>
</html>