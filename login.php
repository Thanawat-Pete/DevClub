<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "กรุณากรอกอีเมลและรหัสผ่าน";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                header("Location: index.php");
                exit();
            } else {
                $error = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
            }
        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบผู้ดูแล - DevClub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 400px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .auth-header { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); padding: 30px; text-align: center; color: white; }
        .auth-body { padding: 40px; }
        .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25); }
        .btn-primary { background: #4f46e5; border: none; padding: 12px; font-weight: 600; width: 100%; border-radius: 10px; }
        .btn-primary:hover { background: #4338ca; }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <img src="logo.png" width="60" class="mb-3 bg-white rounded-circle p-1">
        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 700;"> Login</h4>
        <p class="mb-0 opacity-75">เข้าสู่ระบบจัดการข้อมูลชมรม</p>
    </div>
    <div class="auth-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mb-3">เข้าสู่ระบบ</button>
            <div class="text-center">
                <a href="register.php" class="text-decoration-none text-muted">ยังไม่มีบัญชี? สมัครสมาชิก</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
