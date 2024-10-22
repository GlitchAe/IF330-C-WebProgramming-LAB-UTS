<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'db.php';
$list_id = $_GET['id'];

// Ambil nilai filter dan pencarian dari parameter URL (jika ada)
$status_filter = $_GET['status'] ?? 'all';
$search_term = $_GET['search'] ?? '';

// Siapkan query dasar untuk mengambil tugas berdasarkan `todo_list_id`
$query = "SELECT * FROM tasks WHERE todo_list_id = ?";
$params = [$list_id];

// Tambahkan filter status ke query jika ada
if ($status_filter === 'completed') {
    $query .= " AND is_completed = 1";
} elseif ($status_filter === 'incomplete') {
    $query .= " AND is_completed = 0";
}

// Tambahkan pencarian ke query jika ada kata kunci
if ($search_term) {
    $query .= " AND description LIKE ?";
    $params[] = '%' . $search_term . '%';
}

// Eksekusi query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>

<!-- Form untuk filtering dan pencarian -->
<form method="GET" action="">
    <input type="hidden" name="id" value="<?php echo $list_id; ?>">
    <select name="status">
        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All</option>
        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
        <option value="incomplete" <?php echo $status_filter === 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
    </select>
    <input type="text" name="search" placeholder="Search tasks" value="<?php echo htmlspecialchars($search_term); ?>">
    <button type="submit">Filter & Search</button>
</form>

<!-- Tombol untuk menambahkan task baru -->
<a href="add_task.php?id=<?php echo $list_id; ?>">Add New Task</a>

<!-- Daftar tugas -->
<ul>
    <?php if (count($tasks) > 0): ?>
        <?php foreach ($tasks as $task): ?>
            <li>
                <?php echo htmlspecialchars($task['description']); ?> - 
                <?php echo $task['is_completed'] ? 'Completed' : 'Incomplete'; ?> - 
                Deadline: <?php echo $task['deadline'] ? htmlspecialchars($task['deadline']) : 'No deadline'; ?>
                
                <!-- Tombol untuk menghapus dan menyelesaikan task -->
                <a href="delete_task.php?id=<?php echo $task['id']; ?>&list_id=<?php echo $list_id; ?>">Delete</a>
                <?php if (!$task['is_completed']): ?>
                    <a href="complete_task.php?id=<?php echo $task['id']; ?>&list_id=<?php echo $list_id; ?>">Complete Task</a>
                    <a href="edit_task.php?id=<?php echo $task['id']; ?>&list_id=<?php echo $list_id; ?>">Edit</a>
                <?php endif; ?>
                <!-- Tombol untuk mengulangi task hanya jika sudah complete -->
                <?php if ($task['is_completed']): ?>
                    <a href="repeat_task.php?id=<?php echo $task['id']; ?>&list_id=<?php echo $list_id; ?>">Repeat Task</a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No tasks found.</li>
    <?php endif; ?>
</ul>

<!-- Tombol untuk kembali ke halaman sebelumnya -->
<a href="index.php">Back</a>
