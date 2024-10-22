<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'db.php';
$task_id = $_GET['id'];
$list_id = $_GET['list_id'];

// Update status tugas menjadi 'Completed'
$stmt = $pdo->prepare("UPDATE tasks SET is_completed = 1 WHERE id = ?");
$stmt->execute([$task_id]);

// Kembali ke halaman view_todo.php setelah tugas diupdate
header("Location: view_todo.php?id=" . $list_id);
exit;
?>
