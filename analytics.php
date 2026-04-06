<?php
session_start();
include 'db.php';
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'] ?? '';

// STOCK REPORTS
$total_medicines = $conn->query("SELECT COUNT(*) as total FROM medicines")->fetch_assoc()['total'];
$low_stock = $conn->query("SELECT COUNT(*) as total FROM medicines WHERE quantity < 10")->fetch_assoc()['total'];
$expired_medicines = $conn->query("SELECT COUNT(*) as total FROM medicines WHERE expiry_date < CURDATE()")->fetch_assoc()['total'];
$new_medicines = $conn->query("SELECT COUNT(*) as total FROM medicines WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['total'] ?? 0;

// FINANCIAL REPORTS
$daily_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE DATE(sale_date) = CURDATE()")->fetch_assoc()['total'] ?? 0;
$weekly_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'] ?? 0;
$monthly_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0;
$quarter = ceil(date('n') / 3);
$quarter_start = date('Y-m-d', strtotime(date('Y') . '-' . (($quarter-1)*3+1) . '-01'));
$quarter_end = date('Y-m-t', strtotime(date('Y') . '-' . ($quarter*3) . '-01'));
$quarterly_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE sale_date BETWEEN '$quarter_start' AND '$quarter_end'")->fetch_assoc()['total'] ?? 0;
$yearly_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE YEAR(sale_date) = YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Analytics & Reports</title>
    <style>
body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; padding:0; }
header { width:100%; display:flex; align-items:center; background:#2c7be5; height:80px; padding:0 20px; color:white; }
header h1 { margin-left:20px; font-size:30px; }

/* NAVIGATION */
nav { width:100%; display:flex; gap:10px; background:#34495e; padding:10px 0; }
nav a { color:white; text-decoration:none; padding:10px; background:#2c3e50; border-radius:5px; }
nav a:hover { background:#1abc9c; }

/* CONTAINER */
.container { padding:20px; max-width:1200px; margin:auto; display:grid; gap:20px; }

/* CARDS */
.card { background:white; padding:20px; border-radius:12px; text-align:center; box-shadow:0 6px 15px rgba(0,0,0,0.08); }
.card h3 { color:#34495e; margin-bottom:10px; }
.card p { font-size:24px; font-weight:bold; color:#27ae60; }

/* GRID FOR REPORTS */
.report-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap:20px; }

    </style>
</head>
<body>

<header>
    <h1>Analytics & Reports</h1>
</header>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="analytics.php">Analytics & Reports</a>
</nav>

<div class="container">

    <h2>Stock Reports</h2>
    <div class="report-grid">
        <div class="card"><h3>Total Medicines</h3><p><?php echo $total_medicines; ?></p></div>
        <div class="card"><h3>Low Stock Medicines</h3><p><?php echo $low_stock; ?></p></div>
        <div class="card"><h3>Expired Medicines</h3><p><?php echo $expired_medicines; ?></p></div>
        <div class="card"><h3>New Medicines Today</h3><p><?php echo $new_medicines; ?></p></div>
    </div>

    <h2>Financial Reports</h2>
    <div class="report-grid">
        <div class="card"><h3>Daily Sales</h3><p><?php echo number_format($daily_sales); ?> TZS</p></div>
        <div class="card"><h3>Weekly Sales</h3><p><?php echo number_format($weekly_sales); ?> TZS</p></div>
        <div class="card"><h3>Monthly Sales</h3><p><?php echo number_format($monthly_sales); ?> TZS</p></div>
        <div class="card"><h3>Quarterly Sales</h3><p><?php echo number_format($quarterly_sales); ?> TZS</p></div>
        <div class="card"><h3>Yearly Sales</h3><p><?php echo number_format($yearly_sales); ?> TZS</p></div>
    </div>

</div>

</body>
</html>