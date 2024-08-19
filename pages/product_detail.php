<?php
include '../includes/header.php';
require_once '../classes/Product.php';
require_once '../classes/Cart.php';

$productId = $_GET['id'];
$product = new Product();
$productDetails = $product->getProductById($productId);
?>

<div class="container">
    <div class="product-details">
        <img src="../uploads/products/<?= $productDetails['image'] ?>" alt="<?= $productDetails['name'] ?>" class="product-image">
        <div class="product-info">
            <h1><?= $productDetails['name'] ?></h1>
            <p class="product-description"><?= $productDetails['description'] ?></p>
            <p class="product-price">$<?= $productDetails['price'] ?></p>
            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                <input type="hidden" name="product_id" value="<?= $productDetails['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
            </form>
        </div>
    </div>
</div>

