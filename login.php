<?php
session_start();
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $password = $_POST['password'];

    $conn = new mysqli('localhost', 'root', '', 'sport');
    if ($conn->connect_error) {
        $error = "Error de conexión.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM usuarios WHERE nombre = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['nombre'] = $nombre;
                if ($nombre === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no encontrado.";
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
    <title>Iniciar Sesión - Sport-FM</title>
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
        .login-container {
            max-width: 400px;
            margin: 3rem auto;
            background: #000000;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            border: 1.5px solid #e53935;
        }
        .login-title {
            color: #e53935;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-form label {
            font-weight: bold;
            color: #fff;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            background: #232323;
            color: #fff;
        }
        .login-form button {
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
        .login-form button:hover {
            background: #b71c1c;
        }
        .login-links {
            margin-top: 2rem;
            text-align: center;
        }
        .login-links a {
            color: #e53935;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s;
        }
        .login-links a:hover {
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
            <div class="nav-links">
                <a href="index.php">Inicio</a>
                <a href="login.php" class="active">Iniciar Sesión</a>
            </div>
        </nav>
    </header>
    <div class="main-content">
        <div class="login-container">
            <h1 class="login-title">Iniciar Sesión</h1>
            <form class="login-form" action="login.php" method="post">
                <label for="nombre">Nombre de usuario:</label>
                <input type="text" id="nombre" name="nombre" required>
                
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Ingresar</button>
            </form>
            <div class="login-links">
                <a href="register.php">¿No tienes cuenta? Regístrate</a>
            </div>
        </div>
    </div>
    <?php if ($error): ?>
    <script>
        alert("<?= htmlspecialchars($error) ?>");
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