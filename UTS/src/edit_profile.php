<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'db.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']); 
    $email = htmlspecialchars($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    // Initialize variable for profile image
    $profile_image = null;


    // Prepare the SQL query
    $query = "UPDATE users SET username = ?, email = ?"; // Initialize the query
    $params = [$username, $email];

    // Add password to query if provided
    if ($password) {
        $query .= ", password = ?"; // Update query to include password
        $params[] = $password; // Add password to parameters
    }

    // Complete the query
    $query .= " WHERE id = ?"; // Finalize the query with the condition
    $params[] = $user_id; // Add user ID to parameters

    // Execute the query
    $stmt = $pdo->prepare($query);
    if (!$stmt->execute($params)) {
        // Log error
        print_r($stmt->errorInfo());
    }

    header("Location: profile.php");
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-dark text-center">

    <div class="max-w-xl mx-auto p-6 bg-light-10 shadow-lg rounded-lg mt-10">
        <h2 class="text-3xl font-bold text-white mb-6">Edit Your Profile</h2>
        
        <form method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-semibold text-white">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="w-full p-2 mt-2 bg-gray-700 text-white rounded focus:outline-none">
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold text-white">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full p-2 mt-2 bg-gray-700 text-white rounded focus:outline-none">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-white">New Password (optional)</label>
                <input type="password" id="password" name="password" placeholder="New Password" class="w-full p-2 mt-2 bg-gray-700 text-white rounded focus:outline-none">
            </div>
            <button type="submit" class="px-4 py-2 bg-gradient text-white font-bold rounded">Update</button>
        </form>

        <div class="mt-8">
            <a href="index.php" class="px-4 py-2 bg-gray-600 text-white rounded">Back</a>
        </div>
    </div>

</body>
</html>
