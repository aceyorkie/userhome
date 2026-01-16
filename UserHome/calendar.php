<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'orgportal';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
$user_id = $_SESSION['id_no'] ?? null;

if (!$user_id) {
    die("Not logged in.");
}

$id_no = $_SESSION['id_no'] ?? '';
$stmt = $conn->prepare("SELECT name FROM student WHERE id_no = ?");
$stmt->bind_param("s", $id_no);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$name = $row['name'];

$studentOrgs = [];
$stmt = $conn->prepare("
    SELECT organization_name 
    FROM user_organizations 
    WHERE user_id = ? AND status = 'approved'
");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
    $studentOrgs[] = $row['organization_name'];
}
$stmt->close();

/* -------------------------------------
   2. FETCH INSTITUTIONAL EVENTS
-------------------------------------- */

$institutional = [];
$sql = "SELECT event_name AS title, event_date AS start, event_location AS location, 'Institutional' AS type 
        FROM institutional_events 
        WHERE status = 'approved'";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $row['color'] = '#800000';
    $institutional[] = $row;
}

/* -------------------------------------
   3. FETCH ORGANIZATIONAL EVENTS FOR EACH ORG
-------------------------------------- */

$organizational = [];

if (!empty($studentOrgs)) {
    $stmt = $conn->prepare("
        SELECT event_name AS title, event_date AS start, event_location AS location, 'Organizational' AS type
        FROM organizational_events
        WHERE status = 'approved' AND organization = ?
    ");

    foreach ($studentOrgs as $org) {
        $stmt->bind_param("s", $org);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $row['color'] = '#b03060';
            $organizational[] = $row;
        }
    }
    $stmt->close();
}

$conn->close();

/* -------------------------------------
   4. MERGE AND RETURN JSON IF REQUESTED
-------------------------------------- */

$events = array_merge($institutional, $organizational);

if (isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode($events);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Event Calendar</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <link rel="stylesheet" href="/userHomeCopy/UserHome/css/calendar.css">
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

    <div id="calendar"></div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'standard',
            events: 'calendar.php?json=1',
            eventClick: function(info) {
            const e = info.event.extendedProps;
            alert(
                `📌 ${info.event.title}\nType: ${e.type}\nLocation: ${e.location}\nDate: ${info.event.start.toLocaleDateString()}`
            );
            },
            headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            height: 'auto'
        });

        calendar.render();
        });
    </script>

</body>
</html>
