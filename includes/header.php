<?php
notify(date('Y-m'));

$me = rows('SELECT name FROM users WHERE id=?', [$uid])[0];
$unread = val('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0', [$uid]);
$latest = rows('SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT 5', [$uid]);

$menu = [
    'dashboard.php' => 'Dashboard',
    'expense.php' => 'Expenses',
    'income.php' => 'Income',
    'budgets.php' => 'Budgets',
    'saving-plans.php' => 'Saving Plans',
    'monthly-setup.php' => 'Monthly Setup',
    'notifications.php' => 'Notifications',
    'reports.php' => 'Reports',
    'profile.php' => 'Profile',
    'logout.php' => 'Logout'
];
$icons = [
    'dashboard.php' => 'bi-grid-1x2',
    'expense.php' => 'bi-arrow-up-right',
    'income.php' => 'bi-arrow-down-left',
    'budgets.php' => 'bi-pie-chart',
    'saving-plans.php' => 'bi-piggy-bank',
    'monthly-setup.php' => 'bi-calendar-event',
    'notifications.php' => 'bi-bell',
    'reports.php' => 'bi-bar-chart',
    'profile.php' => 'bi-person',
    'logout.php' => 'bi-box-arrow-right'
];
$page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#050d1d">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $menu[$page] ?? "StudentFinance" ?> | Student Expense Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="glow glow-one"></div>
<div class="glow glow-two"></div>

<aside class="sidebar" id="sidebar">
    <div class="brand">
        <span>Student<b>Finance</b></span>
    </div>
    <nav>
        <?php foreach($menu as $file => $label){ ?>
            <a href="<?= $file ?>" class="<?= $page == $file ? 'active' : '' ?> <?= $file == 'logout.php' ? 'logout' : '' ?>"><i class="bi <?= $icons[$file] ?>"></i><?= $label ?></a>
        <?php } ?>
    </nav>
</aside>

<div class="main">
<header class="topbar">
    <button class="menu-btn" id="menuBtn" aria-label="Open menu"><i class="bi bi-list"></i></button>
    <h1 class="page-title"><?= $menu[$page] ?? "" ?></h1>

    <div class="topbar-right">
        <div class="notif-wrap">
            <button class="icon-btn" id="notifBtn" aria-label="Notifications"><i class="bi <?= $icons['notifications.php'] ?>"></i><?php if($unread){ ?><span class="badge"><?= $unread ?></span><?php } ?></button>
            <div class="dropdown" id="notifDrop">
                <div class="dropdown-title">Notifications</div>
                <?php foreach($latest as $n){ ?>
                    <div class="dropdown-item <?= $n['is_read'] ? '' : 'unread' ?>">
                        <?= e($n['message']) ?>
                        <small><?= date('M d, H:i', strtotime($n['created_at'])) ?></small>
                    </div>
                <?php } ?>
                <?php if(!$latest){ ?>
                    <div class="dropdown-item muted">No notifications yet.</div>
                <?php } ?>
                <a class="dropdown-link" href="notifications.php">View all</a>
            </div>
        </div>
        <a href="profile.php" class="avatar"><?= e(strtoupper($me['name'][0])) ?></a>
    </div>
</header>

<main class="content">
<?= show_flash() ?>
