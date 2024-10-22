<?php
ob_clean();
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db.php';

// Initialize arrays for each status
$product_backlog = [];
$sprint_backlog = [];
$in_progress = [];
$testing = [];
$done = [];

try {
    // Get user's todo lists
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT id, title, description, status, priority, created_at 
        FROM todo_lists 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$user_id]);
    $todo_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sort todo lists by status
    foreach ($todo_lists as $item) {
        switch ($item['status']) {
            case 'backlog':
                $product_backlog[] = $item;
                break;
            case 'sprint':
                $sprint_backlog[] = $item;
                break;
            case 'in_progress':
                $in_progress[] = $item;
                break;
            case 'testing':
                $testing[] = $item;
                break;
            case 'done':
                $done[] = $item;
                break;
            default:
                $product_backlog[] = $item;
        }
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $error_message = "An error occurred while loading your todo lists. Please try again later.";
}

// Helper functions for priority colors
function getPriorityColor($priority) {
    $priority = strtolower(trim($priority ?? 'low'));
    return match($priority) {
        'high' => 'bg-red-700',
        'medium' => 'bg-yellow-600',
        'low' => 'bg-green-700',
        default => 'bg-gray-700'
    };
}

function getPriorityBadgeColor($priority) {
    $priority = strtolower(trim($priority ?? 'low'));
    return match($priority) {
        'high' => 'bg-red-800',
        'medium' => 'bg-yellow-700',
        'low' => 'bg-green-800',
        default => 'bg-gray-800'
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Todo Lists</title>
    <link href="./output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    

</head>
<body class="bg-dark min-h-screen">
    <!-- Navbar -->
<nav class="bg-gradient border-b border-gray-700 px-4 py-2">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
                <h1 class="text-white text-xl font-bold">ToDo</h1>
            </div>
        <div class="flex-grow text-center">
            <!-- Real-time date and clock -->
            <div class="inline-block text-white text-lg font-semibold">
                <span id="date"></span>
                <span id="clock"></span>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <a href="profile.php" class="text-white hover:text-white font-bold transition">Profile</a>
            <a href="logout.php" class="bg-danger hover:bg-red-600 text-white px-4 py-1 rounded transition">
                Logout
            </a>
        </div>
    </div>
</nav>

<!-- JavaScript to display real-time date and time -->
<script>
    function updateClockAndDate() {
        const now = new Date();

        // Format hours, minutes, and seconds
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const currentTime = `${hours}:${minutes}:${seconds}`;

        // Format date as Weekday, Month Day, Year
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const currentDate = now.toLocaleDateString(undefined, options);

        // Set date and time in the HTML
        document.getElementById('clock').textContent = currentTime;
        document.getElementById('date').textContent = currentDate;
    }

    setInterval(updateClockAndDate, 1000); // Update every second
    updateClockAndDate(); // Initial call to display right away
</script>

    <!-- Main Content -->
    <div class="p-6">
        <?php if (isset($error_message)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Board Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <h2 class="text-white text-2xl font-semibold">Your To do Lists</h2>
                <div class="flex items-center space-x-2 text-gray-400">
                    <span class="px-2 py-1 bg-gray-700 rounded text-sm">Private</span>
                </div>
            </div>
        </div>

        <!-- List To Do -->
        <div class="flex space-x-4 overflow-x-auto pb-4">
            <!-- To Do Column -->
            <div class="flex-shrink-0 w-72">
                <div class="bg-gray-800 rounded-md shadow">
                    <div class="p-3">
                        <h3 class="text-white font-semibold mb-2">To Do</h3>
                        <?php foreach ($product_backlog as $item): ?>
                            <div class="<?php echo getPriorityColor($item['priority']); ?> rounded mb-2 p-2 hover:opacity-90 cursor-pointer">
                                <p class="text-white text-sm"><?php echo htmlspecialchars($item['title']); ?></p>
                                <span class="inline-block text-xs text-white px-2 py-1 rounded mt-1 <?php echo getPriorityBadgeColor($item['priority']); ?>">
                                    <?php echo ucfirst(strtolower(trim($item['priority'] ?? 'low'))); ?>
                                </span>
                                <div class="flex space-x-2 mt-2">
                                    <a href="edit_todo.php?id=<?php echo $item['id']; ?>" 
                                       class="text-blue-400 hover:text-blue-600">
                                       <span class="bg-dark p-1 text-[xs] rounded-lg text-white material-symbols-outlined">
                                            edit
                                        </span>
                                    </a>
                                    <a href="delete_todo.php?id=<?php echo $item['id']; ?>" 
                                       class="text-red-400 hover:text-red-600"
                                       onclick="return confirm('Are you sure you want to delete this item?');">
                                       <span class="bg-dark p-1 text-[xs] rounded-lg text-white material-symbols-outlined">
                                            delete
                                        </span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="new_todo.php?status=backlog" 
                           class="block mt-2 text-gray-400 hover:text-white p-2 rounded hover:bg-gray-700 transition">
                            + Add To Do
                        </a>
                    </div>
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="flex-shrink-0 w-72">
                <div class="bg-gray-800 rounded-md shadow">
                    <div class="p-3">
                        <h3 class="text-white font-semibold mb-2">In Progress</h3>
                        <?php foreach ($in_progress as $item): ?>
                            <div class="<?php echo getPriorityColor($item['priority']); ?> rounded mb-2 p-2 hover:opacity-90 cursor-pointer">
                                <p class="text-white text-sm"><?php echo htmlspecialchars($item['title']); ?></p>
                                <span class="inline-block text-xs text-white px-2 py-1 rounded mt-1 <?php echo getPriorityBadgeColor($item['priority']); ?>">
                                    <?php echo ucfirst(strtolower(trim($item['priority'] ?? 'low'))); ?>
                                </span>
                                <div class="flex space-x-2 mt-2">
                                    <a href="edit_todo.php?id=<?php echo $item['id']; ?>" 
                                       class="text-blue-400 hover:text-blue-600">
                                       <span class="bg-dark p-1 text-[xs] rounded-lg text-white material-symbols-outlined">
                                            edit
                                        </span>
                                    </a>
                                    <a href="delete_todo.php?id=<?php echo $item['id']; ?>" 
                                       class="text-red-400 hover:text-red-600"
                                       onclick="return confirm('Are you sure you want to delete this item?');">
                                       <span class="bg-dark p-1 text-[xs] rounded-lg text-white material-symbols-outlined">
                                            delete
                                        </span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="new_todo.php?status=progress" 
                           class="block mt-2 text-gray-400 hover:text-white p-2 rounded hover:bg-gray-700 transition">
                            + Add To Do
                        </a>
                    </div>
                </div>
            </div>

            <!-- Done Column -->
            <div class="flex-shrink-0 w-72">
                <div class="bg-gray-800 rounded-md shadow">
                    <div class="p-3">
                        <h3 class="text-white font-semibold mb-2">Done</h3>
                        <?php foreach ($done as $item): ?>
                            <div class="<?php echo getPriorityColor($item['priority']); ?> rounded mb-2 p-2 hover:opacity-90 cursor-pointer">
                                <p class="text-white text-sm"><?php echo htmlspecialchars($item['title']); ?></p>
                                <span class="inline-block text-xs text-white px-2 py-1 rounded mt-1 <?php echo getPriorityBadgeColor($item['priority']); ?>">
                                    <?php echo ucfirst(strtolower(trim($item['priority'] ?? 'low'))); ?>
                                </span>
                                <div class="flex space-x-2 mt-2">
                                    <a href="edit_todo.php?id=<?php echo $item['id']; ?>" 
                                       class="text-blue-400 hover:text-blue-600">
                                       <span class="bg-dark p-1 text-[xs] rounded-lg text-white material-symbols-outlined">
                                            edit
                                        </span>
                                    </a>
                                    <a href="delete_todo.php?id=<?php echo $item['id']; ?>" 
                                       class="text-red-400 hover:text-red-600"
                                       onclick="return confirm('Are you sure you want to delete this item?');">
                                       <span class="bg-dark p-1 text-[xs] rounded-lg text-white material-symbols-outlined">
                                            delete
                                        </span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <a href="new_todo.php?status=done" 
                           class="block mt-2 text-gray-400 hover:text-white p-2 rounded hover:bg-gray-700 transition">
                            + Add To Do
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>