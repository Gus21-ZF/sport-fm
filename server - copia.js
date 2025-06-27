const express = require('express');
const path = require('path');
const app = express();

// Servir archivos estáticos (html, css, js, imágenes)
app.use(express.static(__dirname));

// Página principal
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// Puerto
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});