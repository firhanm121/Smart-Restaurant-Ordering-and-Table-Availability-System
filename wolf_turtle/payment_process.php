<?php
// ============================================
// payment_process.php - Process Payment
// ============================================
include 'config.php';

if (isset($_POST['pay']) || isset($_POST['payment_method'])) {
    $order_id = $_POST['order_id'];
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'qr';
    
    // For card payment, validate card details (dummy validation)
    if ($payment_method == 'card') {
        $card_number = isset($_POST['card_number']) ? $_POST['card_number'] : '';
        $expiry_date = isset($_POST['expiry_date']) ? $_POST['expiry_date'] : '';
        $cvv = isset($_POST['cvv']) ? $_POST['cvv'] : '';
        $card_name = isset($_POST['card_name']) ? $_POST['card_name'] : '';
        
        // Dummy validation (just check if fields are filled)
        if (empty($card_number) || empty($expiry_date) || empty($cvv) || empty($card_name)) {
            header("Location: payment.php?order_id=$order_id&error=invalid_card");
            exit();
        }
        
        // Dummy: Accept any card that passes format
        // In real system, you would connect to payment gateway API
    }
    
    // Update payment status
    $conn->query("UPDATE orders SET payment_status = 'paid', payment_method = '$payment_method' WHERE id = $order_id");
    
    // Redirect to success page
    header("Location: order_success.php?order_id=$order_id");
    exit();
} else {
    header("Location: order.php");
    exit();
}
?>