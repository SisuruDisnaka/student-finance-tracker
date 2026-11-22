<?php
require 'includes/config.php';
include 'includes/header.php';

$income = total('incomes', 'income_date');
$spent = total('expenses', 'expense_date');
$savings = val('SELECT SUM(current_amount) FROM saving_plans WHERE user_id=?', [$uid]);
$cats = cats($like);

// money added to saving plans in this month
$savedThisMonth = val('SELECT SUM(t.amount) FROM saving_transactions t
    JOIN saving_plans p ON p.id = t.saving_plan_id
    WHERE p.user_id=? AND t.transaction_date LIKE ?', [$uid, $like]);

// highest expense category and the biggest values (to size the bars)
$topName = '';
$topSpent = 0;
foreach($cats as $c){
    if($c['spent'] > $topSpent){
        $topSpent = $c['spent'];
        $topName = $c['name'];
    }
}
$maxIncomeExpense = max($income, $spent, 1);
$maxCategory = max($topSpent, 1);
?>

<div class="page-head">
    <div><h2>Reports</h2><p class="muted">Financial overview for <?= date('F Y', strtotime($month . '-01')) ?></p></div>
    <form class="inline-form"><?= month_select() ?></form>
</div>

<section class="grid cols-4">
    <?= stat_card('I', 'blue', 'Total Income', money($income)) ?>
    <?= stat_card('E', 'red', 'Total Expenses', money($spent)) ?>
    <?= stat_card('N', 'green', 'Net Balance', money($income - $spent)) ?>
    <?= stat_card('S', 'amber', 'Total Savings', money($savings) . "<br><small class='muted'>+ " . money($savedThisMonth) . " this month</small>") ?>
</section>

<section class="grid cols-2 gap-top">
    <div class="card">
        <h2>Income vs Expenses</h2>
        <?= chart_row('Income', $income / $maxIncomeExpense * 100, 'success', money($income)) ?>
        <?= chart_row('Expenses', $spent / $maxIncomeExpense * 100, 'danger', money($spent)) ?>
        <p class="muted top-gap">
            <?php if($topSpent > 0){ ?>
                Highest expense category: <b><?= e($topName) ?></b> (<?= money($topSpent) ?>)
            <?php }else{ ?>
                No expenses this month.
            <?php } ?>
        </p>
    </div>

    <div class="card">
        <h2>Category Spending</h2>
        <?php foreach($cats as $c){
            if($c['spent'] > 0){
                echo chart_row($c['name'], $c['spent'] / $maxCategory * 100, 'ok', money($c['spent']));
            }
        } ?>
    </div>
</section>

<div class="card gap-top">
    <h2>Budget Usage</h2>
    <?php foreach($cats as $c){
        if($c['budget_amount'] > 0){
            $pct = round($c['spent'] / $c['budget_amount'] * 100, 1);
            $class = 'ok';
            if($pct >= 80){
                $class = 'warn';
            }
            if($pct > 100){
                $class = 'danger';
            }
            echo chart_row($c['name'], $pct, $class, $pct . '%');
        }
    } ?>
</div>

<?php include 'includes/footer.php'; ?>