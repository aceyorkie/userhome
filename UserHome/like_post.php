<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'orgportal';

$conn = new mysqli($host, $user, $password, $dbname);

$data = json_decode(file_get_contents("php://input"), true);
$post_id = intval($data['post_id']);
$like = $data['like'];
$user_id = $_SESSION['user_id']; // Must be set when user logs in

if ($like) {
    $stmt = $conn->prepare("INSERT IGNORE INTO post_likes (post_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $post_id, $user_id);
} else {
    $stmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
}

$stmt->execute();
echo json_encode(["success" => true, "message" => $like ? "Liked" : "Unliked"]);
