<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'db.php';
$task_id = $_GET['id'];
$list_id = $_GET['list_id'];

// Menghapus task dari database
$stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);

// Kembali ke halaman view_todo.php setelah menghapus task
header("Location: view_todo.php?id=" . $list_id);
exit;

?>
