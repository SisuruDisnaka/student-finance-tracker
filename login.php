<?php
define('PUBLIC_PAGE', 1);
require 'includes/config.php';

$error = '';

if($_POST){
    $user = rows('SELECT id, password FROM users WHERE email=?', [trim($_POST['email'])]);

    if($user && password_verify($_POST['password'], $user[0]['password'])){
        $_SESSION['uid'] = $user[0]['id'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Incorrect email or password.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050d1d">
    <title>Login | StudentFinance</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body class="auth-body">

    <div class="glow glow-one"></div>
    <div class="glow glow-two"></div>

    <main class="login-card">
        <h1>Welcome back<span>.</span></h1>
        <p class="subtitle">Log in to manage your money.</p>

        <?php if(isset($_GET['registered'])){ ?>
            <div class="alert success">Account created! Please log in.</div>
        <?php } ?>
        <?php if($error){ ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php } ?>

        <form method="post" id="loginForm">
            <div class="input-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" placeholder="name@example.com" autocomplete="email" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
            </div>

            <button type="submit" class="login-button">
                <span>Sign In</span>
                <span class="arrow">↗</span>
            </button>
        </form>

        <p class="auth-foot">New here? <a href="register.php">Create an account</a></p>

        <div class="bottom-line">
            <span></span>
            <div>SECURE ACCESS</div>
            <span></span>
        </div>
    </main>
</body>
</html>
