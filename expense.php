<?php
require 'includes/config.php';

if($_POST){
    if($_POST['action'] == 'delete'){
        q('DELETE FROM expenses WHERE id=? AND user_id=?', [$_POST['id'], $uid]);
        flash('Expense deleted.');
    }else{
        $name = trim($_POST['expense_name']);
        $amount = $_POST['amount'];
        $category = $_POST['category_id'];
        $date = $_POST['expense_date'];
        $note = $_POST['note'];

        if($name == ''){
            flash('Expense name is required.', 'error');
        }elseif(!valid_amount($amount)){
            flash('Amount must be more than 0.', 'error');
        }elseif(!valid_date($date)){
            flash('Please enter a valid date.', 'error');
        }elseif(!val('SELECT COUNT(*) FROM categories WHERE id=? AND user_id=?', [$category, $uid])){
            flash('Please choose a valid category.', 'error');
        }elseif($_POST['id'] > 0){
            q('UPDATE expenses SET expense_name=?, amount=?, category_id=?, expense_date=?, note=? WHERE id=? AND user_id=?',
                [$name, $amount, $category, $date, $note, $_POST['id'], $uid]);
            flash('Expense updated.');
        }else{
            q('INSERT INTO expenses (user_id, category_id, expense_name, amount, expense_date, note) VALUES (?,?,?,?,?,?)',
                [$uid, $category, $name, $amount, $date, $note]);
            flash('Expense added.');
        }
    }
    back();
}

include 'includes/header.php';

$search = $_GET['q'] ?? '';
$cat = $_GET['cat'] ?? '';
$cats = rows('SELECT id, name FROM categories WHERE user_id=? ORDER BY name', [$uid]);

$list = rows('SELECT e.*, c.name AS cat FROM expenses e
    JOIN categories c ON c.id = e.category_id
    WHERE e.user_id=? AND e.expense_date LIKE ?
    AND (? = "" OR e.category_id = ?)
    AND (e.expense_name LIKE ? OR e.note LIKE ?)
    ORDER BY e.expense_date DESC, e.id DESC', [$uid, $like, $cat, $cat, "%$search%", "%$search%"]);

$sum = 0;
foreach($list as $r){
    $sum += $r['amount'];
}
?>

<div class="page-head">
    <div><h2>Expenses</h2><p class="muted">Every rupee you spend, in one place.</p></div>
    <button class="btn btn-primary" data-modal="expenseModal">+ Add Expense</button>
</div>

<form class="card filters">
    <input type="search" name="q" placeholder="Search expenses..." value="<?= e($search) ?>">
    <select name="cat" data-autosubmit>
        <option value="">All categories</option>
        <?php foreach($cats as $c){ ?>
            <option value="<?= $c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php } ?>
    </select>
    <?= month_select(true) ?>
    <button class="btn btn-ghost">Search</button>
</form>

<div class="card">
<?php if(!$list){ ?>
    <div class="empty">
        <p><b>No expenses yet.</b></p>
        <p class="muted">Start tracking your spending today.</p>
        <button class="btn btn-primary" data-modal="expenseModal">+ Add Expense</button>
    </div>
<?php }else{ ?>
    <p class="muted"><?= count($list) ?> expense(s) | Total <b><?= money($sum) ?></b></p>
    <div class="table-wrap"><table class="table">
        <thead><tr><th>Date</th><th>Expense</th><th>Category</th><th class="num">Amount</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($list as $r){ ?>
            <tr>
                <td data-label="Date"><?= date('M d, Y', strtotime($r['expense_date'])) ?></td>
                <td data-label="Expense"><?= e($r['expense_name']) ?><br><small class="muted"><?= e($r['note']) ?></small></td>
                <td data-label="Category"><span class="tag"><?= e($r['cat']) ?></span></td>
                <td data-label="Amount" class="num"><?= money($r['amount']) ?></td>
                <td data-label="Actions" class="actions"><?= actions('expenseModal', $r, 'Delete this expense?') ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table></div>
<?php } ?>
</div>

<?php
modal('expenseModal', 'Expense', "
    <label>Expense Name<input name='expense_name' required></label>
    <div class='row2'>
        <label>Amount (Rs.)<input type='number' step='0.01' name='amount' required></label>
        <label>Date<input type='date' name='expense_date' value='" . date('Y-m-d') . "' required></label>
    </div>
    <label>Category<select name='category_id'>" . options($cats) . "</select></label>
    <label>Note (optional)<input name='note'></label>");

include 'includes/footer.php';
?>