<?php
include 'db.php';

if(isset($_POST['register'])){

    $firstName = $_POST['fName'];
    $lastName  = $_POST['lName'];
    $email     = $_POST['email'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT); // 🔒 Hash password
    $role      = $_POST['role'];

    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        echo "User already exists!";
    } else {
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (firstName,lastName,email,password,role) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $firstName, $lastName, $email, $password, $role);
        if($stmt->execute()){
            echo "User registered successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>