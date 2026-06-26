<?php
include 'config.php';
if (!isStaff()) redirect('login.php');

if (isset($_GET['toggle_menu'])) {
    $menu_id = $_GET['menu_id'];
    $conn->query("UPDATE menu_items SET is_available = NOT is_available WHERE id = $menu_id");
    redirect('staff_dashboard.php?msg=Menu updated');
}
if (isset($_GET['update_order'])) {
    $order_id = $_GET['order_id'];
    $status = $_GET['status'];
    $conn->query("UPDATE orders SET order_status = '$status' WHERE id = $order_id");
    redirect('staff_dashboard.php?msg=Order updated');
}
if (isset($_GET['update_table'])) {
    $table_id = $_GET['table_id'];
    $status = $_GET['status'];
    $conn->query("UPDATE tables SET status = '$status' WHERE id = $table_id");
    redirect('staff_dashboard.php?msg=Table updated');
}

$staff = $conn->query("SELECT * FROM users WHERE id = " . $_SESSION['user_id'])->fetch_assoc();
$active_orders = $conn->query("SELECT o.*, t.table_number FROM orders o LEFT JOIN tables t ON o.table_id = t.id WHERE o.order_status IN ('received','preparing','ready') ORDER BY FIELD(o.order_status,'received','preparing','ready'), o.created_at ASC");
$tables = $conn->query("SELECT * FROM tables ORDER BY table_number");
$all_menu = $conn->query("SELECT * FROM menu_items ORDER BY category, name");

$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$total_received = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'received'")->fetch_assoc()['count'];
$total_preparing = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'preparing'")->fetch_assoc()['count'];
$total_ready = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'ready'")->fetch_assoc()['count'];
$total_menu_available = $conn->query("SELECT COUNT(*) as count FROM menu_items WHERE is_available = 1")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f5f5f5; }
        .navbar { background: #1a1a1a; color: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; }
        .container { max-width: 1400px; margin: 20px auto; padding: 0 20px; }
        .message { background: #e8f5e9; padding: 10px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2e7d32; }
        .stats { display: grid; grid-template-columns: repeat(6,1fr); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #ddd; }
        .stat-number { font-size: 24px; font-weight: bold; color: #1a1a1a; }
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px; }
        .tab-btn { padding: 10px 20px; background: #f0f0f0; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .tab-btn.active { background: #1a1a1a; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .section { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #ddd; }
        .section-title { font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #1a1a1a; padding-bottom: 8px; }
        .order-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .badge-available { background: #e8f5e9; color: #2e7d32; }
        .badge-soldout { background: #ffebee; color: #c62828; }
        .btn { padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; display: inline-block; border: none; cursor: pointer; }
        .btn-toggle { background: #ffc107; color: #000; }
        .btn-toggle.available { background: #dc3545; color: white; }
        .btn-toggle.soldout { background: #28a745; color: white; }
        .table-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(150px,1fr)); gap: 15px; }
        .table-card { padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #ddd; }
        .table-card.available { background: #e8f5e9; }
        .table-card.occupied { background: #ffebee; }
        .logout-btn { background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(300px,1fr)); gap: 15px; }
        .menu-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; display: flex; justify-content: space-between; align-items: center; }
        .staff-id { background: #666; padding: 5px 10px; border-radius: 5px; margin-left: 10px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Staff Dashboard</h2>
        <div><span>Welcome, <?php echo $staff['full_name']; ?></span><span class="staff-id">ID: <?php echo $staff['staff_id']; ?></span><a href="logout.php" class="logout-btn" style="margin-left: 15px;">Logout</a></div>
    </div>
    <div class="container">
        <?php if(isset($_GET['msg'])): ?><div class="message">✅ <?php echo $_GET['msg']; ?></div><?php endif; ?>
        <div class="stats">
            <div class="stat-card"><div>Today's Orders</div><div class="stat-number"><?php echo $total_orders; ?></div></div>
            <div class="stat-card"><div>Received</div><div class="stat-number"><?php echo $total_received; ?></div></div>
            <div class="stat-card"><div>Preparing</div><div class="stat-number"><?php echo $total_preparing; ?></div></div>
            <div class="stat-card"><div>Ready</div><div class="stat-number"><?php echo $total_ready; ?></div></div>
            <div class="stat-card"><div>Menu Available</div><div class="stat-number"><?php echo $total_menu_available; ?></div></div>
        </div>
        <div class="tabs">
            <button class="tab-btn active" onclick="openTab('orders')">📋 Orders</button>
            <button class="tab-btn" onclick="openTab('menu')">🍽️ Menu</button>
            <button class="tab-btn" onclick="openTab('tables')">🪑 Tables</button>
        </div>
        
        <div id="orders" class="tab-content active">
            <div class="section"><h3 class="section-title">Active Orders</h3>
                <?php if($active_orders->num_rows == 0): ?><p>No active orders</p><?php endif; ?>
                <?php while($order = $active_orders->fetch_assoc()): $items = $conn->query("SELECT oi.*, mi.name FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ".$order['id']); ?>
                <div class="order-card">
                    <div style="display:flex;justify-content:space-between"><strong>#<?php echo $order['order_number']; ?></strong><span><?php echo date('H:i',strtotime($order['created_at'])); ?></span></div>
                    <div>Table: <?php echo $order['table_number']?:'Takeaway'; ?> | Customer: <?php echo $order['customer_name']; ?></div>
                    <div><?php while($item = $items->fetch_assoc()): ?>• <?php echo $item['quantity']; ?>x <?php echo $item['name']; ?><br><?php endwhile; ?><strong>Total: RM <?php echo number_format($order['total_amount'],2); ?></strong></div>
                    <div style="margin:10px 0"><span class="badge">Status: <?php echo ucfirst($order['order_status']); ?></span></div>
                    <div style="display:flex;gap:10px">
                        <?php if($order['order_status']=='received'): ?><a href="?update_order=1&order_id=<?php echo $order['id']; ?>&status=preparing" class="btn" style="background:#17a2b8;color:white">Start Preparing</a><?php endif; ?>
                        <?php if($order['order_status']=='preparing'): ?><a href="?update_order=1&order_id=<?php echo $order['id']; ?>&status=ready" class="btn" style="background:#28a745;color:white">Mark Ready</a><?php endif; ?>
                        <?php if($order['order_status']=='ready'): ?><a href="?update_order=1&order_id=<?php echo $order['id']; ?>&status=completed" class="btn" style="background:#6c757d;color:white">Complete</a><?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        
        <div id="menu" class="tab-content">
            <div class="section"><h3 class="section-title">Menu Availability</h3><p style="margin-bottom:15px;">Click toggle to mark menu as Available or Sold Out.</p>
                <div class="menu-grid">
                    <?php while($menu = $all_menu->fetch_assoc()): 
                        $status_text = $menu['is_available'] ? '✅ Available' : '❌ Sold Out';
                        $toggle_text = $menu['is_available'] ? 'Mark Sold Out' : 'Mark Available';
                        $btn_class = $menu['is_available'] ? 'available' : 'soldout';
                    ?>
                    <div class="menu-card">
                        <div><strong><?php echo $menu['name']; ?></strong><br><span class="badge <?php echo $menu['is_available']?'badge-available':'badge-soldout'; ?>"><?php echo $status_text; ?></span><br><small>RM <?php echo number_format($menu['price'],2); ?></small></div>
                        <a href="?toggle_menu=1&menu_id=<?php echo $menu['id']; ?>" class="btn btn-toggle <?php echo $btn_class; ?>">🔄 <?php echo $toggle_text; ?></a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <div id="tables" class="tab-content">
            <div class="section"><h3 class="section-title">Table Status</h3>
                <div class="table-grid">
                    <?php while($table = $tables->fetch_assoc()): ?>
                    <div class="table-card <?php echo $table['status']; ?>">
                        <div style="font-size:18px; font-weight:bold;">Table <?php echo $table['table_number']; ?></div>
                        <div><?php echo ucfirst($table['status']); ?></div>
                        <div style="margin-top:10px;">
                            <?php if($table['status']=='available'): ?><a href="?update_table=1&table_id=<?php echo $table['id']; ?>&status=occupied" class="btn" style="background:#dc3545;color:white">Set Occupied</a>
                            <?php else: ?><a href="?update_table=1&table_id=<?php echo $table['id']; ?>&status=available" class="btn" style="background:#28a745;color:white">Set Available</a><?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <script>function openTab(tabName){document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active'));document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));document.getElementById(tabName).classList.add('active');event.target.classList.add('active');}</script>
</body>
</html>