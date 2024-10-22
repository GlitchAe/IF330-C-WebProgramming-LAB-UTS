<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Get status from URL parameter
$status = $_GET['status'] ?? 'backlog'; // Default ke 'backlog' jika tidak ada status

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'db.php';
    $title = htmlspecialchars($_POST['title']);
    $user_id = $_SESSION['user_id'];
    $description = htmlspecialchars($_POST['description'] ?? '');
    $priority = htmlspecialchars($_POST['priority'] ?? 'low');
    $due_date = htmlspecialchars($_POST['due_date'] ?? null);
    $status = htmlspecialchars($_POST['status']);

    $stmt = $pdo->prepare("INSERT INTO todo_lists (user_id, title, description, priority, due_date, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $description, $priority, $due_date, $status]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>New Todo</title>
    <link href="./output.css" rel="stylesheet">
    <style>
    /* For WebKit browsers (Chrome, Safari, etc.) */
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1); /* Inverts the color to white */
    }

    /* For Mozilla Firefox */
    input[type="date"]::-moz-focus-inner {
        border: 0;
    }
</style>
<script>
        // JavaScript to force the date picker to open on input click
        function showDatePicker() {
            document.getElementById('due_date').showPicker();
        }
    </script>
</head>
<body class="bg-dark min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-2xl text-white font-bold mb-6">Create New Todo</h2>
            
            <form method="POST" action="" class="space-y-4">
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
                
                <div>
                    <label for="title" class="block text-gray-300 mb-2">Title</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="w-full bg-gray-700 text-white rounded p-2"
                           placeholder="Enter todo title" 
                           required>
                </div>

                <div>
                    <label for="description" class="block text-gray-300 mb-2">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="w-full bg-gray-700 text-white rounded p-2"
                              rows="3"
                              placeholder="Enter description"></textarea>
                </div>

                <div>
                    <label for="priority" class="block text-gray-300 mb-2">Priority</label>
                    <select id="priority" 
                            name="priority" 
                            class="w-full bg-gray-700 text-white rounded p-2">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div class="">
                    <label for="due_date" class="block text-gray-300 mb-2">Due Date</label>
                    <input type="date" 
                           id="due_date" 
                           name="due_date" 
                           class="w-full bg-gray-700 text-white rounded  p-2"
                           onclick="showDatePicker()">
                </div>
<br/>
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="bg-gradient font-bold shadow-3xl text-white px-4 py-2 rounded hover:bg-blue-700">
                        Create Todo
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