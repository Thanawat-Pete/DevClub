<?php
require_once 'config/database.php';

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?msg=deleted");
        exit();
    } catch (PDOException $e) {
        $error = "Error deleting record: " . $e->getMessage();
    }
}

// Fetch Members
try {
    $stmt = $pdo->query("SELECT * FROM members ORDER BY created_at DESC");
    $members = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching members: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevClub Member Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f8f9fa; }
        .container { max-width: 1000px; margin-top: 50px; }
        .table-responsive { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-add { float: right; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">📋 รายชื่อสมาชิกชมรม DevClub</h2>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ลบข้อมูลเรียบร้อยแล้ว
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <a href="create.php" class="btn btn-primary btn-add">➕ เพิ่มสมาชิกใหม่</a>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>อีเมล</th>
                    <th>สาขา</th>
                    <th>ปีการศึกษา</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($members) > 0): ?>
                    <?php foreach ($members as $index => $member): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($member['fullname']) ?></td>
                            <td><?= htmlspecialchars($member['email']) ?></td>
                            <td><?= htmlspecialchars($member['major']) ?></td>
                            <td><?= htmlspecialchars($member['academic_year']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $member['id'] ?>" class="btn btn-warning btn-sm">✏️ แก้ไข</a>
                                <a href="index.php?delete_id=<?= $member['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?');">🗑️ ลบ</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">ไม่พบข้อมูลสมาชิก</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
