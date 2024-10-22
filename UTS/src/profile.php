<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require 'db.php';
$user_id = $_SESSION['user_id'];

// Fetch the user data including the profile image
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-dark text-center">

    <div class="max-w-xl mx-auto p-6 bg-light-10 shadow-lg rounded-lg mt-10">
        <h2 class="text-3xl font-bold text-white mb-6">Your Profile</h2>
        
            <div class="flex items-center">
                <p class="font-semibold text-white">Username:</p>
                <p class="ml-2 text-white"><?php echo htmlspecialchars($user['username']); ?></p>
            </div>
            <div class="flex items-center">
                <p class="font-semibold text-white">Email:</p>
                <p class="ml-2 text-white"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
        
        <div class="mt-8 space-x-4">
            <a href="edit_profile.php" class="px-4 py-2 bg-gradient text-white font-bold rounded">Edit Profile</a>
            <a href="index.php" class="px-4 py-2 bg-gray-600 text-white rounded">Back</a>
        </div>
    </div>

</body>
</html>