<?php
session_start();

// Solo permite acceso al usuario admin
if (!isset($_SESSION['nombre']) || $_SESSION['nombre'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Conexión a la base de datos (ajusta según tu configuración)
$db = new mysqli('localhost', 'root', '', 'sport');
if ($db->connect_error) {
    die("Error de conexión: " . $db->connect_error);
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'añadir':
                // Procesar añadir producto
                $nombre = $db->real_escape_string($_POST['nombre']);
                $descripcion = $db->real_escape_string($_POST['descripcion']);
                $precio = floatval($_POST['precio']);
                $cantidad = intval($_POST['cantidad']);
                $categoria = $db->real_escape_string($_POST['categoria']);
                
                // Procesar imagen
$imagen = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $target_dir = __DIR__ . "/uploads/"; // Ruta absoluta
    // Crear el directorio si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Validar que es una imagen
    $check = getimagesize($_FILES["imagen"]["tmp_name"]);
    if ($check !== false) {
        // Generar nombre único para evitar sobreescrituras
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
            $imagen = "uploads/" . $new_filename; // Guardamos la ruta relativa
        } else {
            $error = "Error al subir la imagen. Verifica los permisos del directorio.";
        }
    } else {
        $error = "El archivo no es una imagen válida.";
    }
}
                
                $sql = "INSERT INTO productos (nombre, descripcion, precio, cantidad, imagen, categoria) 
                        VALUES ('$nombre', '$descripcion', $precio, $cantidad, '$imagen', '$categoria')";
                $db->query($sql);
                break;
                
            case 'modificar':
                // Procesar modificar producto
                $id = intval($_POST['id']);
                $nombre = $db->real_escape_string($_POST['nombre']);
                $descripcion = $db->real_escape_string($_POST['descripcion']);
                $precio = floatval($_POST['precio']);
                $cantidad = intval($_POST['cantidad']);
                $categoria = $db->real_escape_string($_POST['categoria']);
                
                $sql = "UPDATE productos SET 
                        nombre = '$nombre', 
                        descripcion = '$descripcion', 
                        precio = $precio, 
                        cantidad = $cantidad, 
                        categoria = '$categoria' 
                        WHERE id = $id";
                $db->query($sql);
                break;
                
            case 'eliminar':
                // Procesar eliminar producto
                $id = intval($_POST['id']);
                $sql = "DELETE FROM productos WHERE id = $id";
                $db->query($sql);
                break;
                
            case 'oferta':
                // Procesar oferta
                $producto_id = intval($_POST['producto_id']);
                $precio_oferta = floatval($_POST['precio_oferta']);
                $fecha_inicio = $db->real_escape_string($_POST['fecha_inicio']);
                $fecha_fin = $db->real_escape_string($_POST['fecha_fin']);
                $descripcion_oferta = $db->real_escape_string($_POST['descripcion_oferta']);

                // Inserta la oferta (puedes ajustar para actualizar si ya existe)
                $sql = "INSERT INTO ofertas (producto_id, precio_oferta, fecha_inicio, fecha_fin, descripcion_oferta)
                        VALUES ($producto_id, $precio_oferta, '$fecha_inicio', '$fecha_fin', '$descripcion_oferta')";
                $db->query($sql);
                break;
        }
    }
}

// Obtener productos para modificar/eliminar
$productos = $db->query("SELECT id, nombre FROM productos ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Centro de Control - Sport-FM</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}
body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #181818;
}
.main-content {
    flex: 1 0 auto;
}
.admin-container {
    max-width: 900px;
    margin: 3rem auto;
    background: #000000;
    border-radius: 12px;
    padding: 2.5rem 2rem 2rem 2rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    border: 1.5px solid #e53935;
    color: #fff;
    text-align: center;
}
.admin-title {
    color: #e53935;
    margin-bottom: 2rem;
    font-size: 2rem;
    font-weight: bold;
}
.admin-btn {
    background: #e53935;
    color: #fff;
    border: none;
    border-radius: 20px;
    padding: 12px 36px;
    font-weight: bold;
    cursor: pointer;
    margin: 0 12px 1.5rem 12px;
    font-size: 1.1rem;
    transition: background 0.2s;
    display: inline-block;
}
.admin-btn:hover {
    background: #b71c1c;
}
.admin-btns {
    margin-top: 2rem;
}
.form-container {
    display: none;
    background: #232323;
    padding: 24px 18px;
    border-radius: 10px;
    margin-top: 24px;
    text-align: left;
    border: 1.5px solid #e53935;
    box-shadow: 0 2px 8px rgba(229,57,53,0.08);
}
.form-container.active {
    display: block;
}
.form-group {
    margin-bottom: 18px;
}
label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
    color: #fff;
}
input, textarea, select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    background: #232323;
    color: #fff;
    font-size: 1rem;
    transition: border 0.2s;
}
input:focus, textarea:focus, select:focus {
    border: 1.5px solid #e53935;
    outline: none;
    background: #222;
}
.submit-btn {
    background: #e53935;
    color: white;
    padding: 12px 0;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    width: 100%;
    font-weight: bold;
    font-size: 1.1rem;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(229,57,53,0.08);
}
.submit-btn:hover {
    background: #b71c1c;
    box-shadow: 0 4px 16px rgba(229,57,53,0.15);
}
footer {
    flex-shrink: 0;
    background: #111;
    color: #fff;
    text-align: center;
    padding: 1rem 0;
    margin-top: auto;
    width: 100%;
}
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">SPORT-FM</div>
            <div class="nav-links">
                <a href="index.php">Inicio</a>
                <a href="admin.php" class="active">Centro de Control</a>
                <a href="logout.php">Cerrar sesión</a>
            </div>
        </nav>
    </header>
    <div class="admin-container">
        <h1 class="admin-title">Centro de Control - Admin</h1>
        <div class="admin-btns">
            <button class="admin-btn" onclick="mostrarFormulario('añadir')">Añadir</button>
            <button class="admin-btn" onclick="mostrarFormulario('modificar')">Modificar</button>
            <button class="admin-btn" onclick="mostrarFormulario('eliminar')">Eliminar</button>
            
            <button class="admin-btn" onclick="mostrarFormulario('oferta')">Ofertas</button> <!-- Nuevo botón -->
        </div>
        
        <!-- Formulario para Añadir Producto -->
        <div id="form-añadir" class="form-container">
            <h2>Añadir Nuevo Producto</h2>
            <form action="admin.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="añadir">
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <input type="text" id="categoria" name="categoria">
                </div>
                
                <div class="form-group">
                    <label for="imagen">Imagen:</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div>
                
                <button type="submit" class="submit-btn">Guardar Producto</button>
            </form>
        </div>
        
        <!-- Formulario para Modificar Producto -->
        <div id="form-modificar" class="form-container">
            <h2>Modificar Producto</h2>
            <form action="admin.php" method="post">
                <input type="hidden" name="accion" value="modificar">
                
                <div class="form-group">
                    <label for="modificar-id">Seleccionar Producto:</label>
                    <select id="modificar-id" name="id" required>
                        <option value="">-- Seleccione un producto --</option>
                        <?php while($producto = $productos->fetch_assoc()): ?>
                            <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="modificar-nombre">Nombre:</label>
                    <input type="text" id="modificar-nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="modificar-descripcion">Descripción:</label>
                    <textarea id="modificar-descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="modificar-precio">Precio:</label>
                    <input type="number" id="modificar-precio" name="precio" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="modificar-cantidad">Cantidad:</label>
                    <input type="number" id="modificar-cantidad" name="cantidad" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="modificar-categoria">Categoría:</label>
                    <input type="text" id="modificar-categoria" name="categoria">
                </div>
                
                <button type="submit" class="submit-btn">Actualizar Producto</button>
            </form>
        </div>
        
        <!-- Formulario para Eliminar Producto -->
        <div id="form-eliminar" class="form-container">
            <h2>Eliminar Producto</h2>
            <form action="admin.php" method="post">
                <input type="hidden" name="accion" value="eliminar">
                
                <div class="form-group">
                    <label for="eliminar-id">Seleccionar Producto:</label>
                    <select id="eliminar-id" name="id" required>
                        <option value="">-- Seleccione un producto --</option>
                        <?php 
                        // Resetear el puntero del resultado para volver a usarlo
                        $productos->data_seek(0);
                        while($producto = $productos->fetch_assoc()): ?>
                            <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <button type="submit" class="submit-btn">Eliminar Producto</button>
            </form>
        </div>
        
        <!-- Formulario para Ofertas -->
        <div id="form-oferta" class="form-container">
            <h2>Gestionar Ofertas</h2>
            <form action="admin.php" method="post">
                <input type="hidden" name="accion" value="oferta">
                <div class="form-group">
                    <label for="producto-oferta">Producto:</label>
                    <select id="producto-oferta" name="producto_id" required>
                        <option value="">-- Seleccione un producto --</option>
                        <?php 
                        $productos->data_seek(0);
                        while($producto = $productos->fetch_assoc()): ?>
                            <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="precio_oferta">Precio de Oferta:</label>
                    <input type="number" id="precio_oferta" name="precio_oferta" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>
                </div>
                <div class="form-group">
                    <label for="descripcion_oferta">Descripción de la Oferta:</label>
                    <input type="text" id="descripcion_oferta" name="descripcion_oferta">
                </div>
                <button type="submit" class="submit-btn">Aplicar Oferta</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2023 Sport-FM. Todos los derechos reservados.</p>
    </footer>

    <script>
        function mostrarFormulario(tipo) {
            // Ocultar todos los formularios primero
            document.querySelectorAll('.form-container').forEach(form => {
                form.classList.remove('active');
            });
            
            // Mostrar el formulario seleccionado
            document.getElementById(`form-${tipo}`).classList.add('active');
            
            // Si es modificar, podemos cargar los datos del producto seleccionado
            if (tipo === 'modificar') {
                document.getElementById('modificar-id').addEventListener('change', function() {
                    if (this.value) {
                        // Aquí podrías hacer una petición AJAX para cargar los datos del producto
                        // Por simplicidad, en este ejemplo el usuario debe rellenar los campos manualmente
                    }
                });
            }
        }
        
        // Opcional: Cargar datos del producto para modificar (requeriría AJAX)
        // function cargarDatosProducto(id) {
        //     fetch(`obtener_producto.php?id=${id}`)
        //         .then(response => response.json())
        //         .then(data => {
        //             document.getElementById('modificar-nombre').value = data.nombre;
        //             document.getElementById('modificar-descripcion').value = data.descripcion;
        //             document.getElementById('modificar-precio').value = data.precio;
        //             document.getElementById('modificar-cantidad').value = data.cantidad;
        //             document.getElementById('modificar-categoria').value = data.categoria;
        //         });
        // }
    </script>
</body>
</html>