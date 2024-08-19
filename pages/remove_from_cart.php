<?php
session_start();
require_once '../classes/Cart.php';

if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    $cart = new Cart();
    $cart->removeFromCart($productId);

    header("Location: cart.php");
    exit();
}
?>
