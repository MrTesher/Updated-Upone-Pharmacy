<?php
include 'db.php';
session_start();

// User must be logged in
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'] ?? 'user';

// Handle form submission (placing order)
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['medicine_id'], $_POST['quantity'])){
    $medicine_id = intval($_POST['medicine_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['id'] ?? 0;

    // Insert order into database
    $stmt = $conn->prepare("INSERT INTO orders (user_id, medicine_id, quantity, status, order_date) VALUES (?, ?, ?, 'Imewekwa', NOW())");
    $stmt->bind_param("iii", $user_id, $medicine_id, $quantity);
    $stmt->execute();
    $stmt->close();

    $message = "Oda imewekwa kikamilifu!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sokoni - UPONE PHARMACY</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; padding:20px; margin:0; }
        header { display:flex; align-items:center; justify-content:space-between; background:#2c7be5; padding:15px 20px; color:white; }
        header h1 { margin:0; font-size:24px; }
        nav a { color:white; text-decoration:none; margin-left:15px; padding:6px 10px; background:#34495e; border-radius:5px; }
        nav a:hover { background:#2c3e50; }

        h2 { text-align:center; margin:20px 0; color:#2c3e50; }

        .container { max-width:1200px; margin:0 auto; }

        table { width:100%; border-collapse: collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.08); }
        th, td { padding:12px; text-align:left; border-bottom:1px solid #ddd; }
        th { background:#3498db; color:white; }
        tr:hover { background:#f2f2f2; }

        .btn { padding:6px 10px; border:none; border-radius:5px; cursor:pointer; color:white; }
        .order-btn { background:#27ae60; }
        .order-btn:hover { background:#1e8449; }

        .message { text-align:center; margin:15px 0; color:green; font-weight:bold; }
    </style>
</head>
<body>

<header>
    <h1>Sokoni - UPONE PHARMACY</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="sokoni.php">Sokoni</a>
        <a href="logout.php">Toka</a>
    </nav>
</header>

<div class="container">

    <?php if(isset($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <h2>Oda Dawa Yako Hapa</h2>

    <table>
        <thead>
            <tr>
                <th>Jina la Dawa</th>
                <th>Kategoria</th>
                <th>Bei (TZS)</th>
                <th>Oda</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $medicines = $conn->query("SELECT * FROM medicines");
            while($row = $medicines->fetch_assoc()){
                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['category']}</td>
                        <td>{$row['price']}</td>
                        <td>
                            <form method='post' style='display:flex; gap:5px;'>
                                <input type='hidden' name='medicine_id' value='{$row['id']}'>
                                <input type='number' name='quantity' value='1' min='1' style='width:60px;'>
                                <button type='submit' class='btn order-btn'>Weka Oda</button>
                            </form>
                        </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>

</div>

</body>
</html>