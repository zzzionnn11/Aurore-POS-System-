<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AURORE Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

// Handle Add Product
if(isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image = trim($_POST['image_url']);
    if(!empty($name) && $price > 0 && $stock >= 0) {
        $conn->query("INSERT INTO products (product_name, price, stock, image_url) VALUES ('$name', '$price', '$stock', '$image')");
        $message = "Product added successfully!";
    } else {
        $error = "Please fill all fields correctly (name, price > 0, stock >= 0).";
    }
}

// Handle Edit Product
if(isset($_POST['edit_product'])) {
    $id = intval($_POST['product_id']);
    $name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image = trim($_POST['image_url']);
    if(!empty($name) && $price > 0 && $stock >= 0) {
        $conn->query("UPDATE products SET product_name='$name', price='$price', stock='$stock', image_url='$image' WHERE id=$id");
        $message = "Product updated!";
    } else {
        $error = "Invalid data for update.";
    }
}

// Handle Delete – with friendly error if product has sales
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Check if product appears in any transaction_items
    $check = $conn->query("SELECT COUNT(*) as count FROM transaction_items WHERE product_id = $id");
    $row = $check->fetch_assoc();
    if($row['count'] > 0) {
        $error = "Cannot delete product: it has been sold in " . $row['count'] . " transaction(s). To remove from POS, set stock to 0 instead.";
    } else {
        $conn->query("DELETE FROM products WHERE id = $id");
        $message = "Product deleted successfully.";
    }
}

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if($search) {
    $products = $conn->query("SELECT * FROM products WHERE product_name LIKE '%$search%' ORDER BY product_name");
} else {
    $products = $conn->query("SELECT * FROM products ORDER BY product_name");
}
$lowStock = $conn->query("SELECT * FROM products WHERE stock < 5");
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><i class="fas fa-boxes"></i> Inventory Management</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> ADD NEW PRODUCT
                </button>
            </div>

            <?php if(isset($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($lowStock->num_rows > 0): ?>
                <div class="alert alert-warning">
                    <strong><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</strong>
                    <ul class="mb-0 mt-2">
                        <?php while($alert = $lowStock->fetch_assoc()): ?>
                            <li><?php echo $alert['product_name']; ?>: <strong><?php echo $alert['stock']; ?> remaining</strong></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Search Bar -->
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
                </div>
            </form>

            <!-- Inventory Table -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5>Current Inventory</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered product-table mb-0">
                        <thead class="table-light">
                            <tr><th>Image</th><th>Product Name</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th>
                        </thead>
                        <tbody>
                            <?php if($products->num_rows > 0): ?>
                                <?php while($p = $products->fetch_assoc()): ?>
                                    <tr class="<?php echo $p['stock'] < 5 ? 'low-stock-row' : ''; ?>">
                                        <td>
                                            <?php if($p['image_url']): ?>
                                                <img src="<?php echo $p['image_url']; ?>" width="50" height="50">
                                            <?php else: ?>
                                                <i class="fas fa-tshirt fa-2x text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($p['product_name']); ?></strong></td>
                                        <td>$<?php echo number_format($p['price'],2); ?></td>
                                        <td><?php echo $p['stock']; ?></td>
                                        <td>
                                            <?php if($p['stock'] == 0): ?>
                                                <span class="badge bg-dark">No Stock</span>
                                            <?php elseif($p['stock'] < 5): ?>
                                                <span class="badge bg-danger">Low Stock</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">In Stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?edit_id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal" onclick="setEditData(<?php echo htmlspecialchars(json_encode($p)); ?>)">Edit</a>
                                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $p['id']; ?>, '<?php echo addslashes($p['product_name']); ?>')">Delete</button>
                                          </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No products found.<?php echo ' '; ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="product_name" class="form-control mb-2" placeholder="Product Name" required>
                    <input type="number" name="price" step="0.01" class="form-control mb-2" placeholder="Price" required>
                    <input type="number" name="stock" class="form-control mb-2" placeholder="Quantity" required>
                    <input type="text" name="image_url" class="form-control" placeholder="Image URL (optional)">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="edit_id">
                    <input type="text" name="product_name" id="edit_name" class="form-control mb-2" required>
                    <input type="number" name="price" id="edit_price" step="0.01" class="form-control mb-2" required>
                    <input type="number" name="stock" id="edit_stock" class="form-control mb-2" required>
                    <input type="text" name="image_url" id="edit_image" class="form-control" placeholder="Image URL">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_product" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setEditData(product) {
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_name').value = product.product_name;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_stock').value = product.stock;
    document.getElementById('edit_image').value = product.image_url || '';
}

function confirmDelete(productId, productName) {
    if (confirm(`Are you sure you want to delete "${productName}"?`)) {
        window.location.href = `?delete=${productId}`;
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>