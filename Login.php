<?php
session_start();
include "db.php";

$message = "";
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $message = "Registration successful! Please log in.";
} elseif (isset($_GET['error']) && $_GET['error'] == '1') {
    $message = "Invalid email or password.";
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login Page</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <nav>
            <a href="#">
                <img src="1.jpg" alt="Logo">
            </a>

            <div class="Navlinks">
                <a href="Home.html">Home</a>
                <a href="Register.html">Register</a>
                <a href="#">Login</a>
            </div>
        </nav>

        <div class="login-container">
            <h1>Enter the Required Credentials</h1>
            <?php if ($message !== "") { echo '<p style="color:green;">' . htmlspecialchars($message) . '</p>'; } ?>

            <form action="Login.php" method="post">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter Username" required>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter Email Address" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Password" required>

                <div class="forgot">
                    <input type="checkbox" id="check">
                    <label for="check">Remember Me</label>
                </div>

                <div class="submit-button">
                    <button type="submit">Login</button>
                </div>

                <a href="#">Forgot Password?</a>

                <p>
                    If you don't have an account, click
                    <a href="Register.html">HERE</a>
                    to register.
                </p>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '' || $username === '') {
    header("Location: Login.php?error=1");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, FullName, Email, Password FROM `user` WHERE Email = ? LIMIT 1");
if (!$stmt) {
    header("Location: Login.php?error=1");
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
mysqli_close($conn);

if ($user && password_verify($password, $user['Password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['FullName'];
    header('Location: Home.html');
    exit;
} else {
    header("Location: Login.php?error=1");
    exit;
}
?>