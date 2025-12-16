<?php
require_once 'config/database.php';

$error = '';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

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
    } elseif (!str_ends_with($email, '@webmail.npru.ac.th')) {
        $error = "ต้องใช้อีเมลมหาวิทยาลัย (@webmail.npru.ac.th) เท่านั้น";
    } elseif (!is_numeric($academic_year) || strlen($academic_year) != 4) {
        $error = "ปีการศึกษาต้องเป็นตัวเลข 4 หลัก";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT fullname, email FROM members WHERE (email = ? OR fullname = ?) AND id != ?");
            $stmt->execute([$email, $fullname, $id]);
            $existing = $stmt->fetch();
            if ($existing) {
                if ($existing['email'] == $email) {
                    $error = "อีเมลนี้มีอยู่ในระบบแล้ว";
                } else {
                    $error = "ชื่อ-นามสกุลนี้มีอยู่ในระบบแล้ว";
                }
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
    <title>แก้ไขข้อมูลสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f0f2f5; min-height: 100vh; }
        .form-card { margin-top: 50px; margin-bottom: 50px; }
        .form-card { 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
            max-width: 500px; 
            width: 100%;
            margin: auto;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border: none;
        }
        .card-header h4 { font-family: 'Outfit', sans-serif; margin: 0; font-weight: 700; }
        .card-body { padding: 40px; }
        .form-floating > label { color: #6b7280; }
        .form-control:focus, .form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25); }
        .btn-primary { 
            background: #f59e0b; border: none; padding: 12px; border-radius: 10px; font-weight: 600; width: 100%; margin-top: 10px;
            transition: all 0.3s;
        }
        .btn-primary:hover { background: #d97706; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
        .btn-link { text-decoration: none; color: #6b7280; display: block; text-align: center; margin-top: 20px; }
        .btn-link:hover { color: #374151; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="logo.png" alt="NPRU Logo" width="40" height="auto" class="d-inline-block align-text-top me-3 bg-white rounded-circle p-1">
      <span style="font-family: 'Sarabun'; font-weight: 700;">NPRU DevClub</span>
    </a>
  </div>
</nav>

<div class="container">
    <div class="form-card">
        <div class="card-header">
            <h4>Edit Member</h4>
            <p class="opacity-75 mb-0" style="font-family: 'Sarabun'">แก้ไขข้อมูลสมาชิก</p>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger rounded-3"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" name="fullname" class="form-control" id="fullname" value="<?= htmlspecialchars($fullname) ?>" required>
                    <label for="fullname">ชื่อ-นามสกุล</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($email) ?>" required>
                    <label for="email">อีเมล</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" name="major" id="major" required>
                        <option value="Computer Science" <?= ($major == 'Computer Science') ? 'selected' : '' ?>>Computer Science (CS)</option>
                        <option value="Information Technology" <?= ($major == 'Information Technology') ? 'selected' : '' ?>>Information Technology (IT)</option>
                        <option value="Software Engineering" <?= ($major == 'Software Engineering') ? 'selected' : '' ?>>Software Engineering (SE)</option>
                        <option value="Multimedia Technology" <?= ($major == 'Multimedia Technology') ? 'selected' : '' ?>>Multimedia Technology (MT)</option>
                        <option value="Artificial Intelligence" <?= ($major == 'Artificial Intelligence') ? 'selected' : '' ?>>Artificial Intelligence (AI)</option>
                        <option value="Other" <?= ($major == 'Other') ? 'selected' : '' ?>>อื่นๆ</option>
                    </select>
                    <label for="major">สาขาวิชา</label>
                </div>

                <div class="form-floating mb-4">
                    <select class="form-select" name="academic_year" id="year" required>
                        <option value="" disabled>เลือกปีการศึกษา...</option>
                        <?php 
                        $current_year = date("Y") + 543;
                        for($i = $current_year + 1; $i >= $current_year - 4; $i--): 
                        ?>
                            <option value="<?= $i ?>" <?= ($academic_year == $i) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                    <label for="year">ปีการศึกษา (พ.ศ.)</label>
                </div>
                
                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                <a href="index.php" class="btn-link">ยกเลิกและย้อนกลับ</a>
            </form>
        </div>
    </div>
</div>

<footer class="text-center py-4 mt-auto text-muted" style="border-top: 1px solid #e5e7eb; font-size: 0.9rem;">
    <div class="container">
        © Software Engineering, NPRU. All rights reserved.
    </div>
</footer>

</body>
</html>
