<div class="col-md-3 col-lg-2 px-0 sidebar">
    <div class="text-center py-4">
    <img src="assets/images/logoDesign.png" width="60" alt="AURORE">
    <h4 class="text-white mt-2">AURORE</h4>
    </div>
    <nav class="nav flex-column px-3">
        <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a class="nav-link" href="pos.php"><i class="fas fa-shopping-cart"></i> Point of Sale</a>
        <a class="nav-link" href="inventory.php"><i class="fas fa-boxes"></i> Inventory</a>
        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <div class="position-absolute bottom-0 p-3 text-white small">
        Welcome, <?php echo $_SESSION['full_name']; ?>
    </div>
</div>