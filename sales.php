<?php
include 'db.php';
session_start();
 if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
 }
 if($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'cashier'){
    die("Access denied!");
}

// FETCH MEDICINES
$medicines = $conn->query("SELECT * FROM medicines");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Billing System</title>

<style>
body { font-family: Arial; }

.container { display: flex; gap: 40px; }

.box {
    border: 1px solid #ccc;
    padding: 20px;
    width: 45%;
}
</style>
<a href="dashboard.php">Dashboard</a>
</head>
<body>

<h2>Billing System</h2>

<div class="container">

<!-- LEFT -->
<div class="box">
    <h3>Available Medicines</h3>

    <select id="medicineList">
        <?php while($row = $medicines->fetch_assoc()){ ?>
            <option value="<?php echo $row['id']; ?>" 
                    data-price="<?php echo $row['price']; ?>" 
                    data-stock="<?php echo $row['quantity']; ?>">
                <?php echo $row['name']; ?> (Stock: <?php echo $row['quantity']; ?>)
            </option>
        <?php } ?>
    </select><br><br>

    <label>Quantity:</label><br>
    <input type="number" id="qty"><br>

    <button onclick="addToCart()">Add to Cart</button>
</div>

<!-- RIGHT -->
<div class="box">
    <h3>Cart</h3>
    <ul id="cartList"></ul>

    <h3>Total: <span id="total">0</span></h3>

    <form method="POST">
        <input type="hidden" name="cart_data" id="cartData">
        <button type="submit" name="checkout">Checkout</button>
    </form>
</div>

</div>

<script>
let cart = [];

function addToCart() {
    let select = document.getElementById("medicineList");
    let option = select.options[select.selectedIndex];

    let id = option.value;
    let name = option.text;
    let price = option.getAttribute("data-price");
    let stock = option.getAttribute("data-stock");

    let qty = parseInt(document.getElementById("qty").value);

    if(qty > stock){
        alert("Not enough stock!");
        return;
    }

    cart.push({id, name, price, quantity: qty});
    displayCart();
}

function displayCart() {
    let list = document.getElementById("cartList");
    list.innerHTML = "";

    let total = 0;

    cart.forEach(item => {
        let li = document.createElement("li");
        li.innerText = item.name + " x " + item.quantity;
        list.appendChild(li);

        total += item.price * item.quantity;
    });

    document.getElementById("total").innerText = total;

    // send cart to PHP
    document.getElementById("cartData").value = JSON.stringify(cart);
}
</script>

</body>
</html>

<?php
// PROCESS CHECKOUT
if(isset($_POST['checkout'])){
    $cart = json_decode($_POST['cart_data'], true);

    foreach($cart as $item){
        $id = $item['id'];
        $qty = $item['quantity'];
        $total = $item['price'] * $qty;

        // INSERT SALE
        $conn->query("INSERT INTO sales (medicine_id, quantity, total_price) 
                      VALUES ('$id', '$qty', '$total')");

        // UPDATE STOCK
        $conn->query("UPDATE medicines SET quantity = quantity - $qty WHERE id = $id");
    }

    echo "<script>alert('Sale completed!'); window.location='sales.php';</script>";
}
?>
