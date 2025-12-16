<?php
require_once 'config/database.php';

// Handle Delete Request (Still needed for fallback, but SweetAlert travels to this url via JS)
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
    $total_members = count($members);
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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700&family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f0f2f5; color: #333; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', 'Sarabun', sans-serif; font-weight: 700; }
        
        .main-container { max-width: 1200px; margin-top: 40px; }
        
        .stat-card {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
            margin-bottom: 30px;
        }
        .stat-number { font-size: 3rem; font-weight: bold; line-height: 1; }
        .stat-label { font-size: 1.1rem; opacity: 0.9; }
        
        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 30px;
        }

        .avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #e0e7ff; }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        .search-box input {
            padding-left: 45px;
            border-radius: 50px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: all 0.3s;
        }
        .search-box input:focus {
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
            border-color: #6366f1;
        }
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .table > :not(caption) > * > * { padding: 1rem 1rem; border-bottom-color: #f3f4f6; }
        .table-hover tbody tr:hover { background-color: #f9fafb; transition: 0.2s; }
        .btn-action { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: 0.2s; }
        .btn-edit { background: #e0e7ff; color: #4338ca; }
        .btn-edit:hover { background: #4338ca; color: white; }
        .btn-delete { background: #fee2e2; color: #b91c1c; }
        .btn-delete:hover { background: #b91c1c; color: white; }

        .badge-soft { padding: 6px 12px; border-radius: 50px; font-size: 0.85rem; font-weight: 500; }
        .bg-major-cs { background-color: #dbeafe; color: #1e40af; }
        .bg-major-it { background-color: #d1fae5; color: #065f46; }
        .bg-major-se { background-color: #fce7f3; color: #9d174d; }
        .bg-major-default { background-color: #f3f4f6; color: #374151; }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="logo.png" alt="NPRU Logo" width="40" height="auto" class="d-inline-block align-text-top me-3 bg-white rounded-circle p-1">
      <div>
          <div style="line-height: 1; font-size: 1.1rem;">Nakhon Pathom Rajabhat University</div>
          <div style="font-size: 0.8rem; opacity: 0.8; font-weight: 400;">DevClub Membership System</div>
      </div>
    </a>
  </div>
</nav>

<div class="container main-container">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="section-title text-primary"><i class="fa-solid fa-users-rectangle me-2"></i>DevClub Members</h2>
            <p class="text-muted">จัดการข้อมูลสมาชิกชมรมอย่างมีประสิทธิภาพ</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="create.php" class="btn btn-primary btn-lg shadow-sm" style="border-radius: 50px; padding-left: 25px; padding-right: 25px;">
                <i class="fa-solid fa-plus me-2"></i> เพิ่มสมาชิกใหม่
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="stat-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-number"><?= $total_members ?></div>
                    <div class="stat-label">สมาชิกทั้งหมด</div>
                </div>
                <i class="fa-solid fa-user-group fa-3x opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="content-card">
        <div class="search-box">
            <i class="fa-solid fa-search search-icon"></i>
            <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="ค้นหาชื่อ, อีเมล, หรือสาขาวิชา...">
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-secondary text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
                        <th style="width: 50px;">#</th>
                        <th>สมาชิก</th>
                        <th>อีเมล</th>
                        <th>สาขาวิชา</th>
                        <th>ปีการศึกษา</th>
                        <th class="text-end">เครื่องมือ</th>
                    </tr>
                </thead>
                <tbody id="memberTable">
                    <?php if (count($members) > 0): ?>
                        <?php foreach ($members as $index => $member): ?>
                            <?php 
                                // Color badging logic
                                $major_class = 'bg-major-default';
                                if (stripos($member['major'], 'Computer Science') !== false || stripos($member['major'], 'CS') !== false || stripos($member['major'], 'วิทยาการคอม') !== false) $major_class = 'bg-major-cs';
                                elseif (stripos($member['major'], 'Information Technology') !== false || stripos($member['major'], 'IT') !== false || stripos($member['major'], 'ไอที') !== false) $major_class = 'bg-major-it';
                                elseif (stripos($member['major'], 'Software Engineering') !== false || stripos($member['major'], 'SE') !== false) $major_class = 'bg-major-se';
                            ?>
                            <tr>
                                <td class="text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($member['fullname']) ?>&background=random&color=fff&size=128" class="avatar me-3" alt="Avatar">
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($member['fullname']) ?></div>
                                            <small class="text-muted" style="font-size: 0.8rem;">ID: <?= str_pad($member['id'], 5, '0', STR_PAD_LEFT) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary"><?= htmlspecialchars($member['email']) ?></td>
                                <td><span class="badge badge-soft <?= $major_class ?>"><?= htmlspecialchars($member['major']) ?></span></td>
                                <td class="fw-medium text-center" style="width: 100px;"><?= htmlspecialchars($member['academic_year']) ?></td>
                                <td class="text-end">
                                    <a href="edit.php?id=<?= $member['id'] ?>" class="btn btn-action btn-edit me-1" title="แก้ไข"><i class="fa-solid fa-pen"></i></a>
                                    <button onclick="confirmDelete(<?= $member['id'] ?>)" class="btn btn-action btn-delete" title="ลบ"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa-regular fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">ยังไม่มีข้อมูลสมาชิกในระบบ</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="text-center py-4 mt-5 text-muted" style="border-top: 1px solid #e5e7eb; font-size: 0.9rem;">
    <div class="container">
        © Software Engineering, NPRU. All rights reserved.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Real-time Search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#memberTable tr');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // SweetAlert2 Delete Confirmation
    function confirmDelete(id) {
        Swal.fire({
            title: 'แน่ใจหรือไม่?',
            text: "ข้อมูลนี้จะถูกลบถาวรและไม่สามารถกู้คืนได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ลบทันที!',
            cancelButtonText: 'ยกเลิก',
            background: '#fff',
            customClass: {
                popup: 'rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `index.php?delete_id=${id}`;
            }
        })
    }

    // Check query param for success message
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('msg') === 'deleted') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: 'ลบข้อมูลสำเร็จแล้ว'
        });
        // cleanup URL
        window.history.replaceState({}, document.title, "index.php");
    }
</script>
</body>
</html>
