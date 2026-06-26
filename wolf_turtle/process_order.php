<?php
include 'config.php';

if (!isset($_SESSION['scanned_table_id'])) {
    die("Please scan QR code at your table first.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_data = json_decode($_POST['order_data'], true);
    $table_id = $_SESSION['scanned_table_id'];
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $total_amount = $_POST['total_amount'];
    $order_number = generateOrderNumber();
    
    $sql = "INSERT INTO orders (order_number, table_id, customer_name, total_amount, payment_status, order_status) 
            VALUES ('$order_number', $table_id, '$customer_name', $total_amount, 'pending', 'received')";
    
    if ($conn->query($sql)) {
        $order_id = $conn->insert_id;
        foreach ($order_data as $item) {
            $conn->query("INSERT INTO order_items (order_id, menu_item_id, quantity, price) 
                         VALUES ($order_id, {$item['id']}, {$item['quantity']}, {$item['price']})");
        }
        $conn->query("UPDATE tables SET status = 'occupied' WHERE id = $table_id");
        header("Location: payment.php?order_id=$order_id");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>