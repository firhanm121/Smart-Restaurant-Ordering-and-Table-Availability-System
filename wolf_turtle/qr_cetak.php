<?php
// ============================================
// qr_cetak.php - Print QR Codes for Tables
// ============================================
include 'config.php';

// Get all tables
$tables = $conn->query("SELECT * FROM tables ORDER BY table_number");

// Get computer IP for URL
$computer_ip = "192.168.0.12";
?>

<!DOCTYPE html>
<html>
<head>
    <title>QR Codes - Wolf & Turtle</title>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .logo {
            max-height: 60px;
            width: auto;
        }
        
        .header p {
            margin-top: 10px;
            color: #666;
        }
        
        .ip-info {
            background: #e8f5e9;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #2e7d32;
        }
        
        .ip-info strong {
            color: #1a1a1a;
            font-size: 18px;
        }
        
        .qr-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .qr-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            page-break-inside: avoid;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .qr-card h2 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 15px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 8px;
        }
        
        .qr-code {
            margin: 20px auto;
            padding: 15px;
            background: white;
            display: inline-block;
            border: 2px dashed #ccc;
            border-radius: 12px;
        }
        
        .qr-code img {
            width: 200px;
            height: 200px;
        }
        
        .table-info {
            margin: 15px 0;
            font-size: 16px;
            color: #555;
        }
        
        .table-info p {
            margin: 5px 0;
        }
        
        .token-text {
            font-size: 11px;
            color: #999;
            word-break: break-all;
            background: #f9f9f9;
            padding: 8px;
            border-radius: 6px;
            margin-top: 12px;
            font-family: monospace;
        }
        
        .instructions {
            background: #fff3e0;
            padding: 20px;
            border-radius: 12px;
            margin: 30px 0;
            border-left: 4px solid #ff9800;
        }
        
        .instructions h3 {
            margin-bottom: 10px;
            color: #e65100;
        }
        
        .print-btn {
            display: block;
            width: 250px;
            margin: 30px auto;
            padding: 15px 30px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .print-btn:hover {
            background: #333;
            transform: scale(1.02);
        }
        
        .test-section {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: 4px solid #1976d2;
        }
        
        .test-section h3 {
            color: #0d47a1;
            margin-bottom: 10px;
        }
        
        .test-section ol {
            margin-left: 20px;
            margin-top: 10px;
        }
        
        .test-section li {
            margin: 5px 0;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: white;
                padding: 10px;
            }
            .qr-card {
                border: 1px solid #000;
                box-shadow: none;
                page-break-inside: avoid;
            }
            .qr-card h2 {
                background: none;
            }
            .qr-code img {
                width: 180px;
                height: 180px;
            }
            .logo {
                max-height: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Logo - Tidak Akan Cetak -->
        <div class="header no-print">
            <img src="images/logo.png" alt="Wolf & Turtle" class="logo" 
                 onerror="this.onerror=null; this.alt='WOLF & TURTLE'; this.style.fontSize='24px'; this.style.fontWeight='bold'; this.style.color='#1a1a1a'">
            <p>QR Code untuk Setiap Meja</p>
        </div>
        
        <!-- IP Info - Tidak Akan Cetak -->
        <div class="ip-info no-print">
            <strong>🌐 IP Komputer:</strong> <?php echo $computer_ip; ?>
            <br>
            <small>Pastikan handphone connect WiFi yang SAMA dengan komputer ini</small>
        </div>
        
        <!-- Test Instructions - Tidak Akan Cetak -->
        <div class="test-section no-print">
            <h3>📱 TEST SEBELUM CETAK:</h3>
            <ol>
                <li>Buka handphone, connect ke WiFi yang SAMA dengan komputer</li>
                <li>Buka browser handphone, taip: <strong>http://<?php echo $computer_ip; ?>/wolf_turtle/</strong></li>
                <li>Jika nampak website Wolf & Turtle, <span style="color: green; font-weight: bold;">✓ BERJAYA</span></li>
                <li>Jika tidak, semak firewall atau pastikan WiFi sama</li>
            </ol>
        </div>
        
        <!-- QR Code Grid -->
        <div class="qr-grid">
            <?php 
            // Reset pointer and loop through all tables
            $tables = $conn->query("SELECT * FROM tables ORDER BY table_number");
            while($table = $tables->fetch_assoc()): 
                
                // Create scan URL
                $scan_url = "http://" . $computer_ip . "/wolf_turtle/scan.php?token=" . $table['qr_code_token'];
                
                // QR Server API (most stable)
                $qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($scan_url);
            ?>
            <div class="qr-card">
                <h2>MEJA <?php echo $table['table_number']; ?></h2>
                
                <div class="qr-code">
                    <img src="<?php echo $qr_api_url; ?>" 
                         alt="QR Code Meja <?php echo $table['table_number']; ?>"
                         onerror="this.onerror=null; this.src='https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo urlencode($scan_url); ?>'">
                </div>
                
                <div class="table-info">
                    <p><strong>👥 Kapasiti:</strong> <?php echo $table['capacity']; ?> orang</p>
                    <p><strong>📍 Lokasi:</strong> <?php echo $table['location']; ?></p>
                </div>
                
                <div class="token-text">
                    Token: <?php echo $table['qr_code_token']; ?>
                </div>
                
                <div style="margin-top: 12px; font-size: 12px; color: #2a9d8f;">
                    Scan untuk order makanan
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Print Button - Tidak Akan Cetak -->
        <button class="print-btn no-print" onclick="window.print()">
            🖨️ CETAK QR CODE
        </button>
        
        <!-- Instructions - Tidak Akan Cetak -->
        <div class="instructions no-print">
            <h3>📌 CARA GUNA QR CODE:</h3>
            <ol style="margin-left: 20px;">
                <li>Test dulu dengan handphone (pastikan boleh akses)</li>
                <li>Klik butang <strong>"CETAK QR CODE"</strong> di atas</li>
                <li>Dalam print preview, pastikan 2 QR code dalam satu muka surat</li>
                <li>Cetak guna kertas biasa atau sticker paper</li>
                <li>Gunting setiap QR code</li>
                <li>Laminate untuk tahan lama</li>
                <li>Letak di setiap meja (contoh: QR Meja T01 di meja T01)</li>
            </ol>
        </div>
        
        <!-- Footer Note - Tidak Akan Cetak -->
        <div class="test-section no-print" style="margin-top: 20px; background: #f5f5f5;">
            <h3>⚠️ PENTING:</h3>
            <p>Setiap kali XAMPP di-restart, IP komputer mungkin berubah. Jika IP berubah, anda perlu generate QR code semula dengan IP baru.</p>
            <p>Untuk demo FYP, ini adalah normal dan boleh diterima.</p>
        </div>
    </div>
</body>
</html>