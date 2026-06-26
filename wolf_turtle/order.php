<?php
include 'config.php';

// Check if QR scanned (for dine-in)
$is_scanned = isset($_SESSION['scanned_table_id']);
$table_id = $is_scanned ? $_SESSION['scanned_table_id'] : null;
$table_number = $is_scanned ? $_SESSION['scanned_table_number'] : '';

// Get ONLY available menu items
$menu_items = $conn->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY category, name");
$categories = $conn->query("SELECT DISTINCT category FROM menu_items WHERE is_available = 1 ORDER BY category");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order - Wolf & Turtle</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        
        body {
            background: #f8f8f8;
            color: #1a1a1a;
        }
        
        .header {
            background: #ffffff;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .logo {
            max-height: 55px;
            width: auto;
        }
        
        .table-info {
            background: #f0f0f0;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
        }
        
        .menu-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #ddd;
        }
        
        .cart-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #ddd;
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        
        .category-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .category-tab {
            padding: 8px 20px;
            background: #f0f0f0;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }
        
        .category-tab.active {
            background: #1a1a1a;
            color: white;
        }
        
        .menu-items {
            display: grid;
            gap: 15px;
        }
        
        .menu-card {
            display: flex;
            gap: 15px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 10px;
        }
        
        .menu-image {
            width: 80px;
            height: 80px;
            background: #f0f0f0;
            border-radius: 8px;
            background-size: cover;
            background-position: center;
        }
        
        .menu-info {
            flex: 1;
        }
        
        .menu-name {
            font-weight: bold;
            font-size: 16px;
        }
        
        .menu-price {
            color: #1a1a1a;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .add-btn {
            background: #1a1a1a;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            align-self: center;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #1a1a1a;
            font-weight: bold;
            font-size: 18px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .place-order-btn {
            width: 100%;
            padding: 15px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        
        .empty-cart {
            text-align: center;
            color: #999;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/logo.png" alt="Wolf & Turtle" class="logo" 
             onerror="this.onerror=null; this.alt='WOLF & TURTLE'; this.style.fontSize='20px'; this.style.fontWeight='bold'; this.style.color='#1a1a1a'">
    </div>
    
    <?php if($is_scanned): ?>
    <div class="table-info">🍽️ You are ordering at TABLE <?php echo $table_number; ?></div>
    <?php else: ?>
    <div class="table-info">📱 Please scan QR code at your table to order</div>
    <?php endif; ?>
    
    <div class="container">
        <div class="menu-section">
            <h2>📋 Menu</h2>
            <div class="category-tabs">
                <button class="category-tab active" onclick="filterMenu('all')">All</button>
                <?php while($cat = $categories->fetch_assoc()): ?>
                <button class="category-tab" onclick="filterMenu('<?php echo $cat['category']; ?>')"><?php echo $cat['category']; ?></button>
                <?php endwhile; ?>
            </div>
            <div class="menu-items" id="menuItems">
                <?php while($item = $menu_items->fetch_assoc()): ?>
                <div class="menu-card" data-category="<?php echo $item['category']; ?>">
                    <div class="menu-image" style="background-image: url('<?php echo $item['image_path'] ?: 'images/default.jpg'; ?>')"></div>
                    <div class="menu-info">
                        <div class="menu-name"><?php echo $item['name']; ?></div>
                        <div style="font-size: 12px; color: #666;"><?php echo $item['description']; ?></div>
                        <div class="menu-price">RM <?php echo number_format($item['price'], 2); ?></div>
                    </div>
                    <button class="add-btn" onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo $item['name']; ?>', <?php echo $item['price']; ?>)">+ Add</button>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <div class="cart-section">
            <h3>🛒 Your Order</h3>
            <div id="cartItems" class="empty-cart">No items in cart</div>
            <div class="cart-total" id="cartTotal">Total: RM 0.00</div>
            <form method="POST" action="process_order.php" onsubmit="return prepareOrder()">
                <input type="hidden" name="order_data" id="orderData">
                <input type="hidden" name="total_amount" id="totalAmount">
                <div class="form-group">
                    <label>Your Name:</label>
                    <input type="text" name="customer_name" required placeholder="Enter your name">
                </div>
                <button type="submit" class="place-order-btn">✅ Place Order</button>
            </form>
        </div>
    </div>
    
    <script>
        let cart = [];
        
        function addToCart(id, name, price) {
            let existing = cart.find(item => item.id === id);
            if (existing) existing.quantity++;
            else cart.push({id, name, price, quantity: 1});
            updateCart();
        }
        
        function updateCart() {
            let html = '', total = 0;
            cart.forEach(item => {
                total += item.price * item.quantity;
                html += `<div class="cart-item"><div><strong>${item.name}</strong><br><small>RM ${item.price.toFixed(2)} x ${item.quantity}</small></div>
                <div><button onclick="updateQuantity(${item.id}, -1)">-</button><span>${item.quantity}</span><button onclick="updateQuantity(${item.id}, 1)">+</button><button onclick="removeItem(${item.id})">✖</button></div></div>`;
            });
            document.getElementById('cartItems').innerHTML = html || '<div class="empty-cart">No items in cart</div>';
            document.getElementById('cartTotal').innerHTML = `Total: RM ${total.toFixed(2)}`;
            document.getElementById('totalAmount').value = total.toFixed(2);
        }
        
        function updateQuantity(id, change) {
            let item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) cart = cart.filter(i => i.id !== id);
                updateCart();
            }
        }
        
        function removeItem(id) { cart = cart.filter(i => i.id !== id); updateCart(); }
        
        function filterMenu(category) {
            document.querySelectorAll('.menu-card').forEach(item => {
                item.style.display = (category === 'all' || item.dataset.category === category) ? 'flex' : 'none';
            });
            document.querySelectorAll('.category-tab').forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');
        }
        
        function prepareOrder() {
            if (cart.length === 0) { alert('Please add items to your cart'); return false; }
            document.getElementById('orderData').value = JSON.stringify(cart);
            return true;
        }
    </script>
</body>
</html>