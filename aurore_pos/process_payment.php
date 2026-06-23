<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

$data = json_decode($_POST['cart_data'], true);
$cart = $data['cart'];
$payment = $data['payment'];

$total = 0;
foreach($cart as $item) {
    $total += $item['price'] * $item['qty'];
}

if($payment < $total) {
    die("Payment insufficient. <a href='pos.php'>Go back</a>");
}

$change = $payment - $total;
$user_id = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    // Insert transaction
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, total_amount, payment_amount, change_amount, transaction_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iddd", $user_id, $total, $payment, $change);
    $stmt->execute();
    $trans_id = $stmt->insert_id;
    $stmt->close();

    // Insert items and update stock
    foreach($cart as $item) {
        $stmt = $conn->prepare("INSERT INTO transaction_items (transaction_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $trans_id, $item['id'], $item['qty'], $item['price']);
        $stmt->execute();
        $stmt->close();
        
        $conn->query("UPDATE products SET stock = stock - {$item['qty']} WHERE id = {$item['id']}");
    }
    $conn->commit();
    
    // Display receipt
    echo "<!DOCTYPE html><html><head><title>Receipt</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='assets/css/style.css'>
    <style>
        body { background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .receipt-container { max-width: 400px; margin: 0 auto; background: white; padding: 24px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .receipt-header { text-align: center; border-bottom: 1px solid #eee; padding-bottom: 16px; margin-bottom: 16px; }
        .receipt-header h2 { margin: 0; font-weight: 700; letter-spacing: 2px; }
        .receipt-header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        .receipt-items { margin-bottom: 20px; }
        .receipt-item { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; }
        .receipt-total { border-top: 1px dashed #ccc; padding-top: 12px; margin-top: 8px; }
        .total-line { display: flex; justify-content: space-between; font-weight: 700; margin-bottom: 6px; }
        .footer { text-align: center; margin-top: 24px; font-size: 11px; color: #888; border-top: 1px solid #eee; padding-top: 16px; }
        .btn-print { background: #000; color: white; border: none; padding: 10px 20px; width: 100%; margin-top: 20px; }
    </style>
    </head><body>
    <div class='receipt-container'>
        <div class='receipt-header'>
            <h2>AURORE</h2>
            <p>Luxury Streetwear</p>
            <p>Receipt #: " . str_pad($trans_id, 6, '0', STR_PAD_LEFT) . "<br>" . date('Y-m-d H:i:s') . "<br>Cashier: " . $_SESSION['full_name'] . "</p>
        </div>
        <div class='receipt-items'>";
        foreach($cart as $item) {
            echo "<div class='receipt-item'><span>" . $item['name'] . " x " . $item['qty'] . "</span><span>$" . number_format($item['price'] * $item['qty'], 2) . "</span></div>";
        }
        echo "</div><div class='receipt-total'>
            <div class='total-line'><span>TOTAL</span><span>$" . number_format($total,2) . "</span></div>
            <div class='total-line'><span>PAYMENT</span><span>$" . number_format($payment,2) . "</span></div>
            <div class='total-line'><span>CHANGE</span><span>$" . number_format($change,2) . "</span></div>
        </div>
        <div class='footer'>Thank you for shopping at AURORE!<br>No returns after 7 days</div>
        <button class='btn-print' onclick='window.print()'>PRINT RECEIPT</button>
        <a href='pos.php' style='display:block; text-align:center; margin-top:12px; color:#000;'>New Sale</a>
    </div>
    </body></html>";
    
} catch(Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}
?>