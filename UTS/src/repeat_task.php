<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'db.php';
$task_id = $_GET['id'];
$list_id = $_GET['list_id'];

// Ambil data task asli berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = htmlspecialchars($task['description']);
    $new_deadline = $_POST['deadline'];

    // Menambahkan task baru dengan deskripsi yang sama tetapi deadline baru
    $stmt = $pdo->prepare("INSERT INTO tasks (todo_list_id, description, deadline, is_completed) VALUES (?, ?, ?, 0)");
    $stmt->execute([$list_id, $description, $new_deadline]);

    // Kembali ke halaman view_todo.php untuk melihat daftar tugas
    header("Location: view_todo.php?id=" . $list_id);
    exit;
}
?>

<!-- Form untuk mengulangi task dengan deadline baru -->
<form method="POST" action="">
    <p>Task: <?php echo htmlspecialchars($task['description']); ?></p>
    <label for="deadline">New Deadline:</label>
    <input type="date" name="deadline" required>
    <button type="submit">Repeat Task</button>
</form>

<!-- Tombol untuk kembali ke halaman sebelumnya -->
<a href="javascript:history.back()">Back</a>

