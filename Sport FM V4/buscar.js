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