<?php
include '../includes/header.php';
require_once '../classes/Product.php';

$product = new Product();
$products = $product->getAllProducts();
?>

<div class="container">
    <h1>Our Products</h1>
    <div class="product-list">
        <?php foreach ($products as $product) : ?>
            <div class="product">
                <img src="../uploads/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                <h2><?= $product['name'] ?></h2>
                <p>$<?= $product['price'] ?></p>
                <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
