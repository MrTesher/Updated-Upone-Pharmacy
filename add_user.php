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

if(isset($_POST['add'])){

    $fname = $_POST['fName'];
    $lname = $_POST['lName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $conn->query("INSERT INTO users(firstName,lastName,email,password,role)
                  VALUES ('$fname','$lname','$email','$password','$role')");

    header("Location: manage_users.php");
}
?>

<h2>Add User</h2>

<form method="POST">
<input type="text" name="fName" placeholder="First Name" required><br><br>
<input type="text" name="lName" placeholder="Last Name" required><br><br>
<input type="email" name="email" placeholder="Email" required><br><br>
<input type="password" name="password" placeholder="Password" required><br><br>

<select name="role" required>
    <option value="admin">Admin</option>
    <option value="pharmacist">Pharmacist</option>
    <option value="cashier">Cashier</option>
</select><br><br>

<button name="add">Add User</button>
</form>
