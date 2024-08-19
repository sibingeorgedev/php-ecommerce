<?php
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../config/config.php';
include '../includes/header.php';

$product = new Product();

$productToEdit = null;
$isEditing = false;
$imageName = '';

if (isset($_GET['edit_id'])) {
    $productToEdit = $product->getProductById($_GET['edit_id']);
    $isEditing = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedFileTypes)) {
            echo 'Invalid file type. Please upload a JPG, PNG, or GIF image.';
            exit();
        }

        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        $uploadFileDir = '../uploads/products/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $imageName = $newFileName;
        } else {
            echo 'There was some error moving the file to upload directory.';
        }
    } else {
        $imageName = $productToEdit['image'] ?? '';
    }

    if (!empty($_POST['id'])) {
        $product->updateProduct($_POST['id'], $_POST['name'], $_POST['price'], $_POST['description'], $imageName);
        header("Location: admin_products.php?status=updated");
        exit();
    } else {
        $product->addProduct($_POST['name'], $_POST['price'], $_POST['description'], $imageName);
        header("Location: admin_products.php?status=added");
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    $product->deleteProduct($_GET['delete_id']);
    header("Location: admin_products.php?status=deleted");
    exit();
}

$products = $product->getAllProducts();
?>

<div class="admin-container">
    <h1>Manage Products</h1>

    <?php if (isset($_GET['status'])): ?>
        <div class="alert">
            <?php
            if ($_GET['status'] == 'added') echo "Product added successfully!";
            if ($_GET['status'] == 'updated') echo "Product updated successfully!";
            if ($_GET['status'] == 'deleted') echo "Product deleted successfully!";
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="admin_products.php" enctype="multipart/form-data" class="admin-form">
        <input type="hidden" name="id" value="<?php echo $productToEdit['id'] ?? ''; ?>">
        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $productToEdit['name'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Product Description</label>
            <textarea class="form-control" id="description" name="description" required><?php echo $productToEdit['description'] ?? ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Product Price</label>
            <input type="number" class="form-control" id="price" name="price" value="<?php echo $productToEdit['price'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="image">Product Image</label>
            <input type="file" class="form-control" id="image" name="image">
            <?php if ($isEditing && !empty($productToEdit['image'])): ?>
                <img src="../uploads/products/<?php echo $productToEdit['image']; ?>" alt="Product Image" class="preview-image">
            <?php endif; ?>
        </div>
        <button type="submit" name="save_product" class="btn-primary"><?php echo $isEditing ? 'Update' : 'Add'; ?> Product</button>
    </form>

    <h2>Product List</h2>
    <table class="product-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product) : ?>
            <tr>
                <td><?php echo $product['name']; ?></td>
                <td><?php echo $product['description']; ?></td>
                <td><?php echo $product['price']; ?></td>
                <td><img src="../uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-thumb"></td>
                <td>
                    <a href="admin_products.php?edit_id=<?php echo $product['id']; ?>" class="btn-warning">Edit</a>
                    <a href="admin_products.php?delete_id=<?php echo $product['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    .admin-container {
        width: 80%;
        margin: 40px auto;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .admin-container h1, h2 {
        text-align: center;
        color: #343a40;
    }

    .alert {
        background-color: #28a745;
        color: #fff;
        padding: 15px;
        text-align: center;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .admin-form {
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #343a40;
    }

    .form-group input, .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        font-size: 1rem;
    }

    .form-group input[type="file"] {
        padding: 3px;
    }

    .form-group textarea {
        resize: vertical;
    }

    .btn-primary {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 1rem;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .preview-image {
        display: block;
        margin: 10px 0;
        max-width: 100px;
        border-radius: 5px;
    }

    .product-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .product-table th, .product-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .product-table th {
        background-color: #343a40;
        color: #fff;
    }

    .product-table td img {
        max-width: 50px;
        border-radius: 5px;
    }

    .btn-warning, .btn-danger {
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
        color: #fff;
        font-size: 0.9rem;
        margin-right: 5px;
    }

    .btn-warning {
        background-color: #ffc107;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .btn-warning:hover {
        background-color: #e0a800;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }
</style>
