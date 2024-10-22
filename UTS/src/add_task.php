<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = htmlspecialchars($_POST['description']);
    $deadline = $_POST['deadline'];
    $list_id = $_POST['list_id'];

    if (empty($description) || empty($deadline)) {
        echo json_encode(['error' => 'Description and deadline are required']);
        exit;
    } else {
        // Menyimpan task baru ke dalam database
        $stmt = $pdo->prepare("INSERT INTO tasks (todo_list_id, description, deadline) VALUES (?, ?, ?)");
        $stmt->execute([$list_id, $description, $deadline]);

        // Dapatkan ID task baru
        $task_id = $pdo->lastInsertId();

        // Kirim respons sukses sebagai JSON
        echo json_encode([
            'success' => true,
            'task_id' => $task_id,
            'description' => $description,
            'deadline' => $deadline
        ]);
        exit;
    }
}
?>
