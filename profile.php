<?php
require 'includes/config.php';

if($_POST){
    if($_POST['action'] == 'name'){
        if(trim($_POST['name']) == ''){
            flash('Name cannot be empty.', 'error');
        }else{
            q('UPDATE users SET name=? WHERE id=?', [trim($_POST['name']), $uid]);
            flash('Name updated.');
        }
    }else{
        $oldHash = val('SELECT password FROM users WHERE id=?', [$uid]);

        if(!password_verify($_POST['current'], $oldHash)){
            flash('Current password is incorrect.', 'error');
        }elseif(strlen($_POST['new']) < 6){
            flash('New password must be at least 6 characters.', 'error');
        }elseif($_POST['new'] != $_POST['confirm']){
            flash('New passwords do not match.', 'error');
        }else{
            q('UPDATE users SET password=? WHERE id=?', [password_hash($_POST['new'], PASSWORD_DEFAULT), $uid]);
            flash('Password changed.');
        }
    }
    back();
}


include 'includes/header.php';

$user = rows('SELECT * FROM users WHERE id=?', [$uid])[0];
?>

<div class="page-head">
    <div><h2>Profile</h2><p class="muted">Manage your account.</p></div>
</div>

<section class="grid cols-2">
    <div class="card">
        <div class="profile-top">
            <span class="avatar big"><?= e(strtoupper($user['name'][0])) ?></span>
            <div>
                <h3><?= e($user['name']) ?></h3>
                <p class="muted"><?= e($user['email']) ?></p>
                <small class="muted">Member since <?= date('M Y', strtotime($user['created_at'])) ?></small>
            </div>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="name">
            <label>Full Name<input name="name" value="<?= e($user['name']) ?>" required></label>
            <button class="btn btn-primary">Save Name</button>
        </form>
    </div>

    <div class="card">
        <h2>Change Password</h2>
        <form method="post">
            <input type="hidden" name="action" value="password">
            <label>Current Password<input type="password" name="current" required></label>
            <label>New Password<input type="password" name="new" required></label>
            <label>Confirm New Password<input type="password" name="confirm" required></label>
            <button class="btn btn-primary">Update Password</button>
        </form>
    </div>
</section>

</div>

</body>