<?php
// ============================================
// generate_qr_codes.php - Generate QR untuk semua meja
// ============================================
include 'config.php';

// Dapatkan semua meja
$tables = $conn->query("SELECT * FROM tables");

// Generate token untuk meja yang belum ada
$conn->query("UPDATE tables SET qr_code_token = CONCAT('table_', table_number, '_', FLOOR(RAND() * 1000000)) WHERE qr_code_token IS NULL");
?>

<!DOCTYPE html>
<html>
<head>
    <title>QR Codes - Wolf & Turtle</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial;
        }
        
        body {
            padding: 20px;
            background: #f5f5f5;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #1a2e3f;
        }
        
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .qr-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border: 2px solid #ddd;
        }
        
        .qr-card h3 {
            margin-bottom: 15px;
            color: #2a9d8f;
        }
        
        .qr-code {
            margin: 20px auto;
        }
        
        .qr-code img {
            width: 150px;
            height: 150px;
        }
        
        .table-detail {
            margin-top: 10px;
            color: #666;
        }
        
        .print-btn {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 15px;
            background: #1a2e3f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        
        .print-btn:hover {
            background: #2a9d8f;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: white;
                padding: 0;
            }
            .qr-card {
                border: 1px solid #000;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <h1 class="no-print">📱 QR Code untuk Setiap Meja</h1>
    <p class="no-print" style="text-align: center; margin-bottom: 30px;">
        Letak QR code ni di setiap meja. Pelanggan WAJIB scan sebelum boleh order.
    </p>
    
    <div class="qr-grid">
        <?php
        $tables = $conn->query("SELECT * FROM tables ORDER BY table_number");
        while($table = $tables->fetch_assoc()):
            
            // URL untuk scan
            $scan_url = "http://" . $_SERVER['HTTP_HOST'] . "/wolf_turtle/scan.php?token=" . $table['qr_code_token'];
        ?>
        <div class="qr-card">
            <h3>MEJA <?php echo $table['table_number']; ?></h3>
            <div class="qr-code">
                <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo urlencode($scan_url); ?>" alt="QR Code">
            </div>
            <div class="table-detail">
                Kapasiti: <?php echo $table['capacity']; ?> orang<br>
                Lokasi: <?php echo $table['location']; ?>
            </div>
            <div style="font-size: 10px; margin-top: 10px; color: #999;">
                Token: <?php echo $table['qr_code_token']; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    
    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak Semua QR Code</button>
    
    <div class="no-print" style="text-align: center; margin-top: 30px; padding: 20px; background: #e8f5f3; border-radius: 10px;">
        <h3>📌 Cara Guna:</h3>
        <p>1. Cetak QR code ni</p>
        <p>2. Laminate atau letak dalam acrylic stand di setiap meja</p>
        <p>3. Pelanggan scan → akan terus ke halaman order meja masing-masing</p>
        <p>4. <strong>Tak scan QR → tak boleh order!</strong></p>
    </div>
</body>
</html>