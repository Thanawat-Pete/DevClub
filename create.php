<?php
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $major = trim($_POST['major']);
    $academic_year = trim($_POST['academic_year']);

    if (empty($fullname) || empty($email) || empty($major) || empty($academic_year)) {
        $error = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    } elseif (!is_numeric($academic_year) || strlen($academic_year) != 4) {
        $error = "ปีการศึกษาต้องเป็นตัวเลข 4 หลัก";
    } else {
        try {
            // Check for duplicate email
            $stmt = $pdo->prepare("SELECT id FROM members WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $error = "อีเมลนี้มีอยู่ในระบบแล้ว";
            } else {
                $sql = "INSERT INTO members (fullname, email, major, academic_year) VALUES (:fullname, :email, :major, :academic_year)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':email' => $email,
                    ':major' => $major,
                    ':academic_year' => $academic_year
                ]);
                header("Location: index.php");
                exit(); 
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
    <title>เพิ่มสมาชิกใหม่ - DevClub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .card { max-width: 600px; margin: 50px auto; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">➕ เพิ่มสมาชิกใหม่</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" name="fullname" class="form-control" value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="email" class="form-control" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">สาขาวิชา</label>
                    <input type="text" name="major" class="form-control" value="<?= isset($major) ? htmlspecialchars($major) : '' ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ปีการศึกษา (พ.ศ.)</label>
                    <input type="number" name="academic_year" class="form-control" placeholder="เช่น 2567" value="<?= isset($academic_year) ? htmlspecialchars($academic_year) : '' ?>" required>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">ย้อนกลับ</a>
                    <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
