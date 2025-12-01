<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'orgportal';

$conn = new mysqli($host, $user, $password, $dbname);

$data = json_decode(file_get_contents("php://input"), true);
$post_id = intval($data['post_id']);
$comment = $conn->real_escape_string($data['comment']);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO post_comments (post_id, user_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $post_id, $user_id, $comment);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Comment added"]);
