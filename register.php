<?php
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    } elseif (!str_ends_with($email, '@webmail.npru.ac.th')) {
        $error = "ต้องใช้อีเมลมหาวิทยาลัย (@webmail.npru.ac.th) เท่านั้น";
    } elseif ($password !== $confirm_password) {
        $error = "รหัสผ่านไม่ตรงกัน";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $error = "อีเมลนี้ถูกใช้งานแล้ว";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (fullname, email, password) VALUES (:fullname, :email, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':email' => $email,
                    ':password' => $hashed_password
                ]);
                $success = "สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ";
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
    <title>ลงทะเบียนผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 450px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .auth-header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; text-align: center; color: white; }
        .auth-body { padding: 40px; }
        .form-control:focus { border-color: #10b981; box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25); }
        .btn-success { background: #10b981; border: none; padding: 12px; font-weight: 600; width: 100%; border-radius: 10px; }
        .btn-success:hover { background: #059669; }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <h4 style="font-family: 'Outfit', sans-serif; font-weight: 700;">Register Admin</h4>
        <p class="mb-0 opacity-75">สมัครสมาชิกสำหรับผู้ดูแลระบบ</p>
    </div>
    <div class="auth-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= $success ?> <br>
                <a href="login.php" class="alert-link">เข้าสู่ระบบที่นี่</a>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">ชื่อ-นามสกุล</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">อีเมล (@webmail.npru.ac.th)</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ยืนยันรหัสผ่าน</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success mb-3">ลงทะเบียน</button>
            <div class="text-center">
                <a href="login.php" class="text-decoration-none text-muted">มีบัญชีอยู่แล้ว? เข้าสู่ระบบ</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
