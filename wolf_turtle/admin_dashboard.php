<?php
include 'config.php';
if (!isAdmin()) redirect('login.php');

if (isset($_POST['add_staff'])) {
    $staff_id = generateStaffID($conn);
    $password = MD5($_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $conn->query("INSERT INTO users (username, staff_id, password, role, full_name, email) VALUES ('$staff_id', '$staff_id', '$password', 'staff', '$full_name', '$email')");
    redirect('admin_dashboard.php?msg=Staff added');
}
if (isset($_GET['delete_staff'])) {
    $conn->query("DELETE FROM users WHERE id = ".$_GET['delete_staff']." AND role='staff'");
    redirect('admin_dashboard.php?msg=Staff deleted');
}
if (isset($_GET['delete_menu'])) {
    $conn->query("DELETE FROM menu_items WHERE id = ".$_GET['delete_menu']);
    redirect('admin_dashboard.php?msg=Menu deleted');
}
if (isset($_POST['add_menu'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $cat = $_POST['category'];
    $pop = isset($_POST['is_popular']) ? 1 : 0;
    $image = "images/default.jpg";
    if(isset($_FILES['image']) && $_FILES['image']['error']==0){
        $target = "images/".time()."_".basename($_FILES['image']['name']);
        if(move_uploaded_file($_FILES['image']['tmp_name'], $target)) $image = $target;
    }
    $conn->query("INSERT INTO menu_items (name, description, price, category, image_path, is_popular) VALUES ('$name','$desc',$price,'$cat','$image',$pop)");
    redirect('admin_dashboard.php?msg=Menu added');
}

$staff_list = $conn->query("SELECT * FROM users WHERE role='staff'");
$menu_items = $conn->query("SELECT * FROM menu_items ORDER BY category,name");
$total_orders = $conn->query("SELECT COUNT(*) as c FROM orders WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['c'];
$total_sales = $conn->query("SELECT SUM(total_amount) as t FROM orders WHERE DATE(created_at)=CURDATE() AND payment_status='paid'")->fetch_assoc()['t'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }
        body { background:#f5f5f5; }
        .navbar { background:#1a1a1a; color:white; padding:15px 5%; display:flex; justify-content:space-between; align-items:center; }
        .container { max-width:1200px; margin:20px auto; padding:0 20px; }
        .message { background:#e8f5e9; padding:10px; border-radius:8px; margin-bottom:20px; border-left:4px solid #2e7d32; }
        .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:30px; }
        .stat-card { background:white; padding:20px; border-radius:8px; text-align:center; border:1px solid #ddd; }
        .stat-number { font-size:32px; font-weight:bold; color:#1a1a1a; }
        .section { background:white; border-radius:8px; padding:20px; margin-bottom:20px; border:1px solid #ddd; }
        .section-title { font-size:18px; margin-bottom:20px; border-bottom:2px solid #1a1a1a; padding-bottom:8px; }
        table { width:100%; border-collapse:collapse; }
        th { background:#1a1a1a; color:white; padding:10px; text-align:left; }
        td { padding:10px; border-bottom:1px solid #eee; }
        .btn { padding:5px 10px; border-radius:4px; text-decoration:none; font-size:12px; display:inline-block; border:none; cursor:pointer; }
        .btn-delete { background:#dc3545; color:white; }
        .btn-add { background:#28a745; color:white; padding:8px 15px; margin-bottom:15px; display:inline-block; }
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); }
        .modal-content { background:white; width:450px; margin:100px auto; padding:30px; border-radius:12px; }
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; margin-bottom:5px; font-weight:bold; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; }
        .modal-buttons { display:flex; gap:10px; margin-top:20px; }
        .btn-save { background:#28a745; color:white; padding:10px; border:none; border-radius:4px; flex:1; cursor:pointer; }
        .btn-cancel { background:#dc3545; color:white; padding:10px; border:none; border-radius:4px; flex:1; cursor:pointer; }
        .preview-id { background:#f0f0f0; padding:15px; text-align:center; border-radius:8px; margin-bottom:20px; font-size:24px; font-weight:bold; }
        .logout-btn { background:#dc3545; color:white; padding:8px 15px; text-decoration:none; border-radius:5px; }
    </style>
</head>
<body>
    <div class="navbar"><h2>Admin Dashboard</h2><div><span>Welcome, <?php echo $_SESSION['full_name']; ?></span><a href="logout.php" class="logout-btn" style="margin-left:15px;">Logout</a></div></div>
    <div class="container">
        <?php if(isset($_GET['msg'])): ?><div class="message">✅ <?php echo $_GET['msg']; ?></div><?php endif; ?>
        <div class="stats">
            <div class="stat-card"><div>Today's Orders</div><div class="stat-number"><?php echo $total_orders; ?></div></div>
            <div class="stat-card"><div>Today's Sales</div><div class="stat-number">RM <?php echo number_format($total_sales?:0,2); ?></div></div>
            <div class="stat-card"><div>Total Menu</div><div class="stat-number"><?php echo $menu_items->num_rows; ?></div></div>
        </div>
        
        <div class="section"><h2 class="section-title">👥 Manage Staff</h2><button class="btn-add" onclick="showStaffModal()">➕ Add Staff</button>
            <table><thead><tr><th>Staff ID</th><th>Name</th><th>Email</th><th>Action</th></tr></thead>
            <tbody><?php while($s=$staff_list->fetch_assoc()): ?><tr><td><?php echo $s['staff_id']; ?></td><td><?php echo $s['full_name']; ?></td><td><?php echo $s['email']; ?></td><td><a href="?delete_staff=<?php echo $s['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete?')">Delete</a></td></tr><?php endwhile; ?></tbody></table>
        </div>
        
        <div class="section"><h2 class="section-title">🍽️ Manage Menu</h2><button class="btn-add" onclick="showMenuModal()">➕ Add Menu</button>
            <table><thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Popular</th><th>Status</th><th>Action</th></tr></thead>
            <tbody><?php while($m=$menu_items->fetch_assoc()): ?><tr><td><?php echo $m['name']; ?></td><td><?php echo $m['category']; ?></td><td>RM <?php echo number_format($m['price'],2); ?></td><td><?php echo $m['is_popular']?'⭐ Yes':'No'; ?></td><td><?php echo $m['is_available']?'✅ Available':'❌ Sold Out'; ?></td><td><a href="?delete_menu=<?php echo $m['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete?')">Delete</a></td></tr><?php endwhile; ?></tbody></table>
        </div>
    </div>
    
    <div id="staffModal" class="modal"><div class="modal-content"><span onclick="hideStaffModal()" style="float:right;cursor:pointer">&times;</span><h2>Add Staff</h2><div class="preview-id" id="previewStaffId">??????</div>
        <form method="POST"><div class="form-group"><label>Full Name</label><input type="text" name="full_name" required></div><div class="form-group"><label>Email</label><input type="email" name="email" required></div><div class="form-group"><label>Password</label><input type="text" name="password" value="staff123" required></div><div class="modal-buttons"><button type="submit" name="add_staff" class="btn-save">Add Staff</button><button type="button" class="btn-cancel" onclick="hideStaffModal()">Cancel</button></div></form>
    </div></div>
    
    <div id="menuModal" class="modal"><div class="modal-content"><span onclick="hideMenuModal()" style="float:right;cursor:pointer">&times;</span><h2>Add Menu</h2>
        <form method="POST" enctype="multipart/form-data"><div class="form-group"><label>Name</label><input type="text" name="name" required></div><div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div><div class="form-group"><label>Price (RM)</label><input type="number" step="0.01" name="price" required></div><div class="form-group"><label>Category</label><select name="category"><option>Mains</option><option>Burgers</option><option>Breakfast</option><option>Desserts</option><option>Coffee</option><option>Cold Coffee</option><option>Non-Coffee</option></select></div><div class="form-group"><label><input type="checkbox" name="is_popular"> Popular Item</label></div><div class="form-group"><label>Image</label><input type="file" name="image" accept="image/*"></div><div class="modal-buttons"><button type="submit" name="add_menu" class="btn-save">Add Menu</button><button type="button" class="btn-cancel" onclick="hideMenuModal()">Cancel</button></div></form>
    </div></div>
    
    <script>
        function showStaffModal(){ let r=Math.floor(100000+Math.random()*900000); document.getElementById('previewStaffId').innerText=r; document.getElementById('staffModal').style.display='block'; }
        function hideStaffModal(){ document.getElementById('staffModal').style.display='none'; }
        function showMenuModal(){ document.getElementById('menuModal').style.display='block'; }
        function hideMenuModal(){ document.getElementById('menuModal').style.display='none'; }
        window.onclick=function(e){ if(e.target==document.getElementById('staffModal')) hideStaffModal(); if(e.target==document.getElementById('menuModal')) hideMenuModal(); }
    </script>
</body>
</html>