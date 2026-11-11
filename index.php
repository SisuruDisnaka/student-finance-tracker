<?php
session_start();

if(!isset($_SESSION['uid'])){
    header("Location: login.php");
}
else{
    header("Location: dashboard.php");
}