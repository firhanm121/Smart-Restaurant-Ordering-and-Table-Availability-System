<?php
include 'config.php';

$tables = $conn->query("SELECT * FROM tables ORDER BY table_number");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Table Status - Wolf & Turtle</title>
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
            background: #f8f8f8;
            color: #1a1a1a;
        }
        
        /* Header dengan Logo - WARNA PUTIH */
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
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #1a1a1a;
            font-size: 32px;
        }
        
        /* Legend */
        .legend {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        
        .legend-color.available {
            background: #e8f5e9;
            border: 2px solid #2e7d32;
        }
        
        .legend-color.occupied {
            background: #ffebee;
            border: 2px solid #c62828;
        }
        
        /* Tables Grid */
        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        
        .table-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: 0.3s;
            background: white;
        }
        
        .table-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .table-card.available {
            border-left: 4px solid #2e7d32;
        }
        
        .table-card.occupied {
            border-left: 4px solid #c62828;
            opacity: 0.8;
        }
        
        .table-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #1a1a1a;
        }
        
        .table-detail {
            font-size: 14px;
            color: #666;
            margin: 8px 0;
        }
        
        .table-status {
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 12px;
            font-weight: bold;
        }
        
        .status-available {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #2e7d32;
        }
        
        .status-occupied {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #c62828;
        }
        
        /* QR Hint - Info cara order */
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
        
        /* Footer */
        .footer {
            background: #1a1a1a;
            color: #888;
            text-align: center;
            padding: 40px 5%;
            margin-top: 60px;
        }
        
        @media (max-width: 768px) {
            .tables-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .table-number {
                font-size: 22px;
            }
            .logo {
                max-height: 45px;
            }
        }
        
        @media (max-width: 480px) {
            .tables-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/logo.png" alt="Wolf & Turtle" class="logo" 
             onerror="this.onerror=null; this.alt='WOLF & TURTLE'; this.style.fontSize='24px'; this.style.fontWeight='bold'; this.style.color='#1a1a1a'">
    </div>
    
    <div class="nav">
        <a href="index.php">HOME</a>
        <a href="tables.php">TABLE STATUS</a>
        <a href="order.php">ORDER NOW</a>
        <a href="login.php">STAFF LOGIN</a>
    </div>
    
    <div class="container">
        <h1>📋 Table Availability</h1>
        
        <!-- Legend -->
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color available"></div>
                <span>Available</span>
            </div>
            <div class="legend-item">
                <div class="legend-color occupied"></div>
                <span>Occupied</span>
            </div>
        </div>
        
        <!-- Tables Grid - TANPA ORDER BUTTON -->
        <div class="tables-grid">
            <?php while($table = $tables->fetch_assoc()): ?>
            <div class="table-card <?php echo $table['status']; ?>">
                <div class="table-number">Table <?php echo $table['table_number']; ?></div>
                <div class="table-detail">👥 Capacity: <?php echo $table['capacity']; ?> pax</div>
                <div class="table-detail">📍 Location: <?php echo $table['location']; ?></div>
                <div class="table-status status-<?php echo $table['status']; ?>">
                    <?php echo strtoupper($table['status']); ?>
                </div>
                <!-- ORDER BUTTON REMOVED - Customer only view table availability -->
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- QR Hint - Customer must scan QR code at table -->
        <div class="qr-hint">
            <p>📱 <strong>How to Order?</strong></p>
            <p>Please sit at any available table and scan the QR code to order.</p>
            <p>No QR code? Ask our staff for assistance.</p>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2026 Wolf & Turtle Coffee. All rights reserved.</p>
        <p style="margin-top: 10px;">Bukit Tunku • The Exchange TRX • Wangsa Maju</p>
    </div>
</body>
</html>