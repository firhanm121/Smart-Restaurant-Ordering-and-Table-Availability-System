<?php
include 'config.php';

// Get available tables count
$available_tables = $conn->query("SELECT COUNT(*) as count FROM tables WHERE status = 'available'")->fetch_assoc()['count'];

// Get popular menu items
$popular_menu = $conn->query("SELECT * FROM menu_items WHERE is_popular = 1 AND is_available = 1 LIMIT 6");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wolf & Turtle</title>
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
            background: #ffffff;
            color: #1a1a1a;
        }
        
        /* Header with Logo - WARNA PUTIH */
        .header {
            background: #ffffff;
            padding: 15px 5%;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .logo {
            max-height: 60px;
            width: auto;
        }
        
        /* Navigation - WARNA PUTIH */
        .nav {
            background: #ffffff;
            padding: 12px 5%;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .nav a {
            color: #333;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
            padding: 8px 0;
            transition: 0.3s;
        }
        
        .nav a:hover {
            color: #1a1a1a;
            border-bottom: 2px solid #1a1a1a;
        }
        
        /* Hero Section */
        .hero {
            background: #f8f8f8;
            padding: 80px 5%;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: #1a1a1a;
        }
        
        .hero p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #1a1a1a;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #333;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            color: #1a1a1a;
            border: 2px solid #1a1a1a;
        }
        
        .btn-outline:hover {
            background: #1a1a1a;
            color: white;
        }
        
        /* Section */
        .section {
            padding: 60px 5%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 40px;
            color: #1a1a1a;
        }
        
        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .menu-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            transition: 0.3s;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .menu-image {
            width: 100%;
            height: 180px;
            background: #f0f0f0;
            background-size: cover;
            background-position: center;
        }
        
        .menu-info {
            padding: 20px;
        }
        
        .menu-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .menu-price {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin-top: 10px;
        }
        
        .popular-badge {
            background: #1a1a1a;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 8px;
        }
        
        /* Table Status */
        .table-status {
            background: #f8f8f8;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            margin-top: 40px;
        }
        
        /* Footer */
        .footer {
            background: #1a1a1a;
            color: #888;
            text-align: center;
            padding: 40px 5%;
            margin-top: 60px;
        }
        
        /* QR Hint */
        .qr-hint {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
        }
        
        .qr-hint p {
            color: #666;
            font-size: 14px;
        }
        
        .qr-hint strong {
            color: #1a1a1a;
        }
        
        @media (max-width: 768px) {
            .hero h1 { font-size: 32px; }
            .menu-grid { grid-template-columns: 1fr; }
            .logo { max-height: 45px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/logo.png" alt="Wolf & Turtle" class="logo" 
             onerror="this.onerror=null; this.alt='WOLF & TURTLE'; this.style.fontSize='24px'; this.style.fontWeight='bold'; this.style.color='#1a1a1a'">
    </div>
    
    <!-- Navigation - TANPA ORDER NOW BUTTON -->
    <div class="nav">
        <a href="index.php">HOME</a>
        <a href="tables.php">TABLE STATUS</a>
        <a href="login.php">STAFF LOGIN</a>
    </div>
    
    <div class="hero">
        <h1>Specialty Coffee & Comfort Food</h1>
        <p>Open Daily • 8:00 AM – 7:00 PM • 100% Halal</p>
        <p><?php echo $available_tables; ?> tables available now!</p>
        <div style="margin-top: 20px;">
            <!-- ORDER NOW BUTTON REMOVED - Only Check Tables button -->
            <a href="tables.php" class="btn">Check Tables</a>
        </div>
    </div>
    
    <div class="section">
        <h2 class="section-title">Popular Menu</h2>
        <div class="menu-grid">
            <?php if($popular_menu && $popular_menu->num_rows > 0): ?>
                <?php while($item = $popular_menu->fetch_assoc()): ?>
                <div class="menu-card">
                    <div class="menu-image" style="background-image: url('<?php echo $item['image_path'] ?: 'images/default.jpg'; ?>')"></div>
                    <div class="menu-info">
                        <div class="menu-name">
                            <?php echo $item['name']; ?>
                            <span class="popular-badge">POPULAR</span>
                        </div>
                        <div style="color: #666; font-size: 14px; margin: 8px 0;"><?php echo $item['description']; ?></div>
                        <div class="menu-price">RM <?php echo number_format($item['price'], 2); ?></div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center;">No menu items available.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="table-status">
        <h3>Real-Time Table Availability</h3>
        <p>Check available tables before you arrive</p>
        <a href="tables.php" class="btn" style="margin-top: 20px;">View Table Status</a>
    </div>
    
    <!-- QR Hint - Inform customer how to order -->
    <div class="qr-hint" style="max-width: 1200px; margin: 0 auto 40px auto; padding: 20px;">
        <p>📱 <strong>How to Order?</strong></p>
        <p>Please sit at any available table and scan the QR code to order.</p>
        <p>No QR code? Ask our staff for assistance.</p>
    </div>
    
    <div class="footer">
        <p>© 2026 Wolf & Turtle Coffee. All rights reserved.</p>
        <p style="margin-top: 10px;">Bukit Tunku • The Exchange TRX • Wangsa Maju</p>
    </div>
</body>
</html>