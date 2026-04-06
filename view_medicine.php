<?php
include 'db.php';
session_start();
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    exit();
}

// Delete medicine
if(isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM medicines WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: viewmedicine.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Medicines</title>
    <style>
        body { font-family: Arial; background:#f4f6f9; margin:0; padding:0; }
        .top-bar { background:#2c3e50; padding:15px; text-align:right; }
        .top-bar a { color:white; text-decoration:none; font-weight:bold; }
        .container { width:95%; margin:30px auto; background:white; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; margin-bottom:20px; color:#27ae60; }
        input { padding:10px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc; margin-right:10px; }
        table { border-collapse:collapse; width:100%; text-align:left; }
        table, th, td { border:1px solid #ddd; }
        th, td { padding:12px; }
        th { background-color:#27ae60; color:white; }
        tr:nth-child(even) { background-color:#f2f2f2; }
        tr:hover { background-color:#d1f2d1; }
        .action a { text-decoration:none; padding:5px 10px; border-radius:4px; margin-right:5px; color:white; font-weight:bold; }
        .edit-btn { background-color:#3498db; } .edit-btn:hover { background-color:#2980b9; }
        .delete-btn { background-color:#e74c3c; } .delete-btn:hover { background-color:#c0392b; }
    </style>
</head>
<body>

<div class="top-bar"><a href="dashboard.php">Dashboard</a></div>

<div class="container">
    <h2>Medicine List</h2>
    <input type="text" id="searchInput" placeholder="Search by name or category...">
    <input type="date" id="searchDate" placeholder="Filter by expiry date">

    <table>
        <thead>
            <tr>
                <th>Name</th><th>Category</th><th>Price</th><th>Quantity</th><th>Expiry</th><th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
        <?php
        $result = $conn->query("SELECT * FROM medicines ORDER BY id DESC");
        while($row = $result->fetch_assoc()){
            echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['category']}</td>
                <td>{$row['price']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['expiry_date']}</td>
                <td class='action'>
                    <a class='edit-btn' href='edit_medicine.php?id={$row['id']}'>Edit</a>
                    <a class='delete-btn' href='viewmedicine.php?delete_id={$row['id']}' onclick='return confirm(\"Delete this medicine?\")'>Delete</a>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
const searchInput = document.getElementById("searchInput");
const searchDate = document.getElementById("searchDate");
const rows = document.querySelectorAll("#tableBody tr");
function filterTable(){
    const text = searchInput.value.toLowerCase();
    const date = searchDate.value;
    rows.forEach(row=>{
        const rowText=row.innerText.toLowerCase();
        const rowDate=row.cells[4].innerText;
        const matchesText=rowText.includes(text);
        const matchesDate=!date || rowDate===date;
        row.style.display=(matchesText && matchesDate)?"":"none";
    });
}
searchInput.addEventListener("keyup", filterTable);
searchDate.addEventListener("change", filterTable);
</script>

</body>
</html>