<?php
session_start();

if (!isset($_SESSION['id_no'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'student_portal';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id_no = $_SESSION['id_no'];

$sql = "SELECT id_no, name, department, year, course FROM users WHERE id_no='$id_no'";
$result = $conn->query($sql);

$course = '';
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $course = $row['course'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        #postsContainer {
            margin: 20px auto;
            padding: 10px;
            max-width: 800px;
            font-family: "Roboto", sans-serif;
        }
        #postsContainer div {
            margin-bottom: 15px;
            padding: 10px;
            border-left: 5px solid maroon;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        #postsContainer .announcement {
            border-left-color: maroon;
            font-weight: bold;
        }
        #postsContainer .recruitment {
            border-left-color: yellow;
            font-style: italic;
        }
    </style>
</head>

<body>
    <nav>
        <div class="nav-left">
            <img src="greetings/umdc-logo.png" alt="Logo" class="logo">
        </div>
        <div class="nav-center">
            <a href="#home" onclick="non()">Non-Academic Organization</a>
            <a href="#organizations" onclick="org()">Organizations</a>
            <a href="#about" onclick="about()">About</a>
        </div>
        <div class="nav-right">
            <a href="#profile" onclick="profile()">Profile</a>
        </div>
    </nav>

    <div id="postsContainer"></div>

    <script>
        function home() {
            window.location.href = 'HomePage.html';
        }

        function org() {
            let course = "<?php echo htmlspecialchars($course); ?>";  
            console.log(course);

            if (course === "BS IN INFORMATION TECHNOLOGY") {
                window.location.href = "DTP.html";
            } else if (course === "BS IN COMPUTER ENGINEERING") {
                window.location.href = "CE.html";
            } else if (course === "BS IN TOURISM MANAGEMENT") {
                window.location.href = "TM.html";
            } else {
                alert("No page available for this course.");
            }
        }

        function profile() {
            window.location.href = 'profile.php';
        }

    </script>
</body>
</html>
