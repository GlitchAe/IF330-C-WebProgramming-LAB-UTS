<?php
require 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM todo_lists WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $priority = htmlspecialchars($_POST['priority']);
    $due_date = htmlspecialchars($_POST['due_date']);
    $status = htmlspecialchars($_POST['status']);

    // Update the task in the database
    $stmt = $pdo->prepare('UPDATE todo_lists SET title = ?, description = ?, priority = ?, due_date = ?, status = ? WHERE id = ?');
    $stmt->execute([$title, $description, $priority, $due_date, $status, $id]);

    // Redirect back to the list page
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Todo</title>
    <link href="./output.css" rel="stylesheet">
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1); /* Inverts the color to white */
        }
        .slide-in {
        transform: translateY(-30px);
        opacity: 0;
        transition: transform 1s ease, opacity 1s ease;
        }
        .slide-in-active {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
    <script>
        function showDatePicker() {
            document.getElementById('due_date').showPicker();
        }
    </script>
</head>
<body class="bg-dark min-h-screen">
    <div class="container mx-auto px-4 py-8 slide-in">
        <div class="max-w-md mx-auto bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-2xl text-white font-bold mb-6">Edit Todo</h2>
            
            <form method="POST" action="edit_todo.php?id=<?php echo $id; ?>" class="space-y-4">
                <div>
                    <label for="title" class="block text-gray-300 mb-2">Title</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="w-full bg-gray-700 text-white rounded p-2"
                           value="<?php echo htmlspecialchars($item['title']); ?>"
                           required>
                </div>

                <div>
                    <label for="description" class="block text-gray-300 mb-2">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="w-full bg-gray-700 text-white rounded p-2"
                              rows="3"
                              placeholder="Enter description"><?php echo htmlspecialchars($item['description']); ?></textarea>
                </div>

                <div>
                    <label for="priority" class="block text-gray-300 mb-2">Priority</label>
                    <select id="priority" 
                            name="priority" 
                            class="w-full bg-gray-700 text-white rounded p-2">
                        <option value="low" <?php echo ($item['priority'] == 'low') ? 'selected' : ''; ?>>Low</option>
                        <option value="medium" <?php echo ($item['priority'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="high" <?php echo ($item['priority'] == 'high') ? 'selected' : ''; ?>>High</option>
                    </select>
                </div>

                <div class="">
                    <label for="due_date" class="block text-gray-300 mb-2">Due Date</label>
                    <input type="date" 
                        id="due_date" 
                        name="due_date" 
                        class="w-full bg-gray-700 text-white rounded p-2"
                        value="<?php echo isset($item['due_date']) ? htmlspecialchars($item['due_date']) : ''; ?>"  
                        onclick="showDatePicker()">
                </div>


                <div>
                    <label for="status" class="block text-gray-300 mb-2">Status</label>
                    <select id="status" 
                            name="status" 
                            class="w-full bg-gray-700 text-white rounded p-2">
                        <option value="backlog" <?php echo ($item['status'] == 'backlog') ? 'selected' : ''; ?>>To Do</option>
                        <option value="in_progress" <?php echo ($item['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="done" <?php echo ($item['status'] == 'done') ? 'selected' : ''; ?>>Done</option>
                    </select>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" 
                            class="bg-gradient font-bold shadow-3xl text-white px-4 py-2 rounded hover:bg-blue-700">
                        Update Todo
                    </button>
                    
                    <a href="index.php" 
                       class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const todoItems = document.querySelectorAll('.slide-in'); // Ambil semua elemen dengan kelas slide-in
        // Tambahkan kelas slide-in-active ke setiap elemen secara bertahap
        todoItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('slide-in-active');
            }, index * 100); // Memberikan jeda waktu antara tiap item agar terlihat bertahap
        });
    });
</script>
