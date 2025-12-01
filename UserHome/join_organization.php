<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'orgportal';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id_no = $_POST['id_no'];
$organization = $_POST['organization'];

if (!isset($_SESSION['id_no']) || $_SESSION['id_no'] !== $id_no) {
    echo "Invalid ID number.";
    exit;
}

$stmt = $conn->prepare("SELECT name, year FROM student WHERE id_no = ?");
$stmt->bind_param("s", $id_no);
$stmt->execute();
$stmt->bind_result($name, $year);
$stmt->fetch();
$stmt->close();

if ($name) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_organizations WHERE user_id = ? AND organization_name = ?");
    $stmt->bind_param("ss", $id_no, $organization);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "You are already a member of " . htmlspecialchars($organization) . "!";
        exit;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_organizations WHERE user_id = ?");
    $stmt->bind_param("s", $id_no);
    $stmt->execute();
    $stmt->bind_result($total_orgs);
    $stmt->fetch();
    $stmt->close();

    if ($total_orgs >= 2) {
        echo "You can only join up to 2 organizations!";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO user_organizations (username, user_id, organization_name, status, user_year) VALUES (?, ?, ?, 'pending', ?)");
    $stmt->bind_param("ssss", $name, $id_no, $organization, $year);

    if ($stmt->execute()) {
        echo "You have successfully joined " . htmlspecialchars($organization);
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID number not found. Please register first.";
}

$conn->close();
?>
