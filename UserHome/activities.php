<?php
session_start();

if (!isset($_SESSION['id_no'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'orgportal';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

$id_no = $_SESSION['id_no'];
$stmt = $conn->prepare("SELECT name FROM student WHERE id_no = ?");
$stmt->bind_param("s", $id_no);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$name = $row['name'];

$sql = "
    SELECT ua.*, p.location, p.from_date, p.to_date, p.event_time
    FROM user_activity ua
    LEFT JOIN posts p ON ua.post_id = p.id
    WHERE ua.id_no = '$id_no'
    ORDER BY ua.id DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Activities</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/activities.css">
    <link rel="stylesheet" href="/userHomeCopy/UserHome/css/nav.css">
</head>

<body>
  <header class="top-nav">
    <div class="top-nav-left">
      <img src="greetings/logo.png" alt="Logo" class="top-logo">
    </div>

    <div class="top-nav-right">
      <div class="profile-info">
        <h3><?php echo htmlspecialchars($id_no); ?></h3>
        <h1><?php echo htmlspecialchars($name); ?></h1>
      </div>
    </div>
  </header>

  <nav>
    <div class="nav-center">
      <a href="profile.php">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
      </a>

      <a href="DTP.php">
        <i class="fa-solid fa-building-columns"></i>
        <span>Organizations</span>
      </a>

      <a href="calendar.php">
        <i class="fa-solid fa-calendar-days"></i>
        <span>Calendar</span>
      </a>

      <a href="activities.php">
        <i class="fa-solid fa-list-check"></i>
        <span>My Activities</span>
      </a>
    </div>

    <div class="nav-profile">
      <div class="profile-icon">
        <i class="fa-solid fa-user"></i>
      </div>

      <div class="profile-info">
        <h3><?php echo htmlspecialchars($id_no); ?></h3>
        <h1><?php echo htmlspecialchars($name); ?></h1>
      </div>
    </div>
  </nav>

    <div class="activities-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                $statusClass = strtolower($row['status']) === "approved" ? "status-approved" :
                            (strtolower($row['status']) === "declined" ? "status-declined" : "status-pending");

                echo "
                <div class='activity-card'>

                    <div class='activity-header'>
                        <h3 class='activity-title'>{$row['title']}</h3>
                        <span class='status-badge $statusClass'>".ucfirst($row['status'])."</span>
                    </div>

                    <p class='activity-org'>Organization: <strong>{$row['organization']}</strong></p>
                    
                    <div class='activity-content'>
                        <p>{$row['content']}</p>
                    </div>

                    <div class='activity-details'>
                        <p><strong>Location:</strong> ".($row['location'] ?: "N/A")."</p>
                        <p><strong>Date:</strong> ".($row['from_date'] ?: "N/A")." - ".($row['to_date'] ?: "N/A")."</p>
                        <p><strong>Time:</strong> ".($row['event_time'] ?: "N/A")."</p>
                    </div>

                </div>";
            }
        } else {
            echo "<p class='no-activities'>You have not joined any activities yet.</p>";
        }
        ?>
    </div>

</body>
</html>
