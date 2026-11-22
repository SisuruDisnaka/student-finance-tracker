<?php
require 'includes/config.php';

if($_POST){
    $action = $_POST['action'];

    if($action == 'delete'){
        q('DELETE FROM recurring_items WHERE id=? AND user_id=?', [$_POST['id'], $uid]);
        flash('Recurring item deleted.');

    }elseif($action == 'toggle'){
        q('UPDATE recurring_items SET active = 1 - active WHERE id=? AND user_id=?', [$_POST['id'], $uid]);

    }elseif($action == 'apply'){
        // Copy the active recurring items into the selected month.
        // The note [recurring:ID] makes sure an item is never added twice.
        $mark = "CONCAT('[recurring:', r.id, ']')";

        q("INSERT INTO incomes (user_id, source, amount, income_date, note)
            SELECT user_id, name, amount, ?, $mark FROM recurring_items r
            WHERE user_id=? AND type='income' AND active=1
            AND NOT EXISTS (SELECT 1 FROM incomes x WHERE x.user_id=r.user_id AND x.note=$mark AND x.income_date LIKE ?)",
            [$month . '-01', $uid, $month . '%']);

        q("INSERT INTO expenses (user_id, category_id, expense_name, amount, expense_date, note)
            SELECT user_id, category_id, name, amount, ?, $mark FROM recurring_items r
            WHERE user_id=? AND type='expense' AND active=1 AND category_id IS NOT NULL
            AND NOT EXISTS (SELECT 1 FROM expenses x WHERE x.user_id=r.user_id AND x.note=$mark AND x.expense_date LIKE ?)",
            [$month . '-01', $uid, $month . '%']);

        flash('Planned items added to the selected month.');

    }else{
        $type = $_POST['type'];
        $name = trim($_POST['name']);
        $amount = $_POST['amount'];
        $category = $_POST['category_id'] ?: null;
        if($type == 'income'){
            $category = null;
        }

        if($name == ''){
            flash('Name is required.', 'error');
        }elseif(!valid_amount($amount)){
            flash('Amount must be more than 0.', 'error');
        }elseif($type != 'income' && $type != 'expense'){
            flash('Please choose income or expense.', 'error');
        }elseif($type == 'expense' && !val('SELECT COUNT(*) FROM categories WHERE id=? AND user_id=?', [$category, $uid])){
            flash('Please choose a category for an expense.', 'error');
        }elseif($_POST['id'] > 0){
            q('UPDATE recurring_items SET type=?, name=?, amount=?, category_id=? WHERE id=? AND user_id=?',
                [$type, $name, $amount, $category, $_POST['id'], $uid]);
            flash('Recurring item updated.');
        }else{
            q('INSERT INTO recurring_items (user_id, type, name, amount, category_id) VALUES (?,?,?,?,?)',
                [$uid, $type, $name, $amount, $category]);
            flash('Recurring item added.');
        }
    }
    back();
}

include 'includes/header.php';

$cats = rows('SELECT id, name FROM categories WHERE user_id=? ORDER BY name', [$uid]);

// added = how many times the item was already copied into this month
$items = rows("SELECT r.*, c.name AS cat,
    (SELECT COUNT(*) FROM incomes x WHERE x.user_id=r.user_id AND x.note=CONCAT('[recurring:', r.id, ']') AND x.income_date LIKE ?) +
    (SELECT COUNT(*) FROM expenses x WHERE x.user_id=r.user_id AND x.note=CONCAT('[recurring:', r.id, ']') AND x.expense_date LIKE ?) AS added
    FROM recurring_items r
    LEFT JOIN categories c ON c.id = r.category_id
    WHERE r.user_id=? ORDER BY r.name", [$month . '%', $month . '%', $uid]);

$in = val("SELECT SUM(amount) FROM recurring_items WHERE user_id=? AND active=1 AND type='income'", [$uid]);
$out = val("SELECT SUM(amount) FROM recurring_items WHERE user_id=? AND active=1 AND type='expense'", [$uid]);
$label = date('F Y', strtotime($month . '-01'));
?>

<div class="page-head">
    <div><h2>Monthly Setup</h2><p class="muted">Plan your recurring income and expenses, then add them to a month in one click.</p></div>
    <form class="inline-form"><?= month_select() ?></form>
</div>

<section class="grid cols-3">
    <?= stat_card('I', 'blue', 'Planned Income', money($in)) ?>
    <?= stat_card('E', 'red', 'Recurring Expenses', money($out)) ?>
    <?= stat_card('L', 'green', 'Expected Left', money($in - $out)) ?>
</section>

<div class="card gap-top apply-box">
    <div><b>Start of <?= $label ?>?</b><p class="muted">Review the items below, then add all active ones to this month.</p></div>
    <form method="post">
        <input type="hidden" name="action" value="apply">
        <button class="btn btn-primary">Add planned items to <?= $label ?></button>
    </form>
</div>

<?php foreach(['income', 'expense'] as $type){ ?>
<div class="card gap-top">
    <div class="section-head">
        <h2>Recurring <?= ucfirst($type) ?></h2>
        <button class="btn btn-sm btn-primary" data-modal="itemModal" data-preset='{"type":"<?= $type ?>"}'>+ Add <?= ucfirst($type) ?></button>
    </div>
    <div class="table-wrap"><table class="table">
        <thead><tr><th>Name</th><th>Category</th><th class="num">Amount</th><th>This month</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($items as $i){ ?>
            <?php if($i['type'] == $type){ ?>
            <tr class="<?= $i['active'] ? '' : 'disabled' ?>">
                <td data-label="Name"><?= e($i['name']) ?></td>
                <td data-label="Category"><?= e($i['cat'] ?? '-') ?></td>
                <td data-label="Amount" class="num"><?= money($i['amount']) ?></td>
                <td data-label="This month">
                    <?php if(!$i['active']){ ?>
                        <span class="tag">Disabled</span>
                    <?php }elseif($i['added']){ ?>
                        <span class="tag tag-success">Added</span>
                    <?php }else{ ?>
                        <span class="tag tag-warn">Planned</span>
                    <?php } ?>
                </td>
                <td data-label="Actions" class="actions">
                    <form method="post">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= $i['id'] ?>">
                        <button class="btn btn-sm btn-ghost"><?= $i['active'] ? 'Disable' : 'Enable' ?></button>
                    </form>
                    <?= actions('itemModal', $i, 'Delete this recurring item?') ?>
                </td>
            </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table></div>
</div>
<?php } ?>

<?php
modal('itemModal', 'Recurring Item', "
    <label>Type
        <select name='type'><option value='income'>Income</option><option value='expense'>Expense</option></select>
    </label>
    <label>Name<input name='name' placeholder='e.g. Rent' required></label>
    <label>Amount (Rs.)<input type='number' step='0.01' name='amount' required></label>
    <label>Category (for expenses)
        <select name='category_id'><option value=''>None</option>" . options($cats) . "</select>
    </label>");

include 'includes/footer.php';
?>