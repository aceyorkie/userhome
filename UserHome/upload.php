<?php
session_start();
$id_no = $_SESSION['id_no'];

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'orgportal';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $fileName = basename($_FILES["profile_image"]["name"]);
    $targetDir = "uploads/";
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {
            $stmt = $conn->prepare("UPDATE student SET profile_image = ? WHERE id_no = ?");
            $stmt->bind_param("ss", $fileName, $id_no);
            if ($stmt->execute()) {
                // Redirect with success message
                header("Location: profile.php?upload=success");
                exit();
            } else {
                header("Location: profile.php?upload=db_fail");
                exit();
            }
        } else {
            header("Location: profile.php?upload=move_fail");
            exit();
        }
    } else {
        header("Location: profile.php?upload=invalid_type");
        exit();
    }
} else {
    header("Location: profile.php?upload=none");
    exit();
}
?>
