<?php
// agregar_carrito.php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['nombre'])) {
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}

$usuario = $_SESSION['nombre'];

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (isset($_POST['id']) && isset($_POST['cantidad'])) {
    $id = intval($_POST['id']);
    $cantidad = intval($_POST['cantidad']);

    // Sumar cantidad al carrito en sesión
    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] += $cantidad;
    } else {
        $_SESSION['carrito'][$id] = $cantidad;
    }

    // Devuelve la cantidad total en el carrito para ese producto
    echo json_encode([
        'success' => true,
        'enCarrito' => $_SESSION['carrito'][$id]
    ]);
    exit;
}

echo json_encode(['success' => false, 'msg' => 'Datos incompletos']);