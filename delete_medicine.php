<?php
include 'db.php';
session_start();
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM medicines WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: viewmedicine.php");
exit();