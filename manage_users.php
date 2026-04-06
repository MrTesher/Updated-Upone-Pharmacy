<?php
session_start();
 if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
 }
include 'db.php';

// ADMIN ONLY
if($_SESSION['role'] != 'admin'){
    die("Access denied!");
}

// FETCH USERS
$result = $conn->query("SELECT * FROM users");
?>
<a href="dashboard.php">Dashboard</a>
<h2>User Management</h2>

<a href="add_user.php"> Add User</a>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()){ ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['firstName'].' '.$row['lastName']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo $row['role']; ?></td>
    <td>
        <a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a> |
        <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete user?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>
