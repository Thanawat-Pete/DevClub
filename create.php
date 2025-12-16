<?php
require_once 'config/database.php';

$error = '';
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
            $stmt = $pdo->prepare("SELECT fullname, email FROM members WHERE email = ? OR fullname = ?");
            $stmt->execute([$email, $fullname]);
            $existing = $stmt->fetch();
            if ($existing) {
                if ($existing['email'] == $email) {
                    $error = "อีเมลนี้มีอยู่ในระบบแล้ว";
                } else {
                    $error = "ชื่อ-นามสกุลนี้มีอยู่ในระบบแล้ว";
                }
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
    <title>เพิ่มสมาชิกใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f0f2f5; display: flex; align-items: center; min-height: 100vh; }
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
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border: none;
        }
        .card-header h4 { font-family: 'Outfit', sans-serif; margin: 0; font-weight: 700; }
        .card-body { padding: 40px; }
        .form-floating > label { color: #6b7280; }
        .form-control:focus, .form-select:focus { border-color: #4f46e5; box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25); }
        .btn-primary { 
            background: #4f46e5; border: none; padding: 12px; border-radius: 10px; font-weight: 600; width: 100%; margin-top: 10px;
            transition: all 0.3s;
        }
        .btn-primary:hover { background: #4338ca; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .btn-link { text-decoration: none; color: #6b7280; display: block; text-align: center; margin-top: 20px; }
        .btn-link:hover { color: #374151; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-card">
        <div class="card-header">
            <h4>Add New Member</h4>
            <p class="opacity-75 mb-0" style="font-family: 'Sarabun'">กรอกข้อมูลสมาชิกใหม่</p>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger rounded-3"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-floating mb-3">
                    <input type="text" name="fullname" class="form-control" id="fullname" placeholder="ชื่อ-นามสกุล" value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>" required>
                    <label for="fullname">ชื่อ-นามสกุล</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                    <label for="email">อีเมล</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" name="major" id="major" required>
                        <option value="" disabled <?= !isset($major) ? 'selected' : '' ?>>เลือกสาขาวิชา...</option>
                        <option value="Computer Science" <?= (isset($major) && $major == 'Computer Science') ? 'selected' : '' ?>>Computer Science (CS)</option>
                        <option value="Information Technology" <?= (isset($major) && $major == 'Information Technology') ? 'selected' : '' ?>>Information Technology (IT)</option>
                        <option value="Software Engineering" <?= (isset($major) && $major == 'Software Engineering') ? 'selected' : '' ?>>Software Engineering (SE)</option>
                        <option value="Other" <?= (isset($major) && $major == 'Other') ? 'selected' : '' ?>>อื่นๆ</option>
                    </select>
                    <label for="major">สาขาวิชา</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="number" name="academic_year" class="form-control" id="year" placeholder="2567" value="<?= isset($academic_year) ? htmlspecialchars($academic_year) : '' ?>" required>
                    <label for="year">ปีการศึกษา (พ.ศ.)</label>
                </div>
                
                <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                <a href="index.php" class="btn-link">ยกเลิกและย้อนกลับ</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
