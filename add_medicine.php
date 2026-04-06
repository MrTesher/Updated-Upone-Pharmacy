<?php
include 'db.php';
session_start();
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

if(isset($_POST['add'])){
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $expiry = date('Y-m-d', strtotime($_POST['expiry']));

    // Always insert new medicine
    $stmt = $conn->prepare("INSERT INTO medicines (name, category, price, quantity, expiry_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $category, $price, $quantity, $expiry);

    if($stmt->execute()){
        header("Location: add_medicine.php?success=1");
        exit();
    } else {
        header("Location: add_medicine.php?error=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medicine</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin:0; padding:0; }
        .top-bar { background:#2c3e50; padding:15px; text-align:right; }
        .top-bar a { color:white; text-decoration:none; font-weight:bold; }
        .container { width:400px; margin:40px auto; background:white; padding:25px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; margin-bottom:20px; }
        .message { text-align:center; color:green; margin-bottom:15px; }
        label { font-weight:bold; }
        input { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:5px; }
        button { width:100%; padding:10px; background:#27ae60; border:none; color:white; font-size:16px; border-radius:5px; cursor:pointer; margin-top:10px; }
        button:hover { background:#219150; }
    </style>
</head>
<body>

<div class="top-bar"><a href="dashboard.php">Dashboard</a></div>

<div class="container">
    <h2>Add Medicine</h2>
    <?php
    if(isset($_GET['success'])) echo "<p class='message'>Medicine added successfully!</p>";
    elseif(isset($_GET['error'])) echo "<p class='message' style='color:red;'>Error occurred!</p>";
    ?>
    <form method="POST">
        <label>Medicine Name:</label>
        <input type="text" name="name" required>
        <label>Category:</label>
        <input type="text" name="category" required>
        <label>Price:</label>
        <input type="number" name="price" required>
        <label>Quantity:</label>
        <input type="number" name="quantity" required>
        <label>Expiry Date:</label>
        <input type="date" name="expiry" required>
        <button type="submit" name="add">Add Medicine</button>
    </form>
</div>

</body>
</html>