<?php

require_once __DIR__ . '/../config/database.php';

class MemberController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function index()
    {
        $stmt = $this->pdo->query("SELECT * FROM members ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function store($data)
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['status' => false, 'errors' => $errors];
        }

        $sql = "INSERT INTO members (fullname, email, major, academic_year) VALUES (:fullname, :email, :major, :academic_year)";
        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute([
                ':fullname' => $data['fullname'],
                ':email' => $data['email'],
                ':major' => $data['major'],
                ':academic_year' => $data['academic_year']
            ]);
            return ['status' => true, 'message' => 'เพิ่มสมาชิกเรียบร้อยแล้ว'];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation (e.g., duplicate email)
                return ['status' => false, 'errors' => ['email' => 'อีเมลนี้ถูกใช้งานแล้ว']];
            }
            return ['status' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'];
        }
    }

    public function show($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM members WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data)
    {
        $errors = $this->validate($data, $id);
        if (!empty($errors)) {
            return ['status' => false, 'errors' => $errors];
        }

        $sql = "UPDATE members SET fullname = :fullname, email = :email, major = :major, academic_year = :academic_year WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        try {
            $stmt->execute([
                ':fullname' => $data['fullname'],
                ':email' => $data['email'],
                ':major' => $data['major'],
                ':academic_year' => $data['academic_year'],
                ':id' => $id
            ]);
            return ['status' => true, 'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว'];
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return ['status' => false, 'errors' => ['email' => 'อีเมลนี้ถูกใช้งานแล้ว']];
            }
            return ['status' => false, 'message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล'];
        }
    }

    public function destroy($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM members WHERE id = :id");
        if ($stmt->execute([':id' => $id])) {
            return ['status' => true, 'message' => 'ลบข้อมูลเรียบร้อยแล้ว'];
        }
        return ['status' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล'];
    }

    private function validate($data, $id = null)
    {
        $errors = [];

        if (empty($data['fullname'])) {
            $errors['fullname'] = 'กรุณากรอกชื่อ-นามสกุล';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'กรุณากรอกอีเมล';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'รูปแบบอีเมลไม่ถูกต้อง';
        }

        if (empty($data['major'])) {
            $errors['major'] = 'กรุณากรอกสาขาวิชา';
        }

        if (empty($data['academic_year'])) {
            $errors['academic_year'] = 'กรุณากรอกปีการศึกษา';
        } elseif (!is_numeric($data['academic_year']) || strlen($data['academic_year']) != 4) {
            $errors['academic_year'] = 'ปีการศึกษาต้องเป็นตัวเลข 4 หลัก (พ.ศ.)';
        }

        return $errors;
    }
}
