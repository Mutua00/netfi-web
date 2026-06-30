<?php
session_start();
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "db.php";

$message = '';
$values = [
    'fullname' => '',
    'email' => '',
    'networktype' => '',
    'ip' => '',
    'gender' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $networktype = trim($_POST['networktype'] ?? '');
    $ip = trim($_POST['ip'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';

    $values['fullname'] = $fullname;
    $values['email'] = $email;
    $values['networktype'] = $networktype;
    $values['ip'] = $ip;
    $values['gender'] = $gender;

    $errors = [];
    if ($fullname === '') {
        $errors[] = 'Full name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if ($password === '' || strlen($password) < 8) {
        $errors[] = 'Password required (min 8 chars).';
    }
    if ($password !== $confirmpassword) {
        $errors[] = 'Passwords do not match.';
    }
    if ($ip !== '' && !filter_var($ip, FILTER_VALIDATE_IP)) {
        $errors[] = 'Invalid IP address.';
    }

    if (!empty($errors)) {
        $message = '<p style="color:red;">' . implode('<br>', $errors) . '</p>';
    } else {
        $checkSql = 'SELECT id FROM `user` WHERE Email = ? LIMIT 1';
        $stmt = mysqli_prepare($conn, $checkSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                $message = '<p style="color:red;">Email already registered.</p>';
            } else {
                mysqli_stmt_close($stmt);
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insertSql = 'INSERT INTO `user` (FullName, Email, networktype, ip, gender, Password) VALUES (?, ?, ?, ?, ?, ?)';
                $stmt = mysqli_prepare($conn, $insertSql);
                if (!$stmt) {
                    $message = '<p style="color:red;">Registration failed. Please try again.</p>';
                } else {
                    mysqli_stmt_bind_param($stmt, 'ssssss', $fullname, $email, $networktype, $ip, $gender, $hash);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                        mysqli_close($conn);
                        header('Location: Login.php?registered=1');
                        exit;
                    } else {
                        $message = '<p style="color:red;">Registration failed. Please try again.</p>';
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        } else {
            $message = '<p style="color:red;">Unable to process your registration right now.</p>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <nav>
        <a href="#">
            <img src="1.jpg" alt="Logo">
        </a>
        <div class="Navlinks">
            <a href="Home.html">Home</a>
            <a href="#">Register</a>
            <a href="Login.php">Login</a>
        </div>
    </nav>

    <p>Enter your details as required:</p>
    <?php if ($message !== '') { echo $message; } ?>

    <form action="Register.php" method="post">
        <div class="field">
            <label for="fullname">Full Names</label>
            <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($values['fullname']) ?>" required>
        </div>

        <div class="field">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($values['email']) ?>" required>
        </div>

        <div class="field">
            <label for="networktype">Network Type</label>
            <input type="text" id="networktype" name="networktype" value="<?= htmlspecialchars($values['networktype']) ?>" required>
        </div>

        <div class="field">
            <label for="ip">IP Address</label>
            <input type="text" id="ip" name="ip" value="<?= htmlspecialchars($values['ip']) ?>" placeholder="192.168.1.1">
        </div>

        <div class="field">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Select</option>
                <option value="Male" <?= ($values['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($values['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                <option value="Other" <?= ($values['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="field">
            <label for="confirmpassword">Confirm password</label>
            <input type="password" id="confirmpassword" name="confirmpassword" required>
        </div>

        <div class="submit">
            <button type="submit">Register</button>
        </div>

        <div class="textlink">
            <p>
                If you already have an account, click
                <a href="Login.php">HERE</a>
                to login.
            </p>
        </div>
    </form>
</body>
</html>
