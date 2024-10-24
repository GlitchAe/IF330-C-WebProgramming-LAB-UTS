<?php
session_start();
include('db.php');

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Invalid email or password.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Check for error message and display it
if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Remove the error message from the session
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="./output.css" rel="stylesheet">
    <style>
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
</head>

<body class="bg-dark flex flex-col items-center justify-center h-screen">
    <div class="flex rounded-3xl p-6 flex-col justify-between text-center py-8 w-80 bg-light-10 slide-in"
        id="login-form">
        <h2 class="text-white font-bold text-2xl mb-6">Login <a class="bg-gradient-to-r from-primary to-secondary inline-block text-transparent bg-clip-text"
        href="register.php">To Do List</a></h1></h2>
        <form method="POST" action="login.php">
            <div class="flex flex-col items-start mb-4">
                <h1 class="text-white text-xs mb-2" for="email">Email</h1>
                <input class="bg-dark p-2 text-xs text-white flex h-8 w-full shadow-sm" type="email" name="email"
                    id="email" placeholder="example@gmail.com" required>
            </div>
            <div class="flex flex-col items-start">
                <h1 class="text-white text-xs mb-2" for="password">Password</h1>
                <input placeholder="password" class="bg-dark p-2 text-xs text-white flex h-8 w-full shadow-sm"
                    type="password" name="password" id="password" required><br><br>
            </div>
            <?php if (isset($errorMessage)): ?>
                <div class="error text-danger text-xs mb-2"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            <button
                class="mb-2 bg-gradient rounded-full text-sm w-full font-bold px-6 py-1 text-white transition duration-400 ease-in-out hover:shadow-glowy"
                type="submit">Login</button>
        </form>
        <h1 class="text-white text-xs">Don't have an account? <a class="font-bold text-secondary"
                href="register.php">Register here</a></h1>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('login-form');
            // Add the active class to trigger the slide-in effect
            setTimeout(() => {
                loginForm.classList.add('slide-in-active');
            }, 100); // Delay to allow the DOM to load
        });
    </script>
</body>

</html>