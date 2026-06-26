<?php
include 'config.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') redirect('admin_dashboard.php');
    if ($_SESSION['role'] == 'staff') redirect('staff_dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $password = MD5($_POST['password']);
    
    if ($login == 'admin') {
        $result = $conn->query("SELECT * FROM users WHERE username = '$login' AND password = '$password' AND role = 'admin'");
    } else {
        $result = $conn->query("SELECT * FROM users WHERE staff_id = '$login' AND password = '$password' AND role = 'staff'");
    }
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['staff_id'] = $user['staff_id'];
        redirect($user['role'] == 'admin' ? 'admin_dashboard.php' : 'staff_dashboard.php');
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Wolf & Turtle</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            width: 420px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
        }
        
        /* Header dengan Logo */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            max-height: 70px;
            width: auto;
            margin-bottom: 15px;
        }
        
        .header h2 {
            color: #1a1a1a;
            font-size: 24px;
            font-weight: 500;
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #c62828;
            font-size: 14px;
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
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .login-btn:hover {
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
            font-size: 13px;
            transition: 0.3s;
        }
        
        .back-link a:hover {
            color: #1a1a1a;
            text-decoration: underline;
        }
        
        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="header">
            <img src="images/logo.png" alt="Wolf & Turtle" class="logo" 
                 onerror="this.onerror=null; this.alt='WOLF & TURTLE'; this.style.fontSize='28px'; this.style.fontWeight='bold'; this.style.color='#1a1a1a'">
            <h2>Staff Login</h2>
        </div>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Staff ID / Admin Username</label>
                <input type="text" name="login" placeholder="Enter 6-digit Staff ID or 'admin'" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
        
        <hr>
        
        <div class="back-link">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>