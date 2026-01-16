<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'orgportal';

$conn = new mysqli($host, $user, $password, $dbname);

$organization = $conn->real_escape_string($_GET['organization']);

$sql = "SELECT org_code, org_description FROM dtp_organization WHERE org_code = '$organization' LIMIT 1";
$res = $conn->query($sql);

if ($res->num_rows === 1) {
    $row = $res->fetch_assoc();
    echo json_encode([
        "name" => $row["org_code"],
        "description" => $row["org_description"]
    ]);
} else {
    echo json_encode([
        "name" => $organization,
        "description" => "No description available."
    ]);
}

$conn->close();
?>
