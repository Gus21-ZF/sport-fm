<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Minero - Acceso Trabajadores</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="%23222" width="100" height="100"/><path fill="%23333" d="M0 0L100 100M100 0L0 100" stroke-width="0.5"/></svg>');
            background-size: cover;
            padding: 20px;
        }
        
        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background-color: #e67e22;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            padding: 10px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            border-color: #4a90e2;
            outline: none;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #e67e22;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #d35400;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-footer a {
            color: #4a90e2;
            text-decoration: none;
            font-size: 14px;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .language-selector {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
            color: #777;
        }
        
        .language-selector select {
            padding: 5px;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }
            
            .login-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">MINERA<br>ANDES<br>CHILE</div>
            <h1>Portal de Trabajadores Mineros</h1>
            <p>Ingrese sus credenciales para acceder al sistema</p>
        </div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="employeeId">Número de Empleado</label>
                <input type="text" id="employeeId" name="employeeId" required placeholder="Ej: MIN-4582">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
            </div>
            
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
        
        <div class="login-footer">
            <a href="#">¿Olvidó su contraseña?</a> | 
            <a href="#">Solicitar acceso</a>
        </div>
        
        <div class="language-selector">
            <label for="language">Idioma:</label>
            <select id="language">
                <option value="es">Español</option>
                <option value="en">English</option>
                <option value="pt">Português</option>
            </select>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const employeeId = document.getElementById('employeeId').value;
            const password = document.getElementById('password').value;
            
            // Validación básica
            if (!employeeId || !password) {
                alert('Por favor, complete todos los campos');
                return;
            }
            
            // Simulación de login exitoso
            alert('Login exitoso. Redirigiendo al portal...');
            
            // En un caso real, aquí se enviarían los datos al servidor
            console.log('Credenciales ingresadas:', {employeeId, password});
            
            // Redirección (simulada)
            // window.location.href = 'portal.html';
        });
        
        // Selector de idioma
        document.getElementById('language').addEventListener('change', function() {
            alert('Idioma cambiado a: ' + this.options[this.selectedIndex].text);
            // En una implementación real, cambiaría el idioma de la interfaz
        });
    </script>
</body>
</html>
