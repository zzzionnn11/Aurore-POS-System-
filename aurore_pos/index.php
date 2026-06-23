<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AURORE Dashboard</title>
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

// Fetch data inside body
$products = $conn->query("SELECT * FROM products ORDER BY product_name");
$lowStock = $conn->query("SELECT * FROM products WHERE stock < 5");
$totalValue = $conn->query("SELECT SUM(price * stock) as total FROM products")->fetch_assoc()['total'];
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 p-4">
            <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
            
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <div class="card-stats">
                        <h6>TOTAL PRODUCTS</h6>
                        <h2><?php echo $products->num_rows; ?></h2>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card-stats">
                        <h6>LOW STOCK ITEMS</h6>
                        <h2><?php echo $lowStock->num_rows; ?></h2>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card-stats">
                        <h6>INVENTORY VALUE</h6>
                        <h2>$<?php echo number_format($totalValue,2); ?></h2>
                    </div>
                </div>
            </div>

            <?php if($lowStock->num_rows > 0): ?>
            <div class="alert alert-warning">
                <strong><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</strong>
                <ul class="mb-0 mt-2">
                    <?php while($row = $lowStock->fetch_assoc()): ?>
                        <li><?php echo $row['product_name']; ?>: <strong><?php echo $row['stock']; ?> remaining</strong></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="row mt-4">
                <h5>Current Inventory</h5>
                <?php 
                $products->data_seek(0); // reset pointer
                while($p = $products->fetch_assoc()): 
                ?>
                    <div class="col-md-3 mb-3">
                        <div class="product-card p-2">
                            <strong><?php echo $p['product_name']; ?></strong><br>
                            $<?php echo $p['price']; ?><br>
                            Stock: 
                            <span class="<?php echo $p['stock'] < 5 ? 'stock-low' : 'stock-normal'; ?>">
                                <?php echo $p['stock']; ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>