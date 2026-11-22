<?php
require 'includes/config.php';

if($_POST){
    $action = $_POST['action'];

    if($action == 'delete'){
        // the saving history is deleted too
        q('DELETE FROM saving_plans WHERE id=? AND user_id=?', [$_POST['id'], $uid]);
        flash('Saving plan deleted.');

    }else{
        // create or edit a plan
        $name = trim($_POST['name']);
        $description = $_POST['description'];
        $target = $_POST['target_amount'];
        $date = $_POST['target_date'] ?: null;
        $initial = max(0, (float)$_POST['initial_amount']);

        if($name == ''){
            flash('Plan name is required.', 'error');
        }elseif(!valid_amount($target)){
            flash('Target amount must be more than 0.', 'error');
        }elseif($date && !valid_date($date)){
            flash('Please enter a valid date.', 'error');
        }elseif($_POST['id'] > 0){
            q('UPDATE saving_plans SET name=?, description=?, target_amount=?, target_date=? WHERE id=? AND user_id=?',
                [$name, $description, $target, $date, $_POST['id'], $uid]);
            flash('Saving plan updated.');
        }else{
            q('INSERT INTO saving_plans (user_id, name, description, target_amount, current_amount, target_date) VALUES (?,?,?,?,?,?)',
                [$uid, $name, $description, $target, $initial, $date]);
            $planId = $db->insert_id;
            if($initial > 0){
                q("INSERT INTO saving_transactions (saving_plan_id, amount, transaction_date, note) VALUES (?, ?, CURDATE(), 'Initial amount')",
                    [$planId, $initial]);
            }
            flash('Saving plan created.');
        }
    }
    back();
}

include 'includes/header.php';

$plans = rows('SELECT * FROM saving_plans WHERE user_id=? ORDER BY id DESC', [$uid]);
?>

<div class="page-head">
    <div><h2>Saving Plans</h2><p class="muted">Turn your dreams into goals.</p></div>
    <button class="btn btn-primary" data-modal="planModal">+ Create Saving Plan</button>
</div>

<?php if(!$plans){ ?>
    <div class="card empty">
        <p><b>No saving plans yet.</b></p>
        <p class="muted">What is your next dream?</p>
        <button class="btn btn-primary" data-modal="planModal">+ Create Saving Plan</button>
    </div>
<?php } ?>

<section class="grid cols-3">
<?php foreach($plans as $p){
    $pct = round($p['current_amount'] / $p['target_amount'] * 100, 1);
    $done = $pct >= 100;
    $remaining = max(0, $p['target_amount'] - $p['current_amount']); ?>
    <div class="card plan">
        <div class="card-head">
            <h3><?= e($p['name']) ?></h3>
            <span class="tag <?= $done ? 'tag-success' : '' ?>"><?= $done ? 'Completed' : 'Active' ?></span>
        </div>
        <p class="muted"><?= e($p['description']) ?>&nbsp;</p>
        <div class="kv"><span>Target</span><b><?= money($p['target_amount']) ?></b></div>
        <div class="kv"><span>Current</span><b><?= money($p['current_amount']) ?></b></div>
        <div class="kv"><span>Remaining</span><b><?= money($remaining) ?></b></div>
        <?= bar($pct, $done ? 'success' : 'ok') ?>
        <div class="kv">
            <small class="muted"><?= $pct ?>% saved</small>
            <small class="muted"><?= $p['target_date'] ? 'Target: ' . date('M d, Y', strtotime($p['target_date'])) : '' ?></small>
        </div>
        <div class="actions top-gap">
            <?= actions('planModal', $p, 'Delete this plan and its history?') ?>
        </div>
    </div>
<?php } ?>
</section>

<?php
modal('planModal', 'Saving Plan', "
    <label>Plan Name<input name='name' placeholder='e.g. New Laptop' required></label>
    <label>Description<input name='description'></label>
    <div class='row2'>
        <label>Target Amount (Rs.)<input type='number' step='0.01' name='target_amount' required></label>
        <label class='add-only'>Initial Amount (Rs.)<input type='number' step='0.01' name='initial_amount' value='0'></label>
    </div>
    <label>Target Date<input type='date' name='target_date'></label>");

include 'includes/footer.php';
?>
