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

// Get current student's course using session ID
$id_no = $_SESSION['id_no'] ?? '';
$stmt = $conn->prepare("SELECT name FROM student WHERE id_no = ?");
$stmt->bind_param("s", $id_no);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$name = $row['name'];
$student_course = '';

if ($id_no) {
    $stmt = $conn->prepare("SELECT course FROM student WHERE id_no = ?");
    $stmt->bind_param("s", $id_no);
    $stmt->execute();
    $stmt->bind_result($student_course);
    $stmt->fetch();
    $stmt->close();
}

// Fetch Academic Organizations filtered by course and approved status
$acadOrganizations = [];
if ($student_course) {
    $stmt = $conn->prepare("SELECT org_code, org_name, org_description, org_logo FROM dtp_organization  WHERE org_status = 'approved' AND org_course = ?");
    $stmt->bind_param("s", $student_course);
    $stmt->execute();
    $result_acad = $stmt->get_result();
    while ($row = $result_acad->fetch_assoc()) {
        $acadOrganizations[] = $row;
    }
    $stmt->close();
}

// Fetch all Non-Academic Organizations (no filter needed unless desired)
$nonAcadOrganizations = [];
$sql_nonacad = "SELECT org_code, org_name, org_description, org_logo FROM nonacad_organization";
$result_nonacad = $conn->query($sql_nonacad);
if ($result_nonacad && $result_nonacad->num_rows > 0) {
    while ($row = $result_nonacad->fetch_assoc()) {
        $nonAcadOrganizations[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Organization</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/userHomeCopy/UserHome/css/DTP.css">
  <link rel="stylesheet" href="/userHomeCopy/UserHome/css/nav.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

  <!-- ORGANIZATION -->

  <div class="body-container"> 
    <div class="page-wrapper">
      <div class="text2">
        <h1>Clubs & Organizations</h1>
        <h3>Introducing Academic and Non-Academic Organizations: a student resource for essential skills, career readiness, and tailored support. </h3>
      </div>

      <div class="OrgHeader">
        <h2>Academic<span> Organizations</span></h2>
        <div style="display: flex">
          <button class="arrow arrow-left"><i class="fas fa-chevron-left"></i></button> <button class="arrow arrow-right"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
      <div class="slider-container">
        <div class="slider" id="slider">
          <div id="content1" class="slide">
            <?php foreach ($acadOrganizations as $org): ?>
                <div class="square-container">
                  <div class="orgcontainer">
                    <img class="org_img" src="http://localhost/osaDashboard/org_logo/<?php echo basename($org['org_logo']); ?>">
                    <h3><?php echo htmlspecialchars($org['org_code']); ?></h3>
                  </div>
                  <h2 class="Organization"><?php echo htmlspecialchars($org['org_name']); ?></h2>
                  <h3 class="org_descript"><?php echo htmlspecialchars($org['org_description']); ?></h3>
                  <button class="view-button animated-button" onclick="openJoinContainer('<?php echo htmlspecialchars($org['org_code']); ?>')">
                    <span>Join</span>
                  </button>    
                </div>
            <?php endforeach; ?>
          </div>

          <div id="content2" class="slide">
            <?php foreach ($nonAcadOrganizations as $org): ?>
                <div class="square-container">
                  <div class="orgcontainer">
                    <img class="org_img" src="http://localhost/osaDashboard/org_logo/<?php echo htmlspecialchars($org['org_logo']); ?>">
                    <h3><?php echo htmlspecialchars($org['org_code']); ?></h3>
                  </div>
                  <h2 class="Organization"><?php echo htmlspecialchars($org['org_name']); ?></h2>
                  <h3 class="org_descript"><?php echo htmlspecialchars($org['org_description']); ?></h3>
                  <button class="view-button animated-button" onclick="openAboutOrganization('<?php echo htmlspecialchars($org['org_code']); ?>_about')">
                    <span>About</span>
                  </button>
                  <button class="view-button animated-button" onclick="openJoinContainer('<?php echo htmlspecialchars($org['org_code']); ?>')">
                    <span>Join</span>
                  </button>    
                </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="overlay"></div>
  <div id="joincontainer">
      <p>Enter your ID NO. to join <span id="orgName"></span>:</p>
      <input type="text" id="idNoInput" placeholder="Your ID number">
      <br>
      <button class="ok-button" onclick="submitJoinContainer()">OK</button>
      <button class="cancel-button" onclick="closeJoinContainer()">Cancel</button>
  </div>

    <script>
        const slider = document.getElementById('slider');
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;
        let currentIndex = 0;

        const orgHeader = document.querySelector('.OrgHeader h2');

        const updateSlider = () => {
          slider.style.transform = `translateX(-${currentIndex * slider.offsetWidth}px)`;

          // Change header text based on the currentIndex
          if (currentIndex === 0) {
            orgHeader.innerHTML = 'Academic <span>Organizations</span>';
          } else if (currentIndex === 1) {
            orgHeader.innerHTML = 'Non-Academic <span>Organizations</span>';
          }
        };

        document.querySelector('.arrow-left').addEventListener('click', () => {
          currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
          updateSlider();
        });

        document.querySelector('.arrow-right').addEventListener('click', () => {
          currentIndex = (currentIndex + 1) % totalSlides;
          updateSlider();
        });

        updateSlider();
    </script>
    <script>
        function home() {
            window.location.href = 'HomePage.php';
        }
        function org() {
            window.location.href = 'DTP.html';
        }
        function profile() {
            window.location.href = 'profile.php'; 
        }
        function openAboutOrganization(descriptionBoxID) {
            document.getElementById('overlay').style.display = 'block';

        document.querySelectorAll('.aboutcontainer').forEach(container => {
            container.style.display = 'none';
        });

        document.getElementById(descriptionBoxID).style.display = 'block';
        }

        function closeAboutContainer() {
            document.getElementById('overlay').style.display = 'none';

            document.querySelectorAll('.aboutcontainer').forEach(container => {
                container.style.display = 'none';
            });
        }


        function openJoinContainer(organizationName) {
            document.getElementById('orgName').innerText = organizationName;
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('joincontainer').style.display = 'block';
        }

        function closeJoinContainer() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('joincontainer').style.display = 'none';
        }

        function submitJoinContainer() {
            let id_no = document.getElementById('idNoInput').value.trim();
            let organizationName = document.getElementById('orgName').innerText.trim();

            if (id_no && organizationName) {
                console.log("Submitting:", { id_no, organizationName });
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "join_organization.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        alert(xhr.responseText);
                        closeJoinContainer();
                    }
                };

                xhr.send(`id_no=${encodeURIComponent(id_no)}&organization=${encodeURIComponent(organizationName)}`);
            } else {
                alert("Please fill in all the required fields.");
            }
        }
    </script>
</html>