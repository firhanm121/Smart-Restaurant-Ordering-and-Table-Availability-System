<?php
include 'config.php';
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
$order = $conn->query("SELECT * FROM orders WHERE id = $order_id")->fetch_assoc();
if (!$order) { header("Location: index.php"); exit(); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Success - Wolf & Turtle</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f8f8f8; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .success-card { background: white; max-width: 500px; width: 100%; padding: 50px; text-align: center; border-radius: 16px; border: 1px solid #ddd; }
        .icon { font-size: 80px; margin-bottom: 20px; }
        h1 { margin-bottom: 20px; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 30px; background: #1a1a1a; color: white; text-decoration: none; border-radius: 30px; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon">✅</div>
        <h1>Order Successful!</h1>
        <p>Order #: <?php echo $order['order_number']; ?></p>
        <p>Amount Paid: RM <?php echo number_format($order['total_amount'], 2); ?></p>
        <p>Your order is being prepared.</p>
        <a href="order.php" class="btn">Order Again</a>
        <a href="index.php" class="btn" style="background: #666; margin-left: 10px;">Home</a>
    </div>
</body>
</html>