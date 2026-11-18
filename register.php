<?php
define('PUBLIC_PAGE', 1);
require 'includes/config.php';

$error = '';

if($_POST){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if($name == ''){
        $error = 'Full name is required.';
    }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Please enter a valid email address.';
    }elseif(strlen($_POST['password']) < 6){
        $error = 'Password must be at least 6 characters.';
    }elseif($_POST['password'] != $_POST['confirm']){
        $error = 'Passwords do not match.';
    }elseif(val('SELECT COUNT(*) FROM users WHERE email=?', [$email])){
        $error = 'That email is already registered.';
    }else{
        q('INSERT INTO users (name, email, password) VALUES (?,?,?)', [$name, $email, password_hash($_POST['password'], PASSWORD_DEFAULT)]);
        $newUser = $db->insert_id;

        q('INSERT INTO categories (user_id, name) SELECT ?, name FROM default_categories', [$newUser]);

        header('Location: login.php?registered=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#050d1d">
    <title>Register | StudentFinance</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">

    <div class="glow glow-one"></div>
    <div class="glow glow-two"></div>

    <main class="login-card">
        <h1>Create your account<span>.</span></h1>
        <p class="subtitle">Take control of your student budget.</p>

        <?php if($error){ ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php } ?>

        <form method="post">
            <div class="input-group">
                <label for="name">Full name</label>
                <input id="name" name="name" placeholder="Your full name" autocomplete="name" required>
            </div>

            <div class="input-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" placeholder="name@example.com" autocomplete="email" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="At least 6 characters" autocomplete="new-password" required>
            </div>

            <div class="input-group">
                <label for="confirm">Confirm password</label>
                <input type="password" id="confirm" name="confirm" placeholder="Repeat your password" autocomplete="new-password" required>
            </div>

            <button type="submit" class="login-button">
                <span>Create Account</span>
                <span class="arrow">↗</span>
            </button>
        </form>

        <p class="auth-foot">Already have an account? <a href="login.php">Log in</a></p>

        <div class="bottom-line">
            <span></span>
            <div>SECURE ACCESS</div>
            <span></span>
        </div>
    </main>
</body>
</html>
