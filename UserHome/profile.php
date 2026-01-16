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

// FIRST LOOP – store orgs for JavaScript
$userOrgs = [];
if ($org_result->num_rows > 0) {
    while ($org_row = $org_result->fetch_assoc()) {
        $userOrgs[] = $org_row['organization_name'];
    }
}

// RESET QUERY so HTML loop works
$org_result = $conn->query($org_sql);

$course = '';
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $course = $row['course'];
}

require_once 'phpqrcode/qrlib.php';

$qrDir = 'qrcodes/';
if (!file_exists($qrDir)) {
    mkdir($qrDir, 0777, true);
}

$qrData = "ID: " . $row['id_no'] . "\nName: " . $row['name'] . "\nCourse: " . $row['course'];

$qrFile = $qrDir . $row['id_no'] . '.png';

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
    <link rel="stylesheet" href="/userHomeCopy/UserHome/css/profile.css">
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
                <h1><?php echo htmlspecialchars($row['name']); ?></h1>
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
                <h1><?php echo htmlspecialchars($row['name']); ?></h1>

            </div>
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
            <div style='display: flex; align-items: center; gap: 20px;'>
                <form id='uploadForm' action='upload.php' method='post' enctype='multipart/form-data'>
                    <label for='profileInput'>
                        <img src='$profileImage' alt='Profile Picture' class='profile-img' style='cursor: pointer; border-radius: 50%; width: 110px; height: 110px; object-fit: cover;'>
                    </label>
                    <input type='file' id='profileInput' name='profile_image' style='display: none;' onchange='document.getElementById(\"uploadForm\").submit();'>
                </form>

                <div class='profile-name'style='position: relative; display: flex; align-items: center; gap: 10px;'>
                    <div>
                        <h3>$id_no</h3>
                        <h1>$name</h1>
                        <h3 style='font-weight: 400'>$course</h3>
                    </div>

                    <svg class='settings-icon' onclick='openSettingsModal()' 
                        style='width: 28px; height: 28px; cursor: pointer; margin-left: 10px;'
                        viewBox='0 0 512 512' fill='#444'>
                        <path d='M487.4 315.7l-42.5-24.5c2.6-14.1 2.6-28.7 0-42.8l42.5-24.5c7.7-4.4 11-14 7.7-22.2l-45.3-96.7c-3.8-8.1-13-12.1-21.4-9.3l-49.3 16.9c-10.9-9-23-16.4-36.1-22l-7.4-52.1C333.3 8.3 324.1 0 313.1 0h-114c-11 0-20.2 8.3-21.4 19.2l-7.4 52.1c-13.1 5.6-25.2 13-36.1 22L85 63.7c-8.3-2.8-17.6 1.2-21.4 9.3L18.3 169.7c-3.3 8.2 0 17.8 7.7 22.2l42.5 24.5c-2.6 14.1-2.6 28.7 0 42.8l-42.5 24.5c-7.7 4.4-11 14-7.7 22.2l45.3 96.7c3.8 8.1 13 12.1 21.4 9.3l49.3-16.9c10.9 9 23 16.4 36.1 22l7.4 52.1c1.2 10.9 10.4 19.2 21.4 19.2h114c11 0 20.2-8.3 21.4-19.2l7.4-52.1c13.1-5.6 25.2-13 36.1-22l49.3 16.9c8.3 2.8 17.6-1.2 21.4-9.3l45.3-96.7c3.1-8.2-.2-17.8-7.9-22.2zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z'/>
                    </svg>
                </div>
            </div>

            <div class='qr-code'>
                <img src='$qrFile' alt='QR Code' style='width:120px; height:120px;'>
            </div>
        </div>";
    
        echo "<div id='settingsModal' class='settings-modal-overlay'>
            <div class='settings-modal-box'>
                <h2>Settings</h2>

                <button class='settings-btn'>About</button>
                <button class='settings-btn' onclick='logout()'>Log out</button>
                <button class='settings-cancel-btn' onclick='closeSettingsModal()'>Cancel</button>
            </div>
        </div>";

        echo "<div class='grid-container'>";
            echo "<div class='grid-item my-activities-btn' style='background-color: maroon;'>
                    <div class='activity-content'>
                        <i class='fa-regular fa-rectangle-list'></i>
                        <h1>My Activities</h1>
                    </div>
                </div>";

            if ($org_result->num_rows > 0) {
                while ($org_row = $org_result->fetch_assoc()) {
                    $org_name = htmlspecialchars($org_row['organization_name']);
                    echo "<div class='grid-item  org-box'data-org-name='$org_name'>
                        <h4 class='title-with-arrow'>
                            <span class='text'>Click to view details</span>
                            <span class='arrow-icon'>&#8599;</span>
                        </h4>
                        <div class='org-name'>
                            <i class='fa-solid fa-users'></i>
                            <span>$org_name</span>
                        </div>
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
                    <div class='empty-box'>
                        <i class='fa-regular fa-circle-xmark'></i>
                        <span>EMPTY</span>
                    </div>
                </div>";
            }
            echo "</div>";
        ?>

        <div id="orgDetailsModal" class="modal-overlay">
            <div class="modal-box">
                
                <button class="modal-close-btn" onclick="closeOrgModal()">✕</button>

                <h2 id="orgDetailsName" class="modal-title"></h2>
                <p id="orgDetailsDesc" class="modal-description"></p>

                <label class="modal-label">Reason for leaving</label>
                <textarea id="leaveReasonInput" class="modal-textarea" placeholder="Type your reason here..."></textarea>

                <button id="leaveButton" class="modal-leave-btn">
                    Leave Organization
                </button>

            </div>
        </div>

        <div class="line-separator">
            <h1 class="section-title">
                Upcoming & <br>Ongoing
                <span class="color">Events</span>
            </h1>

            <h3 class="subtitle">
                These activities and events are thoughtfully designed to educate and inspire participants.
            </h3>
        </div>

        <div class="postsWrapper">
            <div id="postsContainer"></div>
        </div>
    </div>

    <div id="overlay"></div>
    <div id="joincontainer">
        <p>Enter your ID NO. to join <span id="orgName"></span> activities:</p>
        <input type="text" id="idNoInput" placeholder="Your ID number">
        <br>
        <button class="ok-button" onclick="submitJoinContainer()">OK</button>
        <button class="cancel-button" onclick="closeJoinContainer()">Cancel</button>
    </div>

    <script>
        const userOrganizations = <?php echo json_encode($userOrgs); ?>;
    </script>

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
        function logout() {
            window.location.href = "logout.php";
        }


        document.addEventListener("DOMContentLoaded", () => {
            const myActivities = document.querySelector(".my-activities-btn");

            if (myActivities) {
                myActivities.addEventListener("click", () => {
                    window.location.href = "activities.php";
                });
            }
        });

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

        document.addEventListener('DOMContentLoaded', function () {
        const postsContainer = document.getElementById('postsContainer');
        const joinContainer = document.getElementById('joincontainer');
        const orgNameElement = document.getElementById('orgName');

        postsContainer.innerHTML = '';

        if (userOrganizations.length > 0) {
            userOrganizations.forEach(org => {
                fetchPosts(org);
            });
        } else {
            postsContainer.innerHTML = "<p>You are not enrolled in any organization. Join one to see posts.</p>";
        }


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
                        `;
                        postDiv.appendChild(postContent);

                        if (post.type === "recruitment") {
                            const joinButton = document.createElement("button");
                            joinButton.textContent = "Join";
                            joinButton.className = "join-button";
                            joinButton.dataset.title = post.title;
                            joinButton.dataset.content = post.content;
                            joinButton.dataset.postId = post.id;

                            joinButton.addEventListener("click", function () {
                                joinContainer.style.display = "block";
                                document.getElementById("overlay").style.display = "block";
                                document.getElementById("orgName").textContent = orgName;
                                joinContainer.dataset.title = post.title;
                                joinContainer.dataset.content = post.content;
                                joinContainer.dataset.postId = post.id; 
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
            document.getElementById("overlay").style.display = "none";
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
                    post_id: joinContainer.dataset.postId 
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

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".org-box").forEach(box => {
                box.addEventListener("click", () => {
                    const orgName = box.dataset.orgName;

                    // Fetch organization details
                    fetch(`get_org_details.php?organization=${encodeURIComponent(orgName)}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById("orgDetailsName").textContent = data.name;
                        document.getElementById("orgDetailsDesc").textContent = data.description;
                        document.getElementById("leaveButton").dataset.orgName = data.name;

                        document.getElementById("orgDetailsModal").style.display = "flex";
                    });
                });
            });

        });

        // Close modal
        function closeOrgModal() {
            document.getElementById("orgDetailsModal").style.display = "none";
        }

        // Leave Organization
        document.getElementById("leaveButton").addEventListener("click", () => {
            const orgName = document.getElementById("leaveButton").dataset.orgName;
            const reason = document.getElementById("leaveReasonInput").value.trim();
            const studentName = "<?php echo $row['name']; ?>";

            if (reason === "") {
                alert("Please enter a reason before leaving.");
                return;
            }

            fetch("submit_leave_request.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    organization: orgName,
                    reason: reason,
                    student_name: studentName
                })
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                if (msg.includes("successfully")) {
                    closeOrgModal();
                    location.reload();
                }
            });
        });


    </script>

    <script>
        function openSettingsModal() {
            document.getElementById("settingsModal").style.display = "flex";
        }

        function closeSettingsModal() {
            document.getElementById("settingsModal").style.display = "none";
        }

        // Close when clicking outside
        document.addEventListener("click", function(e) {
            const modal = document.getElementById("settingsModal");
            const box = document.querySelector(".settings-modal-box");

            if (e.target === modal && !box.contains(e.target)) {
                closeSettingsModal();
            }
        });
    </script>
</body>
</html>

