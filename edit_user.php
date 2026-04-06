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
$result = $conn->query("SELECT * FROM users WHERE id=$id");
$user = $result->fetch_assoc();

if(isset($_POST['update'])){
    $role = $_POST['role'];

    $conn->query("UPDATE users SET role='$role' WHERE id=$id");
    header("Location: manage_users.php");
}
?>

<h2>Edit User Role</h2>

<form method="POST">
<p><?php echo $user['firstName'].' '.$user['lastName']; ?></p>

<select name="role">
    <option value="admin">Admin</option>
    <option value="pharmacist">Pharmacist</option>
    <option value="cashier">Cashier</option>
</select>

<button name="update">Update</button>
</form>
