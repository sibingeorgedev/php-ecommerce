<?php
include '../includes/header.php';
require_once '../classes/Cart.php';
require_once '../classes/Product.php';

$cart = new Cart();
$cartItems = $cart->getCartItems();

$product = new Product();
?>

<div class="container">
    <h1>Your Cart</h1>
    <?php if (!empty($cartItems)) : ?>
        <ul class="cart-items">
            <?php foreach ($cartItems as $productId => $quantity) : ?>
                <?php $productDetails = $product->getProductById($productId); ?>
                <li>
                    <img src="../uploads/products/<?= $productDetails['image'] ?>" alt="<?= $productDetails['name'] ?>">
                    <span><?= $productDetails['name'] ?> - $<?= $productDetails['price'] ?> x <?= $quantity ?></span>
                    <a href="remove_from_cart.php?id=<?= $productId ?>" class="btn">Remove</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="checkout.php" class="btn">Proceed to Checkout</a>
    <?php else : ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
