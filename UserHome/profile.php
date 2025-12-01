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
    die("Connection failed: " . $conn->connect_error);
}

$id_no = $_SESSION['id_no'];

$sql = "SELECT id_no, name, department, year, course, profile_image FROM student WHERE id_no='$id_no'";
$result = $conn->query($sql);

$org_sql = "SELECT organization_name 
            FROM user_organizations 
            WHERE user_id = '$id_no' AND status = 'approved'";

$org_result = $conn->query($org_sql);

$course = '';
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $course = $row['course'];
}

require_once 'phpqrcode/qrlib.php';

// Create a folder for QR codes if it doesn’t exist
$qrDir = 'qrcodes/';
if (!file_exists($qrDir)) {
    mkdir($qrDir, 0777, true);
}

// Use the student's ID number as a unique identifier
$qrData = "ID: " . $row['id_no'] . "\nName: " . $row['name'] . "\nCourse: " . $row['course'];

// Set the filename
$qrFile = $qrDir . $row['id_no'] . '.png';

// Generate only once if not existing
if (!file_exists($qrFile)) {
    QRcode::png($qrData, $qrFile, QR_ECLEVEL_L, 6);
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
        }
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 20px;
            font-family: "Roboto", serif;
            position: relative;
            z-index: 1;
        }
        .nav-left .logo {
            height: 40px;
        }
        .nav-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
        }
        .nav-center a,
        .nav-right a {
            color: rgb(0, 0, 0);
            text-decoration: none;
            margin: 0 15px;
        }
        .nav-right a:hover, .nav-center a:hover {
            color: #7b0000;
        }
        body {
            background-color: #f9f9f9;
        }
        .profile-container {
            font-family: "Poppins", sans-serif;
            padding: 50px 20px 20px;
            text-align: center;
            max-width: 870px;
            margin: 0 auto;
            width: 500%;
        }
        h1, h3 {
            margin: 0; 
            padding: 0; 
        }

        h1 {
            font-family: "Poppins", sans-serif;
            color: maroon;
            text-align: left;
            font-size: 30px;
        }

        h3 {
            font-family: "Poppins", sans-serif;
            color: black;
            text-align: left;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            margin-left: 30px;
            margin-top: 10px;
            margin-right: 20px;
            border-radius: 50%;
            box-shadow: 0 4px 8px 2px rgba(71, 71, 71, 0.2);
            object-fit: cover;
        }
        p {
            font-family: "Montserrat", sans-serif;
            margin: 8px 0;
            color: #555;
            font-size: 20px;
        }
        
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        .grid-container2 {
            background-color: white;
            display: flex;
            align-items: center;
            gap: 20px;
            height: 220px;
            padding: 20px;
            border-radius: 30px;
            box-shadow: 0 4px 8px 2px rgba(71, 71, 71, 0.2);
            cursor: pointer;
            transition: 0.5s;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 250px);
            gap: 40px;
            padding: 20px;
            margin-top: 20px;
            justify-content: center;
            max-width: 750px;
            margin: 0 auto;
        }

        .grid-item {
            background-color: white;
            display: flex;
            align-items: flex-start;
            flex-direction: column; 
            justify-content: space-between;
            margin-top: 20px;
            color: #7b0000;
            font-family: "Poppins", sans-serif;
            font-weight: 900;
            font-size: 40px;
            text-align: left;
            height: 220px;
            border-radius: 30px;
            box-shadow: 0 4px 8px 2px rgba(71, 71, 71, 0.2);
            padding-bottom: 10px;
            padding-right: 20px;
            padding-left: 20px;
            cursor: pointer;
            transition: 0.5s;
        }

        .grid-item2 {
            background-color: white;
            display: flex;
            align-items: flex-end;
            margin-top: 20px;
            color: #7b0000;
            font-family: "Poppins", sans-serif;
            font-weight: 900;
            font-size: 40px;
            text-align: left;
            height: 220px;
            border-radius: 30px;
            box-shadow: 0 4px 8px 2px rgba(71, 71, 71, 0.2);
            padding-bottom: 20px;
            padding-right: 20px;
            padding-left: 20px;
            cursor: pointer;
            transition: 0.5s;
        }

        .grid-item h4{
            font-family: "Poppins", sans-serif;
            color: black;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: 100;
        }

        .title-with-arrow {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0;
            padding-top: 15px;
            font-size: 14px;
        }

        .title-with-arrow .arrow-icon {
            font-size: 20px;     
        }


        .grid-item:hover {
            transform: translateY(-20px);
        }

        .grid-item2:hover {
            transform: translateY(-20px);
        }

        .postsWrapper {
            background-color: white;
            border: 1px solidrgb(220, 220, 220);
            border-radius: 30px;
            padding: 30px;
            margin: 20px auto;
            width: 100%;
            max-width: 870px; 
            box-shadow: 0 4px 8px 2px rgba(71, 71, 71, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-top: 20px; 
        }

        .postsHeader {
            font-size: 30px;
            color: #7b0000; 
            text-align: left;
            width: 500px;
        }

       #postsContainer {
            display: flex;
            flex-wrap: nowrap; 
            overflow-x: auto;  
            gap: 20px;         
            justify-content: flex-start; 
            padding: 10px;     
            width: 100%;
        }

        #postsContainer > div {
            flex-shrink: 0;
            width: 350px;   
            border-radius: 10px;
            padding: 20px; 
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        #postsContainer > div:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .post-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        #postsContainer > div > div {
            padding: 5px;
            text-align: left;
            background-color: transparent;
        }

        #postsContainer .announcement {
            background-color: rgb(243, 243, 243);
            width: 450px;
        }

        #postsContainer .recruitment {
            background-color: rgb(243, 249, 197);
            border-left: 4px solid rgba(248, 255, 111, 0.68);
        }

        strong {
            display: block;
            font-size: 1.1rem;
            color: #222;
        }

        .post-content {
            color: #555;
            font-size: 0.95rem;
            display: block;
        }

        .see-more {
            display: inline-block;
            margin-top: 5px;
            font-size: 0.9rem;
            color: #0077cc;
            text-decoration: none;
        }

        .see-more:hover {
            text-decoration: underline;
        }

        .join-button {
            margin: 10px 15px 15px;
            padding: 8px 16px;
            background-color: #ffcc00;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .join-button:hover {
            background-color: #ffdb4d;
        }

        /* Scrollbar styling */
        #postsContainer::-webkit-scrollbar {
            height: 6px;
        }

        #postsContainer::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 6px;
        }

        #postsContainer::-webkit-scrollbar-track {
            background-color: #f5f5f5;
            border-radius: 6px;
        }

        footer {
            background-color: maroon;
            color: white;
            margin-top: 200px;
            padding: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center; 
            position: relative;
        }

        .footer-left {
            display: flex;
            align-items: center; 
        }

        .footer-left p {
            text-align: center;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            margin-left: 30px;
            color: white;
            
        }

        .footer-right {
            display: flex;
            justify-content: flex-end;
            gap: 30px;
        }

        .footer-right a {
            text-decoration: none;
            text-align: right;
            font-family: "Montserrat", sans-serif;
            color: white;
        }

        .footer-content .logo {
            height: 40px;
        }
        .color{
            background-image: linear-gradient(to right, #DACE4E 10%, #BC0000 90%);
            -webkit-background-clip: text;
            color: transparent;
        }
        .postsHeader h2{
            margin-bottom: 0%;
        }
        .postsHeader h4{
            font-size: 20px;
            font-weight: 300;
        }
        .post-gif {
            display: block;
            margin: 0 auto; /* Center the GIF */
            width: 200px; /* Adjust size as needed */
            height: auto;
        }

        .post-content {
            display: block;
            margin-bottom: 20px;
        }
        .post-details{
            display: flex;
            gap: 20px;
            text-align: center;
            font-family: "Poppins", sans-serif;
            font-size: 10px;
            font-weight: 300;
        }
        .details1{
            background-color:rgba(128, 0, 0, 0.55);
            padding-top: 5px;
            padding-bottom: 5px;
            padding-right: 30px;
            padding-left: 30px;
            border-radius: 30px;
            font-family: "Poppins", sans-serif;
            color: white;
            font-size: 10px;
            font-weight: 300;
            margin-bottom: 10px;
        }
        .details2{
            background-color:rgba(241, 255, 38, 0.55);
            padding-top: 5px;
            padding-bottom: 5px;
            padding-right: 30px;
            padding-left: 30px;
            border-radius: 30px;
            font-family: "Poppins", sans-serif;
            color: maroon;
            font-size: 10px;
            font-weight: 300;
            margin-bottom: 10px;
        }
        .details3{
            background-color:rgba(215, 215, 215, 0.55);
            padding-top: 5px;
            padding-bottom: 5px;
            padding-right: 30px;
            padding-left: 30px;
            border-radius: 30px;
            font-family: "Poppins", sans-serif;
            color: maroon;
            font-size: 10px;
            font-weight: 300;
            margin-bottom: 10px;
        }
        .action-btn{
            margin-top: 30px;
        }
        .circle-button {
            width: 50px;
            height: 50px;
            margin-right: 5px;
            border-radius: 50%;
            border: none;
            background-color:rgb(217, 217, 217);
            color: #333;
            font-size: 18px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
        }
        .circle-button.active {
            color: maroon;
        }

        .circle-button:hover {
            background-color:rgb(199, 198, 198);
        }

        #joincontainer {
            font-family: "Montserrat", sans-serif;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
            text-align: center;
        }
        #joincontainer input {   
            margin-top: 10px;
            padding: 8px;
            width: 80%;
        }
        #joincontainer button {
            margin-top: 10px;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        #joincontainer .ok-button {
            background-color: maroon;
            color: white;
        }
        #joincontainer .cancel-button {
            background-color: #ccc;
            color: black;
        }
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .post-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }


    </style>
</head>
<body>
    <nav>
        <div class="nav-left">
            <img src="greetings/umdc-logo.png" alt="Logo" class="logo">
        </div>
        <div class="nav-center">
            <a href="#profile" onclick="profile()">Dashboard</a> 
            <a href="#organizations" onclick="org()">Academic</a>
            <a href="nonacad.html" onclick="org()">Non Academic</a>
            <a href="#about" onclick="about()">About</a>
        </div>
        <div class="nav-right">
            <a href="#" onclick="confirmLogout()">Log Out</a>
        </div>
    </nav>
    
    <div class="profile-container">
        <?php

        $name = htmlspecialchars($row['name']);
        $profileImage = isset($row['profile_image']) && !empty($row['profile_image']) 
        ? 'uploads/' . htmlspecialchars($row['profile_image']) 
        : 'uploads/default.jpg';
        $id_no = htmlspecialchars($row['id_no']);
        $department = htmlspecialchars($row['department']);
        $course = htmlspecialchars($row['course']);

    echo "<div class='grid-container2'>
    <form id='uploadForm' action='upload.php' method='post' enctype='multipart/form-data'>
        <label for='profileInput'>
            <img src='$profileImage' alt='Profile Picture' class='profile-img' style='cursor: pointer; border-radius: 50%; width: 150px; height: 150px; object-fit: cover;'>
        </label>
        <input type='file' id='profileInput' name='profile_image' style='display: none;' onchange='document.getElementById(\"uploadForm\").submit();'>
    </form>

        <div class='profile-name'>
            <h3>$id_no</h3>
            <h1>$name</h1>
            <h3 style='font-weight: 400'>$course</h3>
            <h3 style='font-weight: 400'>$department</h3>
        </div>

        <div style='text-align:left; margin-left:50px; margin-top:10px;'>
            <img src='$qrFile' alt='QR Code' style='width:120px; height:120px;'>
        </div>
    </div>";

        echo "<div class='grid-container'>";
            echo "<div class='grid-item2' style='background-color: maroon;'><h1 style='font-size: 30px; color:white; width: 10px; line-height: 1.2'>My Activities</h1></div>";

            if ($org_result->num_rows > 0) {
                while ($org_row = $org_result->fetch_assoc()) {
                    $org_name = htmlspecialchars($org_row['organization_name']);
                    echo "<div class='grid-item'>
                    <h4 class='title-with-arrow'>
                        <span class='text'>Want to join an activity? Click here.</span>
                        <span class='arrow-icon'>&#8599;</span>
                    </h4>
                    <div>$org_name</div>
                    </div> ";
                }
            }

            $org_count = $org_result->num_rows;
            for ($i = $org_count; $i < 2; $i++) {
                echo "<div class='grid-item'>
                    <h4 class='title-with-arrow'>
                        <span class='text'>Empty, Please join an organization first.</span>
                        <span class='arrow-icon'>&#8599;</span>
                    </h4>
                    <div>EMPTY</div>
                </div>";
            }

            echo "</div>";

        ?>

        <div id="leaveModal" style="display: none;">
            <div style="background: white; padding: 20px; border-radius: 8px; width: 300px; margin: auto; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
                <h3 id="modalOrgName"></h3>
                <p>Do you want to leave this organization?</p>
                <textarea id="leaveReason" placeholder="Reason for leaving..." style="width: 100%; margin: 10px 0;"></textarea>
                <button onclick="submitLeaveRequest()">Submit</button>
                <button onclick="closeModal()">Cancel</button>
            </div>
        </div>

        <div class="postsHeader">
            <h2 style="width: 390px; line-height: 1.2">Upcoming & Ongoing <span class="color">Events</span></h2>
            <h4>These activities and events are thoughtfully designed to educate and inspire participants.</h4>
        </div>
        <div class="postsWrapper">
            <div id="postsContainer"></div>
        </div>


        <?php
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $dbname = 'orgportal';
        $conn = new mysqli($host, $user, $password, $dbname);

        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        $query = "SELECT * FROM posts WHERE status = 'approved' AND organization = 'GMITS' AND type IN ('announcement', 'recruitment')";
        
        $result = $conn->query($query);
          
        ?>

    </div>

    <div id="overlay"></div>
    <div id="joincontainer">
        <p>Enter your ID NO. to join <span id="orgName"></span> activities:</p>
        <input type="text" id="idNoInput" placeholder="Your ID number">
        <br>
        <button class="ok-button" onclick="submitJoinContainer()">OK</button>
        <button class="cancel-button" onclick="closeJoinContainer()">Cancel</button>
    </div>

    <footer>
        <div class="footer-left">
           <p>&copy; 2024 AJNova Platforms. All rights reserved.</p>
        </div>
        <div class="footer-content">
             <img src="greetings/footerlogo.png" alt="Logo" class="logo">  
        </div>
        <div class="footer-right">
            <a href="#home" onclick="home()">Home</a>
            <a href="#organizations" onclick="org()">Organizations</a>
            <a href="#about" onclick="about()">About</a>
            <a href="#profile" onclick="profile()">Profile</a>
        </div>
    </footer>

    <script>
        function home() {
            window.location.href = 'HomePage.php'; 
        }
        function org() {
            window.location.href = 'DTP.php'; 
        }
        function about() {
            window.location.href = 'About.html'; 
        }
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = 'logout.php';
            }
        }
        document.querySelectorAll('.grid-item[data-org-name]').forEach(item => {
            item.addEventListener('click', () => {
                const orgName = item.getAttribute('data-org-name');
                document.getElementById('modalOrgName').textContent = orgName;
                document.getElementById('leaveModal').style.display = 'block';
            });
        });

        function closeModal() {
            document.getElementById('leaveModal').style.display = 'none';
        }

        function submitLeaveRequest() {
            const orgName = document.getElementById('modalOrgName').textContent;
            const reason = document.getElementById('leaveReason').value;
            const studentName = "<?php echo htmlspecialchars($row['name']); ?>";

            if (reason.trim() === "") {
                alert("Please provide a reason.");
                return;
            }

            fetch('submit_leave_request.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    organization: orgName,
                    reason: reason,
                    student_name: studentName 
                })
            }).then(response => response.text())
            .then(data => {
                alert(data); 
                if (data.includes("submitted successfully")) {
                    closeModal();
                }
            }).catch(error => console.error('Error:', error));
        }

    document.addEventListener('DOMContentLoaded', function () {
    const postsContainer = document.getElementById('postsContainer');
    const joinContainer = document.getElementById('joincontainer');
    const orgNameElement = document.getElementById('orgName');

    postsContainer.innerHTML = '';

    // ✅ Direct call for GMITS (no grid-item dependency)
    fetchPosts('GMITS');

    function fetchPosts(orgName) {
    fetch(`fetch_posts.php?organization=${encodeURIComponent(orgName)}`)
        .then((response) => response.json())
        .then((posts) => {
            console.log("Fetched posts for", orgName, posts);

            if (Array.isArray(posts) && posts.length > 0) {
                posts.forEach((post) => {
                    const postDiv = document.createElement("div");
                    postDiv.className = post.type;

                    const maxLength = 100;
                    const isTruncated = post.content.length > maxLength;
                    const truncatedContent = post.content.substring(0, maxLength) + (isTruncated ? "..." : "");

                    const postImage = document.createElement("img");
                    postImage.className = "post-image";
                    postImage.src = post.image || "uploads/default.jpg";
                    postImage.alt = post.title;

                    postDiv.appendChild(postImage); 

                    const postContent = document.createElement("div");
                    postContent.innerHTML = `
                        <strong>${post.title}</strong>
                        <span class="post-content">${truncatedContent}</span>
                        ${isTruncated ? '<a href="#" class="see-more">See more</a>' :""}
                        <div class="post-details">
                            <div>
                                <h3 class="details1">Location:</h3> ${post.location || 'N/A'}<br>
                            </div>
                            <div>
                                <h3 class="details2">Event date:</h3> ${post.from_date || 'N/A'} - ${post.to_date || 'N/A'}
                            </div>
                            <div>
                                <h3 class="details3">Time:</h3> ${post.event_time || 'N/A'}<br>
                            </div>
                        </div>
                        <div class="action-btn">
                            <button class="circle-button like-button" data-post-id="${post.id}"><i class="fa-regular fa-thumbs-up"></i></button>
                            <button class="circle-button comment-button" data-post-id="${post.id}"><i class="fa-regular fa-comment"></i></button>
                            <button class="circle-button notif-button"><i class="fa-regular fa-bell"></i></button>
                        </div>
                    `;
                    postDiv.appendChild(postContent);


                    // Recruitment-specific join button
                    if (post.type === "recruitment") {
                        const joinButton = document.createElement("button");
                        joinButton.textContent = "Join";
                        joinButton.className = "join-button";
                        joinButton.dataset.title = post.title;
                        joinButton.dataset.content = post.content;

                        joinButton.addEventListener("click", function () {
                            joinContainer.style.display = "block";
                            document.getElementById("orgName").textContent = orgName;
                            joinContainer.dataset.title = post.title;
                            joinContainer.dataset.content = post.content;
                        });

                        postDiv.appendChild(joinButton);
                    }

                    postsContainer.appendChild(postDiv);

                    const seeMoreLink = postDiv.querySelector(".see-more");
                    if (seeMoreLink) {
                        seeMoreLink.addEventListener("click", function (e) {
                            e.preventDefault();
                            postDiv.querySelector(".post-content").textContent = post.content;
                            this.remove();
                        });
                    }
                    // like button
                    const likeBtn = postDiv.querySelector(".like-button");
                    if (likeBtn) {
                        likeBtn.addEventListener("click", function () {
                            const postId = this.dataset.postId;
                            this.classList.toggle("active");
                            const liked = this.classList.contains("active");

                            fetch("like_post.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({ post_id: postId, like: liked })
                            })
                            .then((res) => res.json())
                            .then((data) => {
                                console.log("Like button clicked for post:", postId);
                            });
                        });
                    }
                    //comment button
                    const commentBtn = postDiv.querySelector(".comment-button");
                    if (commentBtn) {
                        commentBtn.addEventListener("click", function () {
                            const postId = this.dataset.postId;
                            const comment = prompt("Enter your comment:");
                            if (comment) {
                                fetch("comment_post.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({ post_id: postId, comment: comment })
                                })
                                .then((res) => res.json())
                                .then((data) => {
                                    alert("Comment added!");
                                });
                            }
                        });
                    }

                });
            } else {
                postsContainer.innerHTML = "<p>No approved posts found.</p>";
            }
        })
        .catch((error) => {
            console.error("Error fetching posts:", error);
            postsContainer.textContent = "Failed to load posts.";
        });
    }    

    function closeJoinContainer() {
        joinContainer.style.display = "none";
    }

    function submitJoinContainer() {
        const idNo = document.getElementById("idNoInput").value.trim();
        const orgName = orgNameElement.textContent.trim();
        const title = joinContainer.dataset.title;
        const content = joinContainer.dataset.content;

        if (!idNo || !orgName || !title || !content) {
            alert("Please fill out all required fields.");
            return;
        }

        fetch("join_activity.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                id_no: idNo,
                organization: orgName,
                title: title,
                content: content,
            }),
        })
            .then((response) => response.text())
            .then((data) => {
                alert(data);
                if (data.includes("successfully")) {
                    closeJoinContainer();
                }
            })
            .catch((error) => console.error("Error:", error));
    }

    document.querySelector(".ok-button").addEventListener("click", submitJoinContainer);
    document.querySelector(".cancel-button").addEventListener("click", closeJoinContainer);
});

    </script>
</body>
</html>

