<?php
include 'config.php';
$token = isset($_GET['token']) ? $_GET['token'] : '';
if(empty($token)) die("Invalid QR Code");

$table = $conn->query("SELECT * FROM tables WHERE qr_code_token = '$token'");
if($table->num_rows == 0) die("Table not found or token invalid");

$meja = $table->fetch_assoc();
$_SESSION['scanned_table_id'] = $meja['id'];
$_SESSION['scanned_table_number'] = $meja['table_number'];
header("Location: order.php");
exit();
?>  