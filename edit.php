<?php
require_once 'config/database.php';

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Fetch current data
try {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch();

    if (!$member) {
        die("ไม่พบข้อมูลสมาชิก");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

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
            // Check for duplicate email (excluding current user)
            $stmt = $pdo->prepare("SELECT id FROM members WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->rowCount() > 0) {
                $error = "อีเมลนี้มีอยู่ในระบบแล้ว";
            } else {
                $sql = "UPDATE members SET fullname = ?, email = ?, major = ?, academic_year = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fullname, $email, $major, $academic_year, $id]);
                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
} else {
    // Populate form with existing data
    $fullname = $member['fullname'];
    $email = $member['email'];
    $major = $member['major'];
    $academic_year = $member['academic_year'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลสมาชิก - DevClub</title>
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
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">✏️ แก้ไขข้อมูลสมาชิก</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($fullname) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">สาขาวิชา</label>
                    <input type="text" name="major" class="form-control" value="<?= htmlspecialchars($major) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ปีการศึกษา (พ.ศ.)</label>
                    <input type="number" name="academic_year" class="form-control" value="<?= htmlspecialchars($academic_year) ?>" required>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">ย้อนกลับ</a>
                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
