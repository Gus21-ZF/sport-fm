<?php
session_start();
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'root', '', 'sport');
    if ($conn->connect_error) {
        $error = "Error de conexión.";
    } else {
        // Verifica si el usuario ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "El correo ya está registrado.";
        } else {
            // Inserta el nuevo usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellidos, correo, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $apellidos, $correo, $hash);
            if ($stmt->execute()) {
                $success = "¡Registro exitoso! Ahora puedes iniciar sesión.";
            } else {
                $error = "Error al registrar. Intenta de nuevo.";
            }
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sport-FM</title>
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
        .register-container {
            max-width: 400px;
            margin: 3rem auto;
            background: #000000;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            border: 1.5px solid #e53935;
        }
        .register-title {
            color: #e53935;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .register-form label {
            font-weight: bold;
            color: #fff;
        }
        .register-form input[type="text"],
        .register-form input[type="email"],
        .register-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            background: #232323;
            color: #fff;
        }
        .register-form button {
            background: #e53935;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 28px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
        }
        .register-form button:hover {
            background: #b71c1c;
        }
        .register-links {
            margin-top: 2rem;
            text-align: center;
        }
        .register-links a {
            color: #e53935;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s;
        }
        .register-links a:hover {
            text-decoration: underline;
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
        </nav>
    </header>
    <div class="main-content">
        <div class="register-container">
            <h1 class="register-title">Crear Cuenta</h1>
            <form class="register-form" action="register.php" method="post">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" required>

                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Registrarse</button>
            </form>
            <div class="register-links">
                <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </div>
    </div>
    <?php if ($error): ?>
    <script>
        alert("<?= htmlspecialchars($error) ?>");
        window.location.href = "register.php";
    </script>
    <?php endif; ?>
    <?php if ($success): ?>
    <script>
        alert("<?= htmlspecialchars($success) ?>");
        window.location.href = "login.php";
    </script>
    <?php endif; ?>
    <footer>
        <p>&copy; 2023 Sport-FM. Todos los derechos reservados.</p>
    </footer>
    <script>
    document.querySelectorAll('.logo').forEach(function(logo) {
        logo.style.cursor = 'pointer';
        logo.addEventListener('click', function(e) {
            e.preventDefault();
            window.history.back();
        });
    });
    </script>
</body>
</html>