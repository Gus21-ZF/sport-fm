<?php
session_start();
require_once 'conexion.php';

$usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

$resultados = [];
if ($busqueda !== '') {
    $stmt = $conn->prepare("SELECT * FROM productos WHERE nombre LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("s", $busqueda);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $resultados[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar producto - Sport-FM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="buscar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div class="anuncio-barra">
            Resultados de búsqueda
        </div>
        <nav>
            <div class="logo">SPORT-FM</div>
            <div class="nav-links">
                <a href="index.php" >Inicio</a>
                <a href="productos.php">Productos</a>
                <a href="ofertas.php">Ofertas</a>
                <a href="#" class="active" id="buscar-btn">Buscar</a>
                <a href="#">Contacto</a>
                <a href="#" id="perfil-link" class="perfil-link">Perfil</a>
                <a href="carrito.php" class="cart-icon" title="Carrito">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;">
                        <path d="M7 20c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm10 0c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2-.896 2 2 2zM7.334 16h9.359c.828 0 1.554-.522 1.841-1.303l3.09-7.787A1 1 0 0 0 20.7 6H6.215l-.94-2.36A1 1 0 0 0 4.333 3H2v2h1.333l3.6 9.06-1.35 2.44C5.21 16.37 5.97 17 6.834 17h12v-2h-11.5l.5-1z" fill="#e53935"/>
                    </svg>
                </a>
            </div>
        </nav>
    </header>
    <div class="busqueda-container">
        <div class="busqueda-titulo">
            <?php if ($busqueda !== ''): ?>
                Resultados para: <b><?= htmlspecialchars($busqueda) ?></b>
            <?php else: ?>
                Escribe un nombre de producto para buscar.
            <?php endif; ?>
        </div>
        <?php if ($busqueda !== ''): ?>
            <?php if (count($resultados) > 0): ?>
                <div class="productos-grid">
                    <?php foreach ($resultados as $producto): ?>
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
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="color:#e53935; font-size:1.1rem; margin-top:30px;">
                    No se encontraron productos con ese nombre.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2023 Sport-FM. Todos los derechos reservados.</p>
    </footer>
    <script src="custom.js"></script>
    <script src="buscar.js"></script>
    <script>
        window.usuarioActual = "<?= htmlspecialchars($usuario) ?>";
    </script>
</body>
</html>