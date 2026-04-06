<?php
session_start();
include 'db.php';

if(isset($_POST['upload'])){
    $file = $_FILES['image'];

    $fileName = time() . "_" . str_replace(' ', '_', $file['name']);
    $tmpName = $file['tmp_name'];

    $folder = "uploads/" . $fileName;

    // Create folder if not exists
    if(!is_dir("uploads")){
        mkdir("uploads", 0777, true);
    }

    if(move_uploaded_file($tmpName, $folder)){
        $email = $_SESSION['email'];

        $stmt = $conn->prepare("UPDATE users SET profile_pic=? WHERE email=?");
        $stmt->bind_param("ss", $folder, $email);
        $stmt->execute();

        $_SESSION['profile_pic'] = $folder;

        echo "Uploaded successfully!";
    } else {
        echo "Upload failed!";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <button type="submit" name="upload">Upload</button>
</form>