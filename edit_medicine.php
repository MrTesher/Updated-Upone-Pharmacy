<?php
include 'db.php';
session_start();
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

if(!isset($_GET['id'])) die("Medicine ID missing.");

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM medicines WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$medicine = $result->fetch_assoc();
if(!$medicine) die("Medicine not found.");

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $expiry = date('Y-m-d', strtotime($_POST['expiry']));

    $stmt = $conn->prepare("UPDATE medicines SET name=?, category=?, price=?, quantity=?, expiry_date=? WHERE id=?");
    $stmt->bind_param("ssdisi", $name, $category, $price, $quantity, $expiry, $id);
    $stmt->execute();
    header("Location: viewmedicine.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Medicine</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin:0; padding:0; }
        .top-bar { background:#2c3e50; padding:15px; text-align:right; }
        .top-bar a { color:white; text-decoration:none; font-weight:bold; }
        .container { width:400px; margin:40px auto; background:white; padding:25px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; margin-bottom:20px; }
        label { font-weight:bold; }
        input { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:5px; }
        button { width:100%; padding:10px; background:#3498db; border:none; color:white; font-size:16px; border-radius:5px; cursor:pointer; margin-top:10px; }
        button:hover { background:#2980b9; }
    </style>
</head>
<body>

<div class="top-bar"><a href="viewmedicine.php">Back</a></div>

<div class="container">
    <h2>Edit Medicine</h2>
    <form method="POST">
        <label>Medicine Name:</label>
        <input type="text" name="name" value="<?php echo $medicine['name']; ?>" required>
        <label>Category:</label>
        <input type="text" name="category" value="<?php echo $medicine['category']; ?>" required>
        <label>Price:</label>
        <input type="number" name="price" value="<?php echo $medicine['price']; ?>" required>
        <label>Quantity:</label>
        <input type="number" name="quantity" value="<?php echo $medicine['quantity']; ?>" required>
        <label>Expiry Date:</label>
        <input type="date" name="expiry" value="<?php echo $medicine['expiry_date']; ?>" required>
        <button type="submit" name="update">Update Medicine</button>
    </form>
</div>

</body>
</html>