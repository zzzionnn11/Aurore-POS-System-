<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AURORE Point of Sale</title>
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

$products = $conn->query("SELECT * FROM products ORDER BY product_name");
$lowStock = $conn->query("SELECT * FROM products WHERE stock < 5");
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 p-4">
            
            <!-- Low Stock Alert -->
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
            
            <div class="row">
                <!-- Products Column -->
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">PRODUCTS</div>
                        <div class="card-body row">
                            <?php while($p = $products->fetch_assoc()): ?>
                            <div class="col-4 mb-3">
                                <button class="btn btn-outline-primary w-100 text-start" 
                                        onclick="addToCart(<?php echo $p['id']; ?>, '<?php echo addslashes($p['product_name']); ?>', <?php echo $p['price']; ?>, <?php echo $p['stock']; ?>)">
                                    <strong><?php echo $p['product_name']; ?></strong><br>
                                    $<?php echo number_format($p['price'],2); ?><br>
                                    <small class="text-muted">Stock: <?php echo $p['stock']; ?></small>
                                </button>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Column -->
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">CURRENT SALE</div>
                        <div class="card-body">
                            <div id="cartItems" style="min-height: 150px;">
                                No items in cart
                            </div>
                            <div class="cart-total">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>TOTAL</span>
                                    <strong>$<span id="cartTotal">0.00</span></strong>
                                </div>
                                <!-- PAYMENT INPUT FIELD -->
                                <input type="number" id="paymentAmount" class="form-control mb-2" placeholder="Payment amount" step="0.01">
                                <div id="changeDisplay" class="small mb-2"></div>
                                <button class="btn btn-primary w-100 mb-2" onclick="processPayment()">PROCESS PAYMENT</button>
                                <button class="btn btn-secondary w-100" onclick="clearCart()">CLEAR CART</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for submitting cart data -->
<form id="paymentForm" method="POST" action="process_payment.php" style="display:none;">
    <input type="hidden" name="cart_data" id="cartDataInput">
</form>

<script src="assets/js/pos.js"></script>
</body>
</html>