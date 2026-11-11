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

if(!$uid){
    header('Location: login.php');
    exit;
}