document.addEventListener('DOMContentLoaded', function() {
    console.log('custom.js cargado');

    // Mostrar bienvenida solo si NO estamos en productos.php, ofertas.php o buscar.php
    if (
        !window.location.pathname.endsWith('productos.php') &&
        !window.location.pathname.endsWith('ofertas.php') &&
        !window.location.pathname.endsWith('buscar.php') &&
        !window.location.pathname.endsWith('carrito.php')
    ) {
        if (typeof window.usuarioActual !== "undefined") {
            if (!window.usuarioActual) {
                Swal.fire({
                    title: '¡Bienvenido a Sport-FM!',
                    text: 'Regístrate para recibir ofertas exclusivas y novedades.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Registrarme',
                    cancelButtonText: 'Más tarde',
                    customClass: {
                        confirmButton: 'swal2-custom-btn',
                        cancelButton: 'swal2-custom-btn-cancel'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'register.php';
                    }
                });
            } else {
                Swal.fire({
                    title: `¡Bienvenido, ${window.usuarioActual}!`,
                    text: 'Nos alegra tenerte de vuelta.',
                    icon: 'success',
                    confirmButtonText: 'Continuar',
                    customClass: {
                        confirmButton: 'swal2-custom-btn'
                    },
                    buttonsStyling: false
                });
            }
        }
    }

    // Buscar
    const buscarBtn = document.getElementById('buscar-btn');
    if (buscarBtn) {
        buscarBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Buscar producto',
                html: `
                    <form id="swal-form-busqueda" style="display:flex; align-items:center; gap:8px; margin-top:10px;">
                        <input type="text" id="swal-input-busqueda" name="busqueda" placeholder="Nombre del producto..." style="flex:1; padding:8px 12px; border-radius:20px; border:1px solid #ccc; font-size:1rem;">
                        <button id="swal-btn-lupa" type="submit" style="background:#e53935; border:none; border-radius:50%; width:38px; height:38px; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                            <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24">
                                <path d="M21.53 20.47l-4.73-4.73A7.92 7.92 0 0 0 18 10a8 8 0 1 0-8 8 7.92 7.92 0 0 0 5.74-2.2l4.73 4.73a1 1 0 0 0 1.41-1.41zM4 10a6 6 0 1 1 6 6 6 6 0 0 1-6-6z"/>
                            </svg>
                        </button>
                    </form>
                `,
                showConfirmButton: false,
                didOpen: () => {
                    const form = document.getElementById('swal-form-busqueda');
                    const input = document.getElementById('swal-input-busqueda');
                    input.focus();
                    form.addEventListener('submit', function(ev) {
                        ev.preventDefault();
                        const valor = input.value.trim();
                        if (valor) {
                            window.location.href = 'buscar.php?busqueda=' + encodeURIComponent(valor);
                        }
                    });
                }
            });
        });
    }

    // Perfil
    const perfilBtn = document.getElementById('perfil-link');
    if (perfilBtn) {
        perfilBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (window.usuarioActual) {
                Swal.fire({
                    title: 'Perfil de usuario',
                    html: `
                        <div style="text-align:left;">
                            <b>Usuario:</b> ${window.usuarioActual}<br>
                            <button id="cerrar-sesion-btn" style="margin-top:16px; background:#e53935; color:#fff; border:none; border-radius:20px; padding:10px 28px; font-weight:bold; cursor:pointer;">Cerrar sesión</button>
                        </div>
                    `,
                    showConfirmButton: false,
                    didOpen: () => {
                        document.getElementById('cerrar-sesion-btn').onclick = function() {
                            window.location.href = 'logout.php';
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'No has iniciado sesión',
                    text: 'Por favor inicia sesión para ver tu perfil.',
                    confirmButtonText: 'Iniciar sesión',
                    customClass: { confirmButton: 'swal2-custom-btn' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
            }
        });
    }

    // Mostrar solo el mensaje de invitación a iniciar sesión en productos.php
    if (window.location.pathname.endsWith('productos.php')) {
        if (!window.usuarioActual) {
            Swal.fire({
                icon: 'info',
                title: 'No has iniciado sesión',
                text: 'Por favor inicia sesión para ver tu perfil y comprar productos.',
                confirmButtonText: 'Iniciar sesión',
                customClass: { confirmButton: 'swal2-custom-btn' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        }
        // No mostrar mensaje de bienvenida aunque esté logueado
        // return; // <--- ELIMINA este return
    }

    // Añadir al carrito
    document.querySelectorAll('.btn-carrito').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const input = document.getElementById('input-' + id);
            let cantidad = parseInt(input.value);
            if (isNaN(cantidad) || cantidad < 1) cantidad = 1;
            const cantidadElem = document.getElementById('cantidad-' + id);

            // Obtener cantidad visual actual
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

    // Comprar directo (con validación de contraseña y compra en BD)
    document.querySelectorAll('.btn-comprar').forEach(btn => {
        btn.addEventListener('click', function() {
            // Evita que el botón "Añadir al carrito" dispare esto
            if (this.classList.contains('btn-carrito')) return;

            const parent = this.closest('.carousel-item, .producto-card');
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
                    // Validar contraseña por AJAX
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
                    // Realiza la compra en la base de datos
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

    document.querySelectorAll('.carousel-item[id^="producto-"]').forEach(card => {
        card.addEventListener('click', function(e) {
            // Evita que el clic en botones dentro de la tarjeta active la redirección
            if (
                e.target.closest('button') ||
                e.target.tagName === 'BUTTON' ||
                e.target.closest('input')
            ) return;

            // Obtén el nombre del producto desde el elemento correspondiente
            const nombreElem = card.querySelector('.product-title');
            if (nombreElem) {
                const nombre = nombreElem.textContent.trim();
                if (nombre) {
                    window.location.href = 'buscar.php?busqueda=' + encodeURIComponent(nombre);
                }
            }
        });
    });

    document.querySelectorAll('.producto-imagen img, .carousel-item .producto-imagen img').forEach(img => {
        img.addEventListener('click', function(e) {
            e.stopPropagation(); // Evita que otros listeners de la tarjeta se activen

            // Busca el nombre del producto en la tarjeta
            let card = img.closest('.producto-card, .carousel-item');
            let nombreElem = card ? card.querySelector('.product-title, .producto-nombre') : null;
            if (nombreElem) {
                const nombre = nombreElem.textContent.trim();
                if (nombre) {
                    window.location.href = 'buscar.php?busqueda=' + encodeURIComponent(nombre);
                }
            }
        });
    });

    document.querySelectorAll('.carousel-item img').forEach(img => {
        img.addEventListener('click', function(e) {
            e.stopPropagation(); // Evita otros listeners

            // Busca el nombre del producto en la tarjeta
            let card = img.closest('.carousel-item');
            let nombreElem = card ? card.querySelector('.product-title') : null;
            if (nombreElem) {
                const nombre = nombreElem.textContent.trim();
                if (nombre) {
                    window.location.href = 'buscar.php?busqueda=' + encodeURIComponent(nombre);
                }
            }
        });
    });

    document.querySelectorAll('.logo').forEach(function(logo) {
        logo.style.cursor = 'pointer';
        logo.addEventListener('click', function(e) {
            e.preventDefault();
            window.history.back();
        });
    });
});