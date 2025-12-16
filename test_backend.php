<?php

require_once 'config/database.php';
require_once 'controllers/MemberController.php';

// Instantiate Controller
$controller = new MemberController($pdo);

echo "--- Testing MemberController ---\n\n";

// 1. Store (Create)
echo "1. Testing Store:\n";
$newMember = [
    'fullname' => 'Test User ' . rand(100, 999),
    'email' => 'test' . rand(100, 999) . '@example.com',
    'major' => 'Computer Science',
    'academic_year' => '2567'
];
$result = $controller->store($newMember);
print_r($result);
echo "\n";

// 2. Index (Read All)
echo "2. Testing Index:\n";
$members = $controller->index();
echo "Found " . count($members) . " members.\n";
if (count($members) > 0) {
    echo "Latest member: " . $members[0]['fullname'] . "\n";
    $testId = $members[0]['id'];
} else {
    echo "No members found.\n";
    $testId = 0;
}
echo "\n";

if ($testId > 0) {
    // 3. Show (Read One)
    echo "3. Testing Show (ID: $testId):\n";
    $member = $controller->show($testId);
    print_r($member);
    echo "\n";

    // 4. Update
    echo "4. Testing Update (ID: $testId):\n";
    $updateData = [
        'fullname' => 'Updated User Name',
        'email' => $member['email'],
        'major' => 'IT',
        'academic_year' => '2568'
    ];
    $updateResult = $controller->update($testId, $updateData);
    print_r($updateResult);
    echo "\n";

    // 5. Destroy (Delete)
    echo "5. Testing Destroy (ID: $testId):\n";
    // Uncomment to test delete (kept commented out to verify data persistence if needed)
    // $deleteResult = $controller->destroy($testId);
    // print_r($deleteResult);
}

echo "\n--- Test Complete ---\n";
