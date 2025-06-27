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
    <header>
        <div class="anuncio-barra">
            Ofertas de invierno
        </div>
        <nav>
            <div class="logo">SPORT-FM</div>
            <div class="nav-links">
                <a href="#" class="active">Inicio</a>
                <a href="productos.html">Productos</a>
                <a href="ofertas.html">Ofertas</a>
                <a href="#" id="buscar-btn">Buscar</a>
                <a href="#">Contacto</a>
                <a href="#" id="perfil-link" class="perfil-link">Perfil</a>
                <a href="carrito.html" class="cart-icon" title="Carrito">
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
        <a href="productos.html" class="btn">Ver Productos</a>
    </section>

    <section class="featured-products">
        <h2>Productos Destacados</h2>
        <div class="image-grid">
            <!-- 
                Aquí debes insertar manualmente o con JS los productos destacados.
                Ejemplo de un producto:
            -->
            <div class="carousel-item" style="position:relative;" id="producto-1">
                <!-- Puedes agregar calificación y cantidad si lo deseas -->
                <div class="calificacion-letrero">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#e53935" style="vertical-align:middle;">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    4.5
                </div>
                <div class="cantidad-disponible">10 disponibles</div>
                <img src="ruta/a/imagen1.jpg" alt="Nombre del producto">
                <div class="product-title">Nombre del producto</div>
                <div class="product-price">$999.00</div>
                <div class="acciones-producto">
                    <input type="number" min="1" max="10" value="1" id="input-1">
                    <button class="btn-comprar">Comprar</button>
                    <button class="btn-comprar btn-carrito" data-id="1" data-max="10">Añadir al carrito</button>
                </div>
            </div>
            <!-- Repite para más productos -->
        </div>
    </section>

    <section class="sale-carousel">
        <h2 style="color:#e53935;">Productos en Rebaja</h2>
        <div class="carousel-container">
            <button class="carousel-btn prev">&#10094;</button>
            <div class="carousel-track">
                <!-- 
                    Aquí debes insertar manualmente o con JS los productos en oferta.
                    Ejemplo de un producto en oferta:
                -->
                <div class="carousel-item" style="position:relative;">
                    <img src="ruta/a/oferta1.jpg" alt="Nombre oferta">
                    <div class="precios-rebaja">
                        <span class="sale-price">$499.00</span>
                        <span class="old-price">$699.00</span>
                    </div>
                    <div class="product-title">Nombre oferta</div>
                    <div class="cantidad-disponible">8 disponibles</div>
                </div>
