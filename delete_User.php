<?php
session_start();
 if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
 }
include 'db.php';

if($_SESSION['role'] != 'admin'){
    die("Access denied!");
}

$id = $_GET['id'];

$conn->query("DELETE FROM users WHERE id=$id");

header("Location: manage_users.php");
?>
