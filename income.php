<?php
// Load database connection and common functions
require 'includes/config.php';

// Handle income form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    // Delete selected income
    if ($action === 'delete') {
        q('DELETE FROM incomes WHERE id=? AND user_id=?', [$id, $uid]);
        flash('Income deleted.');
    } else {
        // Get form values
        $source = trim($_POST['source'] ?? '');
        $amount = $_POST['amount'] ?? '';
        $date = $_POST['income_date'] ?? '';
        $note = trim($_POST['note'] ?? '');

        // Validate input
        if ($source === '' || strlen($source) > 100 || strlen($note) > 255) {
            flash('Please enter a valid income source and note.', 'error');
        } elseif (!valid_amount($amount)) {
            flash('Enter a valid amount greater than 0.', 'error');
        } elseif (!valid_date($date)) {
            flash('Please enter a valid date.', 'error');
        } elseif ($id > 0) {
            // Update existing income
            q('UPDATE incomes SET source=?, amount=?, income_date=?, note=? WHERE id=? AND user_id=?',
                [$source, $amount, $date, $note, $id, $uid]);
            flash('Income updated.');
        } else {
            // Insert new income
            q('INSERT INTO incomes (user_id, source, amount, income_date, note) VALUES (?,?,?,?,?)',
                [$uid, $source, $amount, $date, $note]);
            flash('Income added.');
        }
    }
    back();
}

// Load common page header
include 'includes/header.php';

// Retrieve income records using search and month filters
$search = trim($_GET['q'] ?? '');
$list = rows('SELECT * FROM incomes
    WHERE user_id=? AND income_date LIKE ? AND (source LIKE ? OR note LIKE ?)
    ORDER BY income_date DESC, id DESC',
    [$uid, $like, "%$search%", "%$search%"]);

// Get previous income sources for suggestions
$sources = rows('SELECT DISTINCT source FROM incomes WHERE user_id=?', [$uid]);
$sourceList = '';
foreach ($sources as $s) {
    $sourceList .= '<option value="' . e($s['source']) . '">';
}

// Calculate total filtered income
$sum = array_sum(array_column($list, 'amount'));
?>

<!-- Income page heading -->
<div class="page-head">
    <div><h2>Income</h2><p class="muted">Track all your income sources.</p></div>
    <button class="btn btn-primary" data-modal="incomeModal">+ Add Income</button>
</div>

<!-- Search and month filter -->
<form class="card filters" method="get">
    <input type="search" name="q" placeholder="Search income..." value="<?= e($search) ?>">
    <?= month_select(true) ?>
    <button class="btn btn-ghost">Search</button>
</form>

<!-- Income records table -->
<div class="card">
    <?php if (!$list): ?>
        <div class="empty"><p>No income records found.</p></div>
    <?php else: ?>
        <p class="muted"><?= count($list) ?> records | Total <b><?= money($sum) ?></b></p>
        <div class="table-wrap"><table class="table">
            <thead><tr><th>Date</th><th>Source</th><th class="num">Amount</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($list as $r): ?>
                <tr>
                    <td data-label="Date"><?= e($r['income_date']) ?></td>
                    <td data-label="Source"><?= e($r['source']) ?><br><small class="muted"><?= e($r['note']) ?></small></td>
                    <td data-label="Amount" class="num text-success"><?= money($r['amount']) ?></td>
                    <td data-label="Actions" class="actions"><?= actions('incomeModal', $r, 'Delete this income?') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php endif; ?>
</div>

<?php
// Reusable modal for adding and editing income
modal('incomeModal', 'Income', "
    <label>Income Source<input name='source' list='sources' maxlength='100' required>
        <datalist id='sources'>$sourceList</datalist></label>
    <div class='row2'>
        <label>Amount (Rs.)<input type='number' step='0.01' min='0.01' name='amount' required></label>
        <label>Date<input type='date' name='income_date' value='" . date('Y-m-d') . "' required></label>
    </div>
    <label>Note (optional)<input name='note' maxlength='255'></label>");

// Load common page footer
include 'includes/footer.php';
?>
