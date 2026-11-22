<?php
require 'includes/config.php';

if($_POST){
    $action = $_POST['action'];

    if($action == 'read'){
        q('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?', [$_POST['id'], $uid]);
    }elseif($action == 'readall'){
        q('UPDATE notifications SET is_read=1 WHERE user_id=?', [$uid]);
        flash('All notifications marked as read.');
    }else{
        q('DELETE FROM notifications WHERE id=? AND user_id=?', [$_POST['id'], $uid]);
    }
    back();
}

include 'includes/header.php';

$list = rows('SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC', [$uid]);
?>

<div class="page-head">
    <div><h2>Notifications</h2><p class="muted"><?= $unread ?> unread</p></div>
    <?php if($unread){ ?>
        <form method="post">
            <input type="hidden" name="action" value="readall">
            <button class="btn btn-ghost">Mark all as read</button>
        </form>
    <?php } ?>
</div>

<div class="card">
<?php if(!$list){ ?>
    <div class="empty">
        <p><b>You are all caught up!</b></p>
        <p class="muted">Budget and saving alerts will show up here.</p>
    </div>
<?php } ?>

<?php foreach($list as $n){ ?>
    <div class="notif <?= $n['is_read'] ? '' : 'unread' ?> n-<?= e($n['type']) ?>">
        <div class="notif-text">
            <b><?= e($n['title']) ?></b>
            <p><?= e($n['message']) ?></p>
            <small class="muted"><?= date('M d, Y H:i', strtotime($n['created_at'])) ?></small>
        </div>
        <div class="actions">
            <?php if(!$n['is_read']){ ?>
                <form method="post">
                    <input type="hidden" name="action" value="read">
                    <input type="hidden" name="id" value="<?= $n['id'] ?>">
                    <button class="btn btn-sm btn-ghost">Mark read</button>
                </form>
            <?php } ?>
            <form method="post">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                <button class="btn btn-sm btn-danger">Delete</button>
            </form>
        </div>
    </div>
<?php } ?>
</div>

<?php include 'includes/footer.php'; ?>
