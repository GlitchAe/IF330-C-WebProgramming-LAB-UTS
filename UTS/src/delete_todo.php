<!-- delete_todo.php -->
<?php
require 'db.php';

$id = $_GET['id'];

$stmt = $pdo->prepare('DELETE FROM todo_lists WHERE id = ?');
$stmt->execute([$id]);

// Redirect back to the list page
header('Location: index.php');
exit;
?>
