<?php
// ============================================
// payment.php - Payment Page with Card & QR Options
// ============================================
include 'config.php';

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;

// Get order details
$order = $conn->query("SELECT * FROM orders WHERE id = $order_id")->fetch_assoc();

if (!$order) {
    header("Location: order.php");
    exit();
}

// Get order items
$order_items = $conn->query("
    SELECT oi.*, mi.name 
    FROM order_items oi 
    JOIN menu_items mi ON oi.menu_item_id = mi.id 
    WHERE oi.order_id = $order_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment - Wolf & Turtle</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .payment-container {
            max-width: 550px;
            width: 100%;
        }
        
        .payment-card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo img {
            max-height: 50px;
            width: auto;
        }
        
        h2 {
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 25px;
            font-size: 24px;
        }
        
        .order-number {
            background: #f5f5f5;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
            font-weight: 500;
        }
        
        .order-summary {
            background: #fafafa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
        }
        
        .order-summary h3 {
            margin-bottom: 15px;
            color: #1a1a1a;
            font-size: 18px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #1a1a1a;
        }
        
        /* Payment Method Tabs */
        .payment-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }
        
        .payment-tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            color: #666;
            transition: 0.3s;
            border-radius: 10px;
        }
        
        .payment-tab.active {
            color: #1a1a1a;
            background: #f0f0f0;
        }
        
        /* Payment Content */
        .payment-content {
            display: none;
        }
        
        .payment-content.active {
            display: block;
        }
        
        /* Card Form */
        .card-form {
            background: #fafafa;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: 0.3s;
        }
        
        .form-group input:focus {
            border-color: #1a1a1a;
            outline: none;
            box-shadow: 0 0 0 2px rgba(26,26,26,0.1);
        }
        
        .row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        /* QR Section */
        .qr-section {
            text-align: center;
            padding: 20px;
        }
        
        .qr-code {
            width: 220px;
            height: 220px;
            margin: 0 auto 20px;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px dashed #1a1a1a;
            border-radius: 16px;
            overflow: hidden;
        }
        
        .qr-code img {
            max-width: 200px;
            max-height: 200px;
        }
        
        .qr-note {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .ref-number {
            font-family: monospace;
            background: #f5f5f5;
            padding: 8px;
            border-radius: 6px;
            font-size: 14px;
            color: #1a1a1a;
        }
        
        /* Button */
        .pay-btn {
            width: 100%;
            padding: 15px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }
        
        .pay-btn:hover {
            background: #333;
            transform: translateY(-2px);
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link a:hover {
            color: #1a1a1a;
        }
        
        .payment-note {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .error-message {
            color: #c62828;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-card">
            <div class="logo">
                <img src="images/logo.png" alt="Wolf & Turtle" onerror="this.onerror=null; this.alt='WOLF & TURTLE'; this.style.fontSize='20px'; this.style.fontWeight='bold'">
            </div>
            
            <h2>Complete Payment</h2>
            
            <div class="order-number">
                Order #: <?php echo $order['order_number']; ?>
            </div>
            
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php while($item = $order_items->fetch_assoc()): ?>
                <div class="order-item">
                    <span><?php echo $item['name']; ?> x<?php echo $item['quantity']; ?></span>
                    <span>RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
                <?php endwhile; ?>
                
                <div class="total-row">
                    <span>Total Amount</span>
                    <span>RM <?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
            
            <!-- Payment Method Tabs -->
            <div class="payment-tabs">
                <button class="payment-tab active" onclick="switchPayment('card')">💳 Card Payment</button>
                <button class="payment-tab" onclick="switchPayment('qr')">📱 QR Payment</button>
            </div>
            
            <!-- CARD PAYMENT CONTENT -->
            <div id="cardPayment" class="payment-content active">
                <div class="card-form">
                    <form method="POST" action="payment_process.php" id="cardForm">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="card">
                        
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCardNumber(this)">
                            <div id="cardNumberError" class="error-message">Please enter valid 16-digit card number</div>
                        </div>
                        
                        <div class="row-2col">
                            <div class="form-group">
                                <label>MM / YY</label>
                                <input type="text" id="expiryDate" name="expiry_date" placeholder="MM/YY" maxlength="5" oninput="formatExpiry(this)">
                                <div id="expiryError" class="error-message">Please enter valid expiry date (MM/YY)</div>
                            </div>
                            <div class="form-group">
                                <label>CVV / CVC</label>
                                <input type="password" id="cvv" name="cvv" placeholder="123" maxlength="3" oninput="validateCVV(this)">
                                <div id="cvvError" class="error-message">Please enter valid 3-digit CVV</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Cardholder Name</label>
                            <input type="text" id="cardName" name="card_name" placeholder="As per card">
                            <div id="nameError" class="error-message">Please enter cardholder name</div>
                        </div>
                        
                        <button type="button" class="pay-btn" onclick="validateCard()">💳 Pay Now</button>
                    </form>
                </div>
            </div>
            
            <!-- QR PAYMENT CONTENT -->
            <div id="qrPayment" class="payment-content">
                <div class="qr-section">
                    <div class="qr-code">
                        <?php
                        $qr_data = "PAYMENT-" . $order['order_number'];
                        $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_data);
                        ?>
                        <img src="<?php echo $qr_url; ?>" 
                             alt="Payment QR Code"
                             onerror="this.onerror=null; this.src='https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo urlencode($qr_data); ?>'">
                    </div>
                    <div class="qr-note">
                        Scan QR code to pay (Dummy Payment)
                    </div>
                    <div class="ref-number">
                        Ref: <?php echo $order['order_number']; ?>
                    </div>
                </div>
                
                <form method="POST" action="payment_process.php" id="qrForm">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <input type="hidden" name="payment_method" value="qr">
                    <button type="submit" name="pay" class="pay-btn">✅ Confirm QR Payment</button>
                </form>
            </div>
            
            <div class="back-link">
                <a href="order.php">← Back to Order</a>
            </div>
            
            <div class="payment-note">
                * This is a dummy payment system for academic project purposes
            </div>
        </div>
    </div>
    
    <script>
        // Switch between payment methods
        function switchPayment(method) {
            // Update tabs
            document.querySelectorAll('.payment-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update content
            document.getElementById('cardPayment').classList.remove('active');
            document.getElementById('qrPayment').classList.remove('active');
            
            if (method === 'card') {
                document.getElementById('cardPayment').classList.add('active');
            } else {
                document.getElementById('qrPayment').classList.add('active');
            }
        }
        
        // Format card number with spaces
        function formatCardNumber(input) {
            let value = input.value.replace(/\s/g, '');
            if (value.length > 16) value = value.slice(0, 16);
            let formatted = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            input.value = formatted;
            
            // Validate
            let isValid = value.length === 16;
            document.getElementById('cardNumberError').style.display = isValid ? 'none' : 'block';
            return isValid;
        }
        
        // Format expiry date
        function formatExpiry(input) {
            let value = input.value.replace(/\//g, '');
            if (value.length > 4) value = value.slice(0, 4);
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2);
            }
            input.value = value;
            
            // Validate
            let isValid = value.length === 5 && /^\d{2}\/\d{2}$/.test(value);
            document.getElementById('expiryError').style.display = isValid ? 'none' : 'block';
            return isValid;
        }
        
        // Validate CVV
        function validateCVV(input) {
            let value = input.value;
            let isValid = value.length === 3 && /^\d{3}$/.test(value);
            document.getElementById('cvvError').style.display = isValid ? 'none' : 'block';
            return isValid;
        }
        
        // Validate cardholder name
        function validateName() {
            let name = document.getElementById('cardName').value.trim();
            let isValid = name.length > 0;
            document.getElementById('nameError').style.display = isValid ? 'none' : 'block';
            return isValid;
        }
        
        // Validate all card fields
        function validateCard() {
            let cardNum = document.getElementById('cardNumber').value.replace(/\s/g, '');
            let expiry = document.getElementById('expiryDate').value;
            let cvv = document.getElementById('cvv').value;
            let name = document.getElementById('cardName').value.trim();
            
            let isValid = true;
            
            if (cardNum.length !== 16) {
                document.getElementById('cardNumberError').style.display = 'block';
                isValid = false;
            }
            
            if (!/^\d{2}\/\d{2}$/.test(expiry)) {
                document.getElementById('expiryError').style.display = 'block';
                isValid = false;
            }
            
            if (!/^\d{3}$/.test(cvv)) {
                document.getElementById('cvvError').style.display = 'block';
                isValid = false;
            }
            
            if (name.length === 0) {
                document.getElementById('nameError').style.display = 'block';
                isValid = false;
            }
            
            if (isValid) {
                // Submit the form
                document.getElementById('cardForm').submit();
            } else {
                alert('Please fill in all card details correctly.');
            }
        }
        
        // Validate on input
        document.getElementById('cardNumber').addEventListener('input', function() {
            formatCardNumber(this);
        });
        document.getElementById('expiryDate').addEventListener('input', function() {
            formatExpiry(this);
        });
        document.getElementById('cvv').addEventListener('input', function() {
            validateCVV(this);
        });
        document.getElementById('cardName').addEventListener('input', function() {
            validateName();
        });
    </script>
</body>
</html>