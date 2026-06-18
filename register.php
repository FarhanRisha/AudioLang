<?php
session_start();
require_once 'db.php';

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matric_no = trim($_POST['matric_no']);
    $full_name = trim($_POST['full_name']);
    $phone_no  = trim($_POST['phone_no']);

    if (!empty($matric_no) && !empty($full_name) && !empty($phone_no)) {
        try {
            // FIXED: Changed 'email' to your updated database column name 'matric_no'
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE matric_no = :matric");
            $checkStmt->execute([':matric' => $matric_no]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $message = "This Matric Number is already registered!";
                $status = "error";
            } else {
                // Insert tracking matching your new database schema columns exactly
                $sql = "INSERT INTO user (full_name, matric_no, phone_no) VALUES (:name, :matric, :phone)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':name'   => $full_name,
                    ':matric' => $matric_no,
                    ':phone'  => $phone_no
                ]);

                $message = "Registration successful! You can now log in.";
                $status = "success";
            }
        } catch (Exception $e) {
            $message = "Registration Error: " . $e->getMessage();
            $status = "error";
        }
    } else {
        $message = "Please fill in all input fields.";
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration — AudioLang</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #1a1a1a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: 'Open Sans', sans-serif; }
        .reg-card { background: #1a1a1a; border: 1px solid #333; padding: 40px; border-radius: 16px; width: 100%; max-width: 750px; box-sizing: border-box; }
        .reg-card h2 { color: #00d2ff; text-align: center; margin-bottom: 35px; text-transform: uppercase; font-weight: 700; letter-spacing: 1.5px; font-size: 32px; }
        .reg-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { color: #00d2ff; font-weight: 700; margin-bottom: 8px; font-size: 16px; }
        .form-group input { background: #111111; border: 1px solid #333; padding: 14px; border-radius: 8px; font-size: 16px; color: #ffffff; box-sizing: border-box; }
        .form-group input::placeholder { color: #444444; }
        .btn-container { grid-column: span 2; margin-top: 15px; }
        .btn-reg { width: 100%; padding: 14px; background: #00d2ff; border: none; border-radius: 8px; color: #ffffff; font-size: 16px; font-weight: 700; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px; }
        .btn-reg:hover { background: #00b8e6; }
        .msg { grid-column: span 2; padding: 10px; border-radius: 6px; font-size: 14px; margin-bottom: 10px; text-align: center; }
        .msg.error { background: #5c1d1d; color: #ff8a8a; border: 1px solid #8a2b2b; }
        .msg.success { background: #1d5c2d; color: #8aff9d; border: 1px solid #2b8a43; }
        .nav-link { grid-column: span 2; text-align: center; margin-top: 15px; }
        .nav-link a { color: #888; text-decoration: none; font-size: 14px; }
        .nav-link a:hover { color: #00d2ff; }
    </style>
</head>
<body>

<div class="reg-card">
    <h2>Student Registration</h2>
    
    <form method="POST" action="register.php" class="reg-grid">
        <?php if(!empty($message)): ?>
            <div class="msg <?= $status ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label>Matric Number:</label>
            <input type="text" name="matric_no" placeholder="B032110001" required>
        </div>

        <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone_no" placeholder="e.g. 0123456789" required>
        </div>

        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="full_name" placeholder="e.g. Ahmad Bin Ali" required>
        </div>

        <div class="btn-container">
            <button type="submit" class="btn-reg">Register</button>
        </div>

        <div class="nav-link">
            <a href="login.php">Already registered? Go to Login</a>
        </div>
    </form>
</div>

</body>
</html>