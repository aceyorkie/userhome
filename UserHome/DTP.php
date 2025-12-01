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
$id_no = $_SESSION['id_no'] ?? null;
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
  <link rel="stylesheet" href="css/DTP.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
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

  
  </style>
</head>
<body>

  <!-- NAVIGATION PART -->

  <nav>
    <div class="nav-left">
      <img src="greetings/umdc-logo.png" alt="Logo" class="logo">
    </div>
    <div class="nav-center">
      <a href="#profile" onclick="profile()">Dashboard</a> 
      <a href="#organizations" onclick="org()">Academic</a>
      <a href="nonacad.html">Non-Acadademic</a>
      <a href="#about" onclick="about()">About</a>
    </div>
    <div class="nav-right">
    <button class="button">
    <svg viewBox="0 0 448 512" class="bell"><path d="M224 0c-17.7 0-32 14.3-32 32V49.9C119.5 61.4 64 124.2 64 200v33.4c0 45.4-15.5 89.5-43.8 124.9L5.3 377c-5.8 7.2-6.9 17.1-2.9 25.4S14.8 416 24 416H424c9.2 0 17.6-5.3 21.6-13.6s2.9-18.2-2.9-25.4l-14.9-18.6C399.5 322.9 384 278.8 384 233.4V200c0-75.8-55.5-138.6-128-150.1V32c0-17.7-14.3-32-32-32zm0 96h8c57.4 0 104 46.6 104 104v33.4c0 47.9 13.9 94.6 39.7 134.6H72.3C98.1 328 112 281.3 112 233.4V200c0-57.4 46.6-104 104-104h8zm64 352H224 160c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7s18.7-28.3 18.7-45.3z"></path></svg>
    </button>
    </div>
  </nav>

  <!-- ORGANIZATION -->

  <div class="body-container"> 
    <div class="text2">
      <img class="dept_img" src="uploads/dtplogo.jpg">
      <h2 class="descrip">INFORMATION TECHNOLOGY</h2>
      <h1 class="rep">organizations</h1>
      <h3 class="what">Introducing IT Organizations: a student resource for essential skills, <br> career readiness, and tailored support. </h3>
    </div>

    <div class="OrgHeader">
      <h2>Academic <span>Organizations</span></h2>
      <button class="arrow arrow-left"><i class="fas fa-chevron-left"></i></button> <button class="arrow arrow-right"><i class="fas fa-chevron-right"></i></button>
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
                <button class="view-button animated-button" onclick="openAboutOrganization('<?php echo htmlspecialchars($org['org_code']); ?>_about')">
                  <span>About</span>
                </button>
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

  <script>
    const slider = document.getElementById('slider');
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;
    let currentIndex = 0;

    const orgHeader = document.querySelector('.OrgHeader h2');

    const updateSlider = () => {
      slider.style.transform = `translateX(-${currentIndex * 100}%)`;

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

    <div id="overlay"></div>
    <div id="joincontainer">
        <p>Enter your ID NO. to join <span id="orgName"></span>:</p>
        <input type="text" id="idNoInput" placeholder="Your ID number">
        <br>
        <button class="ok-button" onclick="submitJoinContainer()">OK</button>
        <button class="cancel-button" onclick="closeJoinContainer()">Cancel</button>
    </div>

    <div id="descriptionBox1" class="aboutcontainer">
        <div class="circle-container">
            <div class="circle">
                <img src="org_logo/gmits_logo.jpg">
            </div>
            <div> 
                <h1 class="orgname">GMITS</h1>
                <h3 class="orgdes">The Graphics Media of Information Technology Students (GMITS) helps students explore and develop their skills in digital media and creative technologies.</h3>
                <h1 class="orgname">LEADERS</h1>
            </div>
            <button class="cancel-button" onclick="closeAboutContainer()">Close</button>
        </div>
    </div>
    
    <div id="descriptionBox2" class="aboutcontainer">
        <div class="circle-container">
            <div class="circle">
                <img src="org_logo/pgits_logo.jpg">
            </div>
            <div> 
                <h1 class="orgname">PGITS</h1>
                <h3 class="orgdes">The Programmers’ Guild in Information Technology Students (PGITS) focuses on developing students' programming and problem-solving skills.</h3>
                <h1 class="orgname">LEADERS</h1>
            </div>
            <button class="cancel-button" onclick="closeAboutContainer()">Cancel</button>
        </div>
    </div>

    <!-- FOOTER -->

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
            window.location.href = 'DTP.html';
        }
        function profile() {
            window.location.href = 'profile.php'; 
        }
        // function aboutOrganization(descriptionBoxId) {
        //     document.getElementById(descriptionBoxId).style.display = 'block';
        // }
        // function closeBox(boxId) {
        //     document.getElementById(boxId).style.display = 'none';
        // }

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

    <!-- RASA CHATBOT -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chat Design with Rasa</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    .chat-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 60px;
      height: 60px;
      background-color: #800000;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      transition: background-color 0.3s;
    }

    .chat-button:hover {
      background-color: yellow;
      color: black;
    }

    .chat-box {
      position: fixed;
      bottom: 90px;
      right: 20px;
      width: 300px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      display: none;
      flex-direction: column;
      overflow: hidden;
      z-index: 999;
    }

    .chat-header {
      background-color: rgba(0, 0, 0, 0.3);
      background: linear-gradient(to right, maroon, gold);
      color: white;
      padding: 15px;
      font-weight: bold;
    }

    .chat-messages {
      padding: 20px;
      height: 380px;
      overflow-y: auto;
      background: #ffffff;
      font-size: 14px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .message {
      max-width: 80%;
      padding: 10px 14px;
      border-radius: 18px;
      line-height: 1.4;
      word-wrap: break-word;
      display: inline-block;
    }

    .user-message {
      align-self: flex-end;
      background-color: #e0e0e0;
      color: #000;
      border-top-right-radius: 0;
    }

    .bot-message {
      align-self: flex-start;
      background-color: maroon;
      color: white;
      border-top-left-radius: 0;
    }

    .chat-input {
      display: flex;
      border-top: 1px solid #ddd;
    }

    .chat-input input {
      flex: 1;
      padding: 10px;
      border: none;
      outline: none;
    }

    .chat-input button {
      padding: 10px 15px;
      background-color: #800000;
      border: none;
      color: white;
      cursor: pointer;
    }

    .chat-input button:hover {
      background-color: yellow;
      color: black;
    }

    .suggestion-button {
      background-color: #f0f0f0;
      color: #333;
      padding: 8px 12px;
      margin: 5px 0;
      border-radius: 16px;
      display: inline-block;
      cursor: pointer;
      font-size: 13px;
      max-width: fit-content;
      transition: background-color 0.3s;
    }

    .suggestion-button:hover {
      background-color: #e0e0e0;
    }

    .bot-gif {
      text-align: center;
    }

    .bot-gif img {
      height: 120px;
      width: 120px;
    }

    .typing-indicator {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 4px;
      padding: 10px 14px;
      background-color: maroon;
      color: white;
      border-radius: 18px;
      border-top-left-radius: 0;
      max-width: fit-content;
      animation: fadeIn 0.3s ease-in;
    }

    .typing-indicator span {
      height: 8px;
      width: 8px;
      background-color: white;
      border-radius: 50%;
      display: inline-block;
      animation: blink 1.2s infinite;
    }

    .typing-indicator span:nth-child(2) {
      animation-delay: 0.2s;
    }
    .typing-indicator span:nth-child(3) {
      animation-delay: 0.4s;
    }

    @keyframes blink {
      0% { opacity: 0.2; }
      20% { opacity: 1; }
      100% { opacity: 0.2; }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }


  </style>
</head>
<body>

  <div class="chat-button" onclick="toggleChat()">💬</div>

  <div class="chat-box" id="chatBox">
    <div class="chat-header">FAQs</div>
    <div class="chat-messages" id="chatMessages"> </div>
    <div class="chat-input">
      <input type="text" id="userInput" placeholder="Type a message..." />
      <button onclick="sendMessage()">Send</button>
    </div>
  </div>

  <script>
    function toggleChat() {
      const chatBox = document.getElementById("chatBox");
      const chatMessages = document.getElementById("chatMessages");

      if (chatBox.style.display === "flex") {
        chatBox.style.display = "none";
      } else {
        chatBox.style.display = "flex";
        chatMessages.innerHTML = ""; // delete message

        const botGif = document.createElement("div");
        botGif.className = "bot-gif";
        botGif.innerHTML = '<img src="images/BOT.gif" alt="Bot" />';
        chatMessages.appendChild(botGif);
        showRecommendedQuestions();  // display random suggestions
      }
    }

    function showRecommendedQuestions() {
      const chatMessages = document.getElementById("chatMessages");
      chatMessages.innerHTML += `<div class="message bot-message">Try asking me any of the following:</div>`;

      // recommendation nga questions
      const questionSets = [
        [
          "Tell me about PGITS",
          "Tell me the activities PGITS has done recently?",
          "Can I join PGITS even if I'm not part of the IT department?"
        ],
        [
          "What is the purpose of PGITS?",
          "What will I gain as a PGITS member?",
          "Any workshops available?"
        ],
        [
          "Do I need to be an expert to be in PGITS?",
          "How can I become a member of PGITS?",
          "Why should I join PGITS?"
        ],
        [
          "Who are the officers of GMITS?",
          "Is it okay to join PGITS even if I'm not good at programming yet?",
          "Is PGITS connected with the IT department?"
        ]
      ];

      const randomSet = questionSets[Math.floor(Math.random() * questionSets.length)];

      randomSet.forEach((text) => {
        const suggestionBtn = document.createElement("div");
        suggestionBtn.className = "suggestion-button";
        suggestionBtn.innerText = text;
        suggestionBtn.onclick = () => {
          document.getElementById("userInput").value = text;
          sendMessage();
        };
        chatMessages.appendChild(suggestionBtn);
      });

      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    async function sendMessage() {
      const input = document.getElementById("userInput");
      const message = input.value.trim();
      const chatMessages = document.getElementById("chatMessages");

      if (!message) return;

      // display message
      const userBubble = document.createElement("div");
      userBubble.className = "message user-message";
      userBubble.innerText = message;
      chatMessages.appendChild(userBubble);
      input.value = "";
      chatMessages.scrollTop = chatMessages.scrollHeight;

      // typing indicator nga animation
      const typingIndicator = document.createElement("div");
      typingIndicator.className = "message bot-message typing-indicator";
      typingIndicator.id = "typing-indicator";
      typingIndicator.innerHTML = "<span></span><span></span><span></span>";
      chatMessages.appendChild(typingIndicator);
      chatMessages.scrollTop = chatMessages.scrollHeight;

      // Send message
      const response = await fetch("http://localhost:5005/webhooks/rest/webhook", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ sender: "user1", message: message })
      });

      const data = await response.json();

      // delay sa typing ni siya
      setTimeout(() => {
        const typingElem = document.getElementById("typing-indicator");
        if (typingElem) typingElem.remove();

        if (data && data.length > 0) {
          data.forEach(res => {
            const botBubble = document.createElement("div");
            botBubble.className = "message bot-message";
            botBubble.innerText = res.text;
            chatMessages.appendChild(botBubble);
          });
        } else {
          const fallbackBubble = document.createElement("div");
          fallbackBubble.className = "message bot-message";
          fallbackBubble.innerText = "Sorry, I didn’t get that.";
          chatMessages.appendChild(fallbackBubble);
        }

        chatMessages.scrollTop = chatMessages.scrollHeight;
      }, 2000); 
    }
  </script>
</body>
</html>