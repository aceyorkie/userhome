<?php

session_start();

if (!isset($_SESSION['id_no'])) {
    echo "Unauthorized access.";
    exit();
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'orgportal';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);

$organization = $conn->real_escape_string($data['organization']);
$reason = $conn->real_escape_string($data['reason']);
$student_name = $conn->real_escape_string($data['student_name']);
$id_no = $_SESSION['id_no'];

// Check if the user already has a pending leave request for the same organization
$check_sql = "SELECT * FROM leave_requests WHERE id_no = '$id_no' AND organization = '$organization' AND status = 'pending'";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows > 0) {
    // If there's already a pending request, prevent submission
    echo "You have already submitted a leave request for this organization.";
    $conn->close();
    exit();
}

// Insert the leave request if no pending request exists
$sql = "INSERT INTO leave_requests (id_no, name, organization, reason, status) 
        VALUES ('$id_no', '$student_name', '$organization', '$reason', 'pending')";

if ($conn->query($sql) === TRUE) {
    echo "Leave request submitted successfully.";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();

?>
