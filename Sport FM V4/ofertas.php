<?php
session_start();
require_once 'conexion.php';

// Consulta para obtener todas las ofertas activas y sus productos
$query = "SELECT o.id as oferta_id, o.descripcion_oferta, o.fecha_inicio, o.fecha_fin, 
                 p.id as producto_id, p.nombre as producto_nombre, p.imagen, p.categoria, p.precio, p.cantidad, o.precio_oferta
          FROM ofertas o
          JOIN productos p ON o.producto_id = p.id
          WHERE o.fecha_inicio <= CURDATE() AND o.fecha_fin >= CURDATE()
          ORDER BY o.id DESC, p.nombre ASC";
$result = $conn->query($query);

$usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';

// Agrupar productos por oferta
$ofertas = [];
while ($row = $result->fetch_assoc()) {
    $oferta_id = $row['oferta_id'];
    if (!isset($ofertas[$oferta_id])) {
        $ofertas[$oferta_id] = [
            'descripcion_oferta' => $row['descripcion_oferta'],
            'fecha_inicio' => $row['fecha_inicio'],
            'fecha_fin' => $row['fecha_fin'],
            'productos' => []
        ];
    }
    $ofertas[$oferta_id]['productos'][] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas - Sport-FM</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="custom.css">
    <link rel="stylesheet" href="ofertas.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .ofertas-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0;
        }
        .oferta-barra {
            background: #e53935;
            color: #fff;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 14px 0 10px 0;
            text-align: center;
            margin: 30px auto 30px auto;
            border-radius: 18px;
            letter-spacing: 1px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            max-width: 1000px;
            width: 100%;
        }
        .oferta-desc {
            color: #fff;
            font-size: 1rem;
            margin-top: 4px;
            margin-bottom: 0;
            font-weight: normal;
        }
        .oferta-fechas {
            color: #fff;
            font-size: 0.9rem;
            margin-top: 2px;
            margin-bottom: 0;
        }
        .productos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }
        .producto-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .producto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .producto-imagen {
            height: 200px;
            overflow: hidden;
        }
        .producto-imagen img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .producto-card:hover .producto-imagen img {
            transform: scale(1.05);
        }
        .producto-info {
            padding: 15px;
        }
        .producto-categoria {
            color: #666;
            font-size: 0.8rem;
            margin-bottom: 5px;
        }
        .producto-nombre {
            font-weight: 600;
            margin-bottom: 10px;
            height: 40px;
            overflow: hidden;
        }
        .producto-precio {
            font-weight: 700;
            color: #e53935;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .producto-precio.old {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
            margin-right: 8px;
        }
        .producto-acciones {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .btn-carrito {
            background: #111;
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-carrito:hover {
            background: #333;
        }
        .btn-comprar {
            background: #e53935;
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-comprar:hover {
            background: #b71c1c;
        }
        .btn-favorito {
            background: none;
            border: 1px solid #ddd;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-favorito:hover {
            border-color: #e53935;
            color: #e53935;
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
        @media (max-width: 1024px) {
            .oferta-barra, .productos-grid {
                max-width: 100%;
            }
        }
        @media (max-width: 768px) {
            .productos-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            .oferta-barra {
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="anuncio-barra">
            ¡Ofertas exclusivas por tiempo limitado!
        </div>
        <nav>
            <div class="logo">SPORT-FM</div>
            <div class="nav-links">
                <a href="index.php">Inicio</a>
                <a href="productos.php">Productos</a>
                <a href="ofertas.php" class="active">Ofertas</a>
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

    <div class="ofertas-container">
        <?php if (empty($ofertas)): ?>
            <div class="oferta-barra" style="margin-bottom:0;">No hay ofertas activas en este momento.</div>
        <?php else: ?>
            <?php foreach ($ofertas as $oferta_id => $oferta): ?>
                <div class="oferta-barra">
                    Oferta especial #<?= $oferta_id ?>
                    <?php if ($oferta['descripcion_oferta']): ?>
                        <div class="oferta-desc"><?= htmlspecialchars($oferta['descripcion_oferta']) ?></div>
                    <?php endif; ?>
                    <div class="oferta-fechas">
                        <?= date('d/m/Y', strtotime($oferta['fecha_inicio'])) ?> - <?= date('d/m/Y', strtotime($oferta['fecha_fin'])) ?>
                    </div>
                </div>
                <div class="productos-grid">
                    <?php foreach ($oferta['productos'] as $producto): ?>
                        <div class="producto-card" id="producto-<?= $producto['producto_id'] ?>">
                            <div class="producto-imagen">
                                <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['producto_nombre']) ?>">
                            </div>
                            <div class="producto-info">
                                <div class="producto-categoria"><?= htmlspecialchars($producto['categoria']) ?></div>
                                <h3 class="producto-nombre"><?= htmlspecialchars($producto['producto_nombre']) ?></h3>
                                <div>
                                    <span class="producto-precio" style="color:#e53935;">
                                        $<?= number_format($producto['precio_oferta'], 2) ?>
                                    </span>
                                    <span class="producto-precio old">
                                        $<?= number_format($producto['precio'], 2) ?>
                                    </span>
                                </div>
                                <div style="margin-top:10px;">
                                    <input type="number" min="1" max="<?= $producto['cantidad'] ?>" value="1" id="input-<?= $producto['producto_id'] ?>" style="width:50px; padding:4px; border-radius:4px; border:1px solid #ccc; text-align:center;">
                                    <span id="cantidad-<?= $producto['producto_id'] ?>" style="margin-left:10px; color:#e53935; font-weight:bold;"><?= $producto['cantidad'] ?> disponibles</span>
                                </div>
                                <div class="producto-acciones">
                                    <button class="btn-favorito">♥</button>
                                    <button class="btn-carrito" data-id="<?= $producto['producto_id'] ?>" data-max="<?= $producto['cantidad'] ?>">Añadir al carrito</button>
                                    <button class="btn-comprar">Comprar</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2023 Sport-FM. Todos los derechos reservados.</p>
    </footer>

    <script src="custom.js"></script>
    <script src="ofertas.js"></script>
    <script>
        window.usuarioActual = "<?= htmlspecialchars($usuario) ?>";
        document.addEventListener('DOMContentLoaded', function() {
    // Añadir al carrito
    document.querySelectorAll('.btn-carrito').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const input = document.getElementById('input-' + id);
            let cantidad = parseInt(input.value);
            if (isNaN(cantidad) || cantidad < 1) cantidad = 1;
            const cantidadElem = document.getElementById('cantidad-' + id);

            let disponibles = parseInt(cantidadElem.textContent);
            if (cantidad > disponibles) cantidad = disponibles;

            fetch('agregar_carrito.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + id + '&cantidad=' + cantidad
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    disponibles -= cantidad;
                    if (disponibles > 0) {
                        cantidadElem.textContent = disponibles + ' disponibles';
                        input.max = disponibles;
                        if (cantidad > disponibles) input.value = disponibles;
                    } else {
                        document.getElementById('producto-' + id).remove();
                    }
                } else {
                    alert('No se pudo añadir al carrito. Inténtalo de nuevo.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error al añadir al carrito. Por favor, intenta de nuevo más tarde.');
            });
        });
    });

    // Comprar directo
    document.querySelectorAll('.btn-comprar').forEach(btn => {
        btn.addEventListener('click', function() {
            const parent = this.closest('.producto-card');
            const id = parent.id.replace('producto-', '');
            const input = parent.querySelector('input[type="number"]');
            let cantidad = parseInt(input.value);
            if (isNaN(cantidad) || cantidad < 1) cantidad = 1;
            const max = parseInt(input.max);

            if (cantidad > max) cantidad = max;

            Swal.fire({
                title: 'Confirma tu compra',
                text: '¿Deseas comprar este producto?',
                icon: 'question',
                input: 'password',
                inputLabel: 'Ingresa tu contraseña para confirmar',
                inputPlaceholder: 'Contraseña',
                inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
                showCancelButton: true,
                confirmButtonText: 'Comprar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'swal2-custom-btn',
                    cancelButton: 'swal2-custom-btn-cancel'
                },
                buttonsStyling: false,
                preConfirm: (password) => {
                    if (!password) {
                        Swal.showValidationMessage('Debes ingresar tu contraseña');
                        return false;
                    }
                    return fetch('validar_compra.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'password=' + encodeURIComponent(password)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error('Contraseña incorrecta');
                        }
                        return true;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(error.message);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('comprar_producto.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'id=' + id + '&cantidad=' + cantidad
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (data.restante > 0) {
                                document.getElementById('cantidad-' + id).textContent = data.restante + ' disponibles';
                                input.max = data.restante;
                                if (cantidad > data.restante) input.value = data.restante;
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Compra realizada!',
                                    text: 'Tu compra se ha realizado correctamente.',
                                    confirmButtonText: 'Aceptar',
                                    customClass: { confirmButton: 'swal2-custom-btn' },
                                    buttonsStyling: false
                                });
                            } else {
                                document.getElementById('producto-' + id).remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Compra realizada!',
                                    text: 'Has comprado la última unidad.',
                                    confirmButtonText: 'Aceptar',
                                    customClass: { confirmButton: 'swal2-custom-btn' },
                                    buttonsStyling: false
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.msg || 'No se pudo realizar la compra. Verifica la cantidad.',
                                confirmButtonText: 'Aceptar',
                                customClass: { confirmButton: 'swal2-custom-btn' },
                                buttonsStyling: false
                            });
                        }
                    });
                }
            });
        });
    });

    // Favoritos (visual)
    document.querySelectorAll('.btn-favorito').forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('favorito-activo');
            if (this.classList.contains('favorito-activo')) {
                this.innerHTML = '♥';
                this.style.color = '#e53935';
                this.style.borderColor = '#e53935';
            } else {
                this.innerHTML = '♥';
                this.style.color = '';
                this.style.borderColor = '#ddd';
            }
        });
    });
});
    </script>
</body>
</html>