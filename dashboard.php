<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start();
include 'db.php';

if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'] ?? '';

// Handle profile picture upload
if(isset($_POST['upload_profile'])){
    $userId = $_SESSION['id'] ?? 0;
    if($userId == 0){ die("User ID not found."); }

    $file = $_FILES['profile_pic'];
    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileError = $file['error'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];

    if(in_array($ext, $allowed)){
        if($fileError === 0){
            $newName = 'profile_'.$userId.'.'.$ext;
            $destination = 'uploads/'.$newName;
            if(!is_dir('uploads')) mkdir('uploads', 0777, true);

            if(move_uploaded_file($fileTmp, $destination)){
                $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE id=?");
                $stmt->bind_param("si", $destination, $userId);
                $stmt->execute();
                $_SESSION['profile_pic'] = $destination;
                $upload_success = "Profile picture updated!";
            } else { $upload_error = "Failed to move file."; }
        } else { $upload_error = "Error uploading file."; }
    } else { $upload_error = "Invalid file type (jpg, png, gif)."; }
}

// Dashboard statistics
$total_medicines = $conn->query("SELECT COUNT(*) as total FROM medicines")->fetch_assoc()['total'];
$sales_today = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE DATE(sale_date) = CURDATE()")->fetch_assoc()['total'] ?? 0;
$low_stock = $conn->query("SELECT COUNT(*) as total FROM medicines WHERE quantity < 10")->fetch_assoc()['total'];
$expired_medicines = $conn->query("SELECT COUNT(*) as total FROM medicines WHERE expiry_date < CURDATE()")->fetch_assoc()['total'];

$total_users = ($role=='admin') ? $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'] : null;
$active_bills = ($role=='admin' || $role=='cashier') ? $conn->query("SELECT COUNT(*) as total FROM sales WHERE DATE(sale_date)=CURDATE()")->fetch_assoc()['total'] : null;

// Analytics & Reports
// Daily sales
$daily_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE DATE(sale_date) = CURDATE()")->fetch_assoc()['total'] ?? 0;
// Weekly sales (last 7 days)
$weekly_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch_assoc()['total'] ?? 0;
// Monthly sales
$monthly_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0;
// Quarterly sales
$quarter = ceil(date('n') / 3);
$quarter_start = date('Y-m-d', strtotime(date('Y') . '-' . (($quarter-1)*3+1) . '-01'));
$quarter_end = date('Y-m-t', strtotime(date('Y') . '-' . ($quarter*3) . '-01'));
$quarterly_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE sale_date BETWEEN '$quarter_start' AND '$quarter_end'")->fetch_assoc()['total'] ?? 0;
// Yearly sales
$yearly_sales = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE YEAR(sale_date) = YEAR(CURDATE())")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: Arial, sans-serif; background: #f4f6f8; }

/* HEADER */
header { width:100%; display:flex; align-items:center; background:#2c7be5; height:100px; position:relative; }
header img.logo { height:70px; margin-left:10px; border-radius:50%; }
header h1 { position:absolute; left:50%; transform:translateX(-50%); color:white; font-size:45px; }

/* PROFILE */
header .profile { margin-left:auto; display:flex; align-items:center; gap:10px; margin-right:20px; position:relative; }
header .profile-pic { height:50px; width:50px; border-radius:50%; object-fit:cover; border:2px solid white; cursor:pointer; }
header .profile-info { display:flex; flex-direction:column; }
.profile-name { color:white; font-size:15px; font-weight:bold; }
.profile-role { background: rgba(255,255,255,0.2); padding:2px 6px; border-radius:5px; font-size:11px; width:fit-content; }

/* NAVIGATION */
nav { width:100%; display:flex; gap:10px; background:#34495e; padding:10px 0; }
nav a { color:white; text-decoration:none; padding:10px; background:#2c3e50; border-radius:5px; }

/* CONTAINER & GRID */
.container {
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

/* CARD STYLES */
.card {
    background: #ffffff;
    color: #333;
    padding: 25px 20px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
}

.card h3 { font-size: 18px; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; color: #2c3e50; }
.card p { font-size: 28px; font-weight: bold; margin: 0; }

/* ACCENT COLORS */
.card:nth-child(1) p { color: #3498db; }
.card:nth-child(2) p { color: #27ae60; }
.card:nth-child(3) p { color: #e74c3c; }
.card:nth-child(4) p { color: #f39c12; }
.card:nth-child(5) p { color: #8e44ad; }
.card:nth-child(6) p { color: #16a085; }

/* HOVER EFFECT */
.card:hover { transform: translateY(-6px); box-shadow: 0 12px 20px rgba(0,0,0,0.12); }

/* LOGOUT */
.logout-btn { background:red; color:white; padding:6px 10px; border-radius:5px; text-decoration:none; font-size:14px; }

/* PROFILE UPLOAD */
#uploadForm { display:none; position:absolute; top:60px; right:0; background:white; padding:15px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.2); z-index:100; }
#uploadForm input[type=file] { margin-bottom:10px; }
#uploadForm button { padding:8px 12px; background:#3498db; color:white; border:none; border-radius:5px; cursor:pointer; }
#uploadForm button:hover { background:#2980b9; }
#uploadForm .close { position:absolute; top:5px; right:10px; cursor:pointer; color:red; font-weight:bold; }
.success { color:green; margin-bottom:5px; }
.error { color:red; margin-bottom:5px; }

/* REPORTS SECTION */
.reports { margin-top:40px; grid-column: 1/-1; background:white; padding:20px; border-radius:15px; box-shadow:0 6px 18px rgba(0,0,0,0.08); }
.reports h2 { text-align:center; margin-bottom:20px; color:#2c3e50; }
.reports .report-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap:15px; }
.reports .report-card { background:#f9f9f9; padding:15px; border-radius:10px; text-align:center; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
.reports .report-card h4 { margin-bottom:10px; font-size:16px; color:#34495e; }
.reports .report-card p { font-size:22px; font-weight:bold; color:#27ae60; }
    </style>
</head>
<body>

<header>
    <img src="Upone_logo.jpg" class="logo">
    <h1>UPONE PHARMACY</h1>

    <div class="profile">
        <img src="<?php echo $_SESSION['profile_pic'] ?? 'profile_avatar.jpg'; ?>" class="profile-pic" id="profilePic">

        <div class="profile-info">
            <span class="profile-name"><?php echo $_SESSION['firstName']." ".$_SESSION['lastName']; ?></span>
            <span class="profile-role">
                <?php
                if($role=='admin') echo "Administrator";
                elseif($role=='pharmacist') echo "Pharmacist";
                elseif($role=='cashier') echo "Cashier";
                else echo "User";
                ?>
            </span>
        </div>

        <a href="logout.php" class="logout-btn">Logout</a>

        <div id="uploadForm">
            <span class="close" id="closeUpload">x</span>
            <form method="post" enctype="multipart/form-data">
                <?php if(isset($upload_success)) echo "<div class='success'>$upload_success</div>"; ?>
                <?php if(isset($upload_error)) echo "<div class='error'>$upload_error</div>"; ?>
                <input type="file" name="profile_pic" required>
                <button type="submit" name="upload_profile">Upload</button>
            </form>
        </div>
    </div>
</header>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <?php if($role=='admin' || $role=='pharmacist'): ?>
        <a href="add_medicine.php">Add Medicine</a>
        <a href="viewmedicine.php">View Medicine</a>
    <?php endif; ?>
    <?php if($role=='admin' || $role=='cashier'): ?>
        <a href="sales.php">Billing</a>
    <?php endif; ?>
    <?php if($role=='admin'): ?>
        <a href="manage_users.php">Manage Users</a>
    <?php endif; ?>
    <a href="analytics.php">Analytics & Reports</a>
</nav>

<div class="container">
    <!-- Dashboard Cards -->
    <div class="card"><h3>Total Medicines</h3><p><?php echo $total_medicines; ?></p></div>
    <div class="card"><h3>Sales Today</h3><p><?php echo $sales_today; ?></p></div>
    <div class="card"><h3>Low Stock</h3><p><?php echo $low_stock; ?></p></div>
    <div class="card"><h3>Expired Medicines</h3><p><?php echo $expired_medicines; ?></p></div>
    <?php if($role=='admin'): ?><div class="card"><h3>Total Users</h3><p><?php echo $total_users; ?></p></div><?php endif; ?>
    <?php if($role=='admin' || $role=='cashier'): ?><div class="card"><h3>Active Bills Today</h3><p><?php echo $active_bills; ?></p></div><?php endif; ?>


    </div>
</div>

<script>
// Toggle profile upload form
const profilePic = document.getElementById('profilePic');
const uploadForm = document.getElementById('uploadForm');
const closeUpload = document.getElementById('closeUpload');
profilePic.addEventListener('click', () => {
    uploadForm.style.display = (uploadForm.style.display === 'block') ? 'none' : 'block';
});
closeUpload.addEventListener('click', () => { uploadForm.style.display = 'none'; });
</script>

</body>
</html>