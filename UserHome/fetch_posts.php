<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'orgportal';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$organization = isset($_GET['organization']) ? $conn->real_escape_string($_GET['organization']) : '';

$query = "SELECT * FROM posts 
          WHERE status = 'approved' 
          AND organization = '$organization' 
          AND type IN ('announcement', 'recruitment')";

$result = $conn->query($query);

$posts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagePath = !empty($row['image_path']) 
    ? "http://localhost/officerDashboardCopy/uploads/" . basename($row['image_path']) 
    : "http://localhost/officerDashboardCopy/uploads/default.jpg";
        $posts[] = [
            'id' => $row['id'],
            'type' => $row['type'],
            'title' => $row['title'],
            'content' => $row['content'],
            'image' => $imagePath,
            'location' => $row['location'],
            'from_date' => $row['from_date'],
            'to_date' => $row['to_date'],
            'event_time' => $row['event_time']
        ];
    }
}

echo json_encode($posts);

$conn->close();
?>
