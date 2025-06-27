<?php
session_start();
require_once 'conexion.php';

$usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
if (!$usuario) {
    header('Location: login.html');
    exit;
}

// --- Mostrar productos en el carrito (no comprados) ---
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$productos_carrito = [];
$total = 0;
if (!empty($carrito)) {
    $ids = implode(',', array_map('intval', array_keys($carrito)));
    $sql = "SELECT id, nombre, imagen, precio FROM productos WHERE id IN ($ids)";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $row['cantidad'] = $carrito[$row['id']];
        $productos_carrito[] = $row;
        $total += $row['precio'] * $row['cantidad'];
    }
}

// --- Mostrar productos ya comprados ---
$stmt = $conn->prepare("SELECT c.producto_id, c.cantidad, p.nombre, p.imagen, p.precio, c.fecha
                        FROM compras c
                        JOIN productos p ON c.producto_id = p.id
                        WHERE c.usuario = ?
                        ORDER BY c.fecha DESC");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$comprados = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - Sport-FM</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    /* Botón estilo igual que "Seguir comprando" */
    .btn-carrito-custom {
        background: #111 !important;
        color: #fff !important;
        border-radius: 20px !important;
        padding: 10px 28px !important;
        font-weight: bold !important;
        border: none !important;
        margin: 0 8px;
        transition: background 0.2s;
        cursor: pointer;
    }
    .btn-carrito-custom:hover {
        background: #333 !important;
    }
    .swal2-custom-btn {
        background: #e53935 !important;
        color: #fff !important;
        border-radius: 20px !important;
        padding: 10px 28px !important;
        font-weight: bold !important;
        border: none !important;
        margin: 0 8px;
        transition: background 0.2s;
    }
    .swal2-custom-btn:hover {
        background: #b71c1c !important;
    }
    .swal2-custom-btn-cancel {
        background: #fff !important;
        color: #e53935 !important;
        border-radius: 20px !important;
        padding: 10px 28px !important;
        font-weight: bold !important;
        border: 2px solid #e53935 !important;
        margin: 0 8px;
        transition: background 0.2s, color 0.2s;
    }
    .swal2-custom-btn-cancel:hover {
        background: #e53935 !important;
        color: #fff !important;
    }
    </style>
</head>
<body>
    <header>
        <div class="anuncio-barra">Ofertas de invierno</div>
        <nav>
            <div class="logo">SPORT-FM</div>
            <div class="nav-links">
                <a href="index.php">Inicio</a>
                <a href="productos.php">Productos</a>
                <a href="ofertas.php">Ofertas</a>
                <a href="buscar.php" id="buscar-btn">Buscar</a>
                <a href="#">Contacto</a>
                <a href="perfil.php" id="perfil-link" class="perfil-link">Perfil</a>
                <a href="carrito.php" class="cart-icon active" title="Carrito">
                    <!-- Icono carrito SVG color negro -->
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" style="vertical-align:middle;">
                        <path d="M7 20c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm10 0c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2-.896 2 2 2zM7.334 16h9.359c.828 0 1.554-.522 1.841-1.303l3.09-7.787A1 1 0 0 0 20.7 6H6.215l-.94-2.36A1 1 0 0 0 4.333 3H2v2h1.333l3.6 9.06-1.35 2.44C5.21 16.37 5.97 17 6.834 17h12v-2h-11.5l.5-1z" fill="#111"/>
                    </svg>
                </a>
            </div>
        </nav>
    </header>

    <section class="hero">
        <h1>Mi Carrito</h1>
        <p>Estos son los productos que has añadido a tu carrito.</p>
        <a href="productos.php" class="btn">Seguir comprando</a>
    </section>

    <section class="featured-products">
        <h2>Carrito actual</h2>
        <?php if (!empty($productos_carrito)): ?>
        <form id="vaciar-carrito-form" method="post" style="margin-bottom:20px; text-align:center;">
            <button type="submit" class="btn-carrito-custom" style="background:#b71c1c;">Vaciar carrito</button>
        </form>
        <div class="image-grid">
            <?php foreach($productos_carrito as $item): ?>
            <div class="carousel-item" style="position:relative;" id="carrito-prod-<?= $item['id'] ?>">
                <img src="<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>">
                <div class="product-title" style="color:#111; font-weight:bold; margin-top:6px;">
                    <?= htmlspecialchars($item['nombre']) ?>
                </div>
                <div class="product-price">
                    $<?= number_format($item['precio'], 2) ?>
                </div>
                <div style="margin-top:10px; color:#333;">
                    Cantidad: <strong><?= $item['cantidad'] ?></strong>
                </div>
                <form class="eliminar-form" method="post" style="margin-top:10px;">
                    <input type="hidden" name="eliminar_id" value="<?= $item['id'] ?>">
                    <button type="submit" class="btn-carrito-custom" style="background:#e53935;">Eliminar</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top:32px;">
            <h3 style="color:#e53935;">Total a pagar: $<?= number_format($total, 2) ?></h3>
            <button id="finalizar-compra" class="btn-carrito-custom" style="margin-top:10px;">Finalizar compra</button>
        </div>
        <?php else: ?>
            <p style="text-align:center;">Tu carrito está vacío.</p>
        <?php endif; ?>
    </section>

    <section class="featured-products">
        <h2>Historial de compras</h2>
        <?php if ($comprados->num_rows > 0): ?>
        <div class="image-grid">
            <?php while($compra = $comprados->fetch_assoc()): ?>
            <div class="carousel-item" style="position:relative;">
                <img src="<?= htmlspecialchars($compra['imagen']) ?>" alt="<?= htmlspecialchars($compra['nombre']) ?>">
                <div class="product-title" style="color:#111; font-weight:bold; margin-top:6px;">
                    <?= htmlspecialchars($compra['nombre']) ?>
                </div>
                <div class="product-price">
                    $<?= number_format($compra['precio'], 2) ?>
                </div>
                <div style="margin-top:10px; color:#333;">
                    Cantidad: <strong><?= $compra['cantidad'] ?></strong>
                </div>
                <div style="margin-top:6px; color:#888; font-size:0.9em;">
                    Fecha: <?= date('d/m/Y H:i', strtotime($compra['fecha'])) ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <p style="text-align:center;">Aún no has realizado compras.</p>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2023 Sport-FM. Todos los derechos reservados.</p>
     </footer>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="custom.js"></script>
    <script src="buscar.js"></script>
    <script>
        window.usuarioActual = "<?= htmlspecialchars($usuario) ?>";
   

    // Eliminar producto del carrito
    document.querySelectorAll('.eliminar-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const prodId = this.querySelector('input[name="eliminar_id"]').value;
            fetch('eliminar_carrito.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'producto_id=' + prodId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('carrito-prod-' + prodId).remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Producto eliminado',
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'swal2-custom-btn' },
                        buttonsStyling: false
                    });
                }
            });
        });
    });

    // Vaciar carrito
    document.getElementById('vaciar-carrito-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('vaciar_carrito.php', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.carousel-item').forEach(el => el.remove());
                Swal.fire({
                    icon: 'success',
                    title: 'Carrito vaciado',
                    confirmButtonText: 'Aceptar',
                    customClass: { confirmButton: 'swal2-custom-btn' },
                    buttonsStyling: false
                });
            }
        });
    });

    // Finalizar compra
    document.getElementById('finalizar-compra')?.addEventListener('click', function() {
        Swal.fire({
            title: '¿Confirmar compra?',
            text: '¿Deseas finalizar tu compra?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, comprar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'swal2-custom-btn',
                cancelButton: 'swal2-custom-btn-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('finalizar_compra.php', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.carousel-item').forEach(el => el.remove());
                        Swal.fire({
                            icon: 'success',
                            title: '¡Compra realizada!',
                            text: 'Tus productos han sido comprados.',
                            confirmButtonText: 'Aceptar',
                            customClass: { confirmButton: 'swal2-custom-btn' },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'No se pudo finalizar la compra.',
                            confirmButtonText: 'Aceptar',
                            customClass: { confirmButton: 'swal2-custom-btn' },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    });
    </script>
</body>
</html>