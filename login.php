<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matric_no = trim($_POST['matric_no']);
    $phone_no = trim($_POST['phone_no']);

    if (!empty($matric_no) && !empty($phone_no)) {
        try {
            // Check your updated database schema names directly
            $stmt = $pdo->prepare("SELECT * FROM user WHERE matric_no = :matric AND phone_no = :phone LIMIT 1");
            $stmt->execute([':matric' => $matric_no, ':phone' => $phone_no]);
            $user = $stmt->fetch();

            if ($user) {
                // Set active presentation session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['full_name']; // Tracks full_name column natively
                
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid Matric Number or Phone Number configuration mismatch.";
            }
        } catch (Exception $e) {
            $error = "System Authentication Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all security fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login — AudioLang</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #1a1a1a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: 'Open Sans', sans-serif; }
        .login-card { background: #333333; padding: 40px; border-radius: 24px; width: 100%; max-width: 420px; box-sizing: border-box; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .login-card h2 { color: #ffffff; margin-bottom: 30px; font-weight: 600; font-size: 28px; }
        .input-group { margin-bottom: 20px; }
        .input-group input { width: 100%; padding: 14px 20px; border-radius: 12px; border: none; font-size: 16px; color: #333; box-sizing: border-box; }
        .input-group input::placeholder { color: #8c8c8c; }
        .btn-login { width: 100%; padding: 14px; background: #00d2ff; border: none; border-radius: 12px; color: #ffffff; font-size: 16px; font-weight: 700; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px; transition: background 0.2s; }
        .btn-login:hover { background: #00b8e6; }
        .error-msg { color: #ff6b6b; font-size: 14px; margin-bottom: 15px; text-align: left; }
        .nav-link { margin-top: 20px; }
        .nav-link a { color: #888; text-decoration: none; font-size: 14px; }
        .nav-link a:hover { color: #00d2ff; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Student Login</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="input-group">
            <input type="text" name="matric_no" placeholder="Matric Number" required>
        </div>
        <div class="input-group">
            <input type="password" name="phone_no" placeholder="Phone Number (Password)" required>
        </div>
        <button type="submit" class="btn-login">Login</button>
    </form>

    <div class="nav-link">
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</div>

</body>
</html>