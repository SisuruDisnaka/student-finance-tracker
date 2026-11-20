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