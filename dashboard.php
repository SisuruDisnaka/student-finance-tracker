<?php
require 'includes/config.php';
include 'includes/header.php';

$income = total('incomes', 'income_date');
$spent = total('expenses', 'expense_date');
$savings = val('SELECT SUM(current_amount) FROM saving_plans WHERE user_id=?', [$uid]);

$recent = rows('SELECT e.*, c.name AS cat FROM expenses e
    JOIN categories c ON c.id = e.category_id
    WHERE e.user_id=? AND e.expense_date LIKE ?
    ORDER BY e.expense_date DESC, e.id DESC LIMIT 5', [$uid, $like]);

$plans = rows('SELECT * FROM saving_plans WHERE user_id=? ORDER BY id DESC LIMIT 3', [$uid]);
?>

<div class="page-head">
    <div>
        <h2>Welcome back, <?= e(explode(' ', $me['name'])[0]) ?></h2>
        <p class="muted">Here is your money overview.</p>
    </div>
    <form class="inline-form"><?= month_select() ?></form>
</div>

<section class="grid cols-4">
    <?= stat_card('I', 'blue', 'Monthly Income', money($income)) ?>
    <?= stat_card('E', 'red', 'Monthly Expenses', money($spent)) ?>
    <?= stat_card('N', 'green', 'Net Balance', money($income - $spent)) ?>
    <?= stat_card('S', 'amber', 'Total Savings', money($savings)) ?>
</section>

<div class="section-head"><h2>Budget Overview</h2><a href="budgets.php">Manage budgets</a></div>
<section class="grid cols-3">
    <?php foreach(cats($like) as $c){ echo budget_card($c); } ?>
</section>

<section class="grid cols-2 gap-top">
    <div class="card">
        <div class="section-head"><h2>Recent Expenses</h2><a href="expenses.php">View all</a></div>
        <?php if(!$recent){ ?>
            <div class="empty">
                <p><b>No expenses yet.</b></p>
                <p class="muted">Start tracking your spending today.</p>
                <a class="btn btn-primary" href="expenses.php">+ Add Expense</a>
            </div>
        <?php }else{ ?>
            <div class="table-wrap"><table class="table">
                <thead><tr><th>Expense</th><th>Category</th><th>Date</th><th class="num">Amount</th></tr></thead>
                <tbody>
                <?php foreach($recent as $r){ ?>
                    <tr>
                        <td data-label="Expense"><?= e($r['expense_name']) ?></td>
                        <td data-label="Category"><span class="tag"><?= e($r['cat']) ?></span></td>
                        <td data-label="Date"><?= date('M d', strtotime($r['expense_date'])) ?></td>
                        <td data-label="Amount" class="num"><?= money($r['amount']) ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table></div>
        <?php } ?>
    </div>

    <div class="card">
        <div class="section-head"><h2>Saving Goals</h2><a href="saving-plans.php">View all</a></div>
        <?php if(!$plans){ ?>
            <div class="empty">
                <p><b>No saving plans yet.</b></p>
                <p class="muted">What is your next dream?</p>
                <a class="btn btn-primary" href="saving-plans.php">+ Create Saving Plan</a>
            </div>
        <?php } ?>
        <?php foreach($plans as $p){
            $pct = round($p['current_amount'] / $p['target_amount'] * 100, 1); ?>
            <div class="goal">
                <div class="kv"><b><?= e($p['name']) ?></b><span><?= $pct ?>%</span></div>
                <?= bar($pct, $pct >= 100 ? 'success' : 'ok') ?>
                <small class="muted"><?= money($p['current_amount']) ?> of <?= money($p['target_amount']) ?></small>
            </div>
        <?php } ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
