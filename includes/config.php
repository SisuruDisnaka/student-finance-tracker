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