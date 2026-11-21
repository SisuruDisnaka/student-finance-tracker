<?php

session_start();

$settings = require __DIR__ . '/settings.php';

$db = new mysqli(
    $settings['host'],
    $settings['username'],
    $settings['password'],
    $settings['database']
);

$db-> set_charset('utf8mb4');

$uid = $_SESSION['uid'] ?? null;

if(!$uid && !defined('PUBLIC_PAGE')){
    header('Location: login.php');
    exit;
}

// Month filter
$month = $_GET['m'] ?? date('Y-m');

if ($month !== 'all' && !preg_match('/^\d{4}-\d{2}$/', $month)) {
    $month = date('Y-m');
}

$like = ($month === 'all') ? '%' : $month . '%';

function q($sql, $params = []) {
    global $db;

    $stmt = $db->prepare($sql);

    if ($params) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    $stmt->execute();
    return $stmt;
}

function rows($sql, $params = []) {
    return q($sql, $params)->get_result()->fetch_all(MYSQLI_ASSOC);
}

function val($sql, $params = []) {
    return q($sql, $params)->get_result()->fetch_row()[0] ?? 0;
}

function total($table, $column) {
    global $uid, $like;

    return val(
        "SELECT SUM(amount) FROM $table WHERE user_id=? AND $column LIKE ?",
        [$uid, $like]
    );
}

function back() {
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

function flash($message, $type = 'success') {
    $_SESSION['flash'] = [$type, $message];
}

function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES);
}


function show_flash() {
    if (!isset($_SESSION['flash'])) {
        return '';
    }

    [$type, $message] = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return "<div class='alert $type'>" . e($message) . "</div>";
}
// Validation
function valid_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);

    return $d && $d->format('Y-m-d') === $date;
}

function valid_amount($amount) {
    return is_numeric($amount) && $amount > 0 && $amount < 10000000000;
}

// Formatting
function money($n) {
    $n = (float) $n;

    return 'Rs. ' . number_format(
        $n,
        ($n == floor($n)) ? 0 : 2
    );
}
function bar($pct, $class = 'ok') {
    $pct = min($pct, 100);

    return "<div class='bar'>
        <div class='bar-fill $class' style='width:$pct%'></div>
    </div>";
}

// Statistics card
function stat_card($letter, $color, $label, $value) {
    $icons = [
        'I' => 'bi-arrow-down-left',
        'E' => 'bi-arrow-up-right',
        'N' => 'bi-wallet2',
        'L' => 'bi-wallet2',
        'S' => 'bi-piggy-bank'
    ];

    $icon = isset($icons[$letter])
        ? "<i class='bi {$icons[$letter]}'></i>"
        : $letter;

    return "<div class='card stat'>
        <span class='stat-icon $color'>$icon</span>
        <div><small>$label</small><h3>$value</h3></div>
    </div>";
}

// Chart row
function chart_row($label, $pct, $class, $text) {
    $pct = min($pct, 100);

    return "<div class='chart-row'>
        <span>" . e($label) . "</span>
        <div class='bar tall'>
            <div class='bar-fill $class' style='width:$pct%'></div>
        </div>
        <b>$text</b>
    </div>";
}


// Dropdown options
function options($list) {
    $html = '';

    foreach ($list as $r) {
        $html .= "<option value='{$r['id']}'>" .
            e($r['name']) . "</option>";
    }

    return $html;
}

// Month dropdown
function month_select($all = false) {
    global $month;

    $html = "<select name='m' data-autosubmit>";

    if ($all) {
        $selected = ($month === 'all') ? 'selected' : '';
        $html .= "<option value='all' $selected>All months</option>";
    }

    for ($i = -1; $i < 12; $i++) {
        $value = date('Y-m', mktime(0, 0, 0, date('n') - $i, 1));
        $selected = ($value === $month) ? 'selected' : '';

        $html .= "<option value='$value' $selected>" .
            date('F Y', strtotime($value . '-01')) .
            "</option>";
    }

    return $html . "</select>";
}
// Modal form
function modal($id, $title, $body, $action = 'save') {
    echo "<div class='modal' id='$id' data-title='$title'>
        <div class='modal-box'>
            <h3 class='modal-title'>$title</h3>
            <form method='post'>
                <input type='hidden' name='action' value='$action'>
                <input type='hidden' name='id' value='0'>
                $body
                <div class='modal-actions'>
                    <button type='button' class='btn btn-ghost' data-close>
                        Cancel
                    </button>
                    <button class='btn btn-primary'>Save</button>
                </div>
            </form>
        </div>
    </div>";
}
// Edit and delete buttons
function actions($modal, $r, $ask = 'Delete this item?') {
    return "<button class='btn btn-sm btn-ghost'
        data-modal='$modal'
        data-fill=\"" . e(json_encode($r)) . "\">Edit</button>

        <form method='post' data-confirm='$ask'>
            <input type='hidden' name='action' value='delete'>
            <input type='hidden' name='id' value='{$r['id']}'>
            <button class='btn btn-sm btn-danger'>Delete</button>
        </form>";
}
// Categories and spending
function cats($like) {
    global $uid;

    return rows(
        'SELECT c.*, COALESCE(SUM(e.amount), 0) AS spent
         FROM categories c
         LEFT JOIN expenses e
            ON e.category_id = c.id AND e.expense_date LIKE ?
         WHERE c.user_id = ?
         GROUP BY c.id
         ORDER BY c.name',
        [$like, $uid]
    );
}

// Budget card
function budget_card($c, $extra = '') {
    $budget = $c['budget_amount'];
    $spent = $c['spent'];

    $pct = $budget > 0 ? round($spent / $budget * 100, 1) : 0;

    $class = ($budget > 0 && $spent > $budget)
        ? 'danger'
        : ($pct >= 80 ? 'warn' : 'ok');

    $remaining = ($class === 'danger')
        ? 'Over Budget'
        : money($budget - $spent);

    return "<div class='card budget-card $class'>
        <div class='card-head'><h3>" . e($c['name']) . "</h3>$extra</div>
        <div class='kv'><span>Budget</span><b>" . money($budget) . "</b></div>
        <div class='kv'><span>Spent</span><b>" . money($spent) . "</b></div>
        <div class='kv'><span>Remaining</span><b>$remaining</b></div>
        " . bar($pct, $class) . "
        <small class='muted'>$pct% Used</small>
    </div>";
}
// Notifications
function add_notification($key, $title, $message, $type) {
    global $uid;

    q(
        'INSERT IGNORE INTO notifications
         (user_id, title, message, type, ref_key)
         VALUES (?, ?, ?, ?, ?)',
        [$uid, $title, $message, $type, $key]
    );
}

function notify($m) {
    global $uid;

    // Budget alerts
    foreach (cats($m . '%') as $c) {
        $budget = $c['budget_amount'];

        if ($budget <= 0) {
            continue;
        }

        $pct = $c['spent'] / $budget * 100;

        if ($pct > 100) {
            add_notification('over' . $c['id'] . $m, 'Budget exceeded',
                'Your ' . $c['name'] . ' budget has been exceeded.', 'danger');
        } elseif ($pct >= 80) {
            add_notification('warn' . $c['id'] . $m, 'Budget warning',
                $c['name'] . ' budget is ' . floor($pct) . '% used.', 'warning');
        }
    }

    // Saving plan alerts
    foreach (rows('SELECT * FROM saving_plans WHERE user_id=?', [$uid]) as $p) {
        if ($p['target_amount'] <= 0) {
            continue;
        }

        $pct = $p['current_amount'] / $p['target_amount'] * 100;
        $step = floor($pct / 25) * 25;

        if ($pct >= 100) {
            add_notification('done' . $p['id'], 'Goal reached',
                'Congratulations! You reached your ' . $p['name'] . ' saving goal.', 'success');
        } elseif ($step > 0) {
            add_notification('save' . $p['id'] . '_' . $step, 'Saving progress',
                'You are ' . $step . '% closer to your ' . $p['name'] . ' goal.', 'info');
        }
    }

    // Monthly reminder
    if (val('SELECT COUNT(*) FROM recurring_items WHERE user_id=? AND active=1', [$uid])) {
        add_notification('cycle' . date('Y-m'), 'Monthly reminder',
            'Your monthly financial cycle is ready to review.', 'info');
    }
}