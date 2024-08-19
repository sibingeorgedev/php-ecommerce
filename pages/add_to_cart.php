<?php
session_start();
require_once '../classes/Cart.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);

    $cart = new Cart();
    $cart->addToCart($productId, $quantity);

    header("Location: cart.php");
    exit();
}
?>
