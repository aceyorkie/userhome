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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        #postsContainer {
            position: relative;
            margin: 20px auto;
            padding: 10px;
            max-width: 800px;
            font-family: "Roboto", sans-serif;
            clear: both;
            z-index: 1;
        }
        #postsContainer div {
            margin-bottom: 15px;
            padding: 20px;
            border-left: 5px solid maroon;
            background-color: #fdfdfd;
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            font-size: 15px;
            line-height: 1.6; 
            color: #333; 
            transition: transform 0.2s, box-shadow 0.2s; 
        }

        #postsContainer div:hover {
            transform: translateY(-3px); 
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15); 
        }

        #postsContainer .announcement {
            border-left-color: maroon;
            background-color: #fff5f5; 
        }

        #postsContainer .recruitment {
            position: relative;
            border-left-color: gold;
            background-color: #fffbea; 
        }
        #postsContainer div::before {
            content: "📢 ";
            font-size: 18px;
            margin-right: 8px;
        }
        #postsContainer .recruitment::after {
            content: "Join";
            display: inline-block;
            position: absolute; 
            top: 15px; 
            right: 10px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            background-color: #ffcc00;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        #postsContainer .recruitment:hover::after {
            background-color: #ffaa00; 
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-left">
            <img src="greetings/umdc-logo.png" alt="Logo" class="logo">
        </div>
        <div class="nav-right">
            <a href="profile.php">Profile</a>
        </div>
    </nav>

    <div id="postsContainer"></div>

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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const postsContainer = document.getElementById('postsContainer');
        
        const posts = <?php 
            $posts = [];
            while ($row = $result->fetch_assoc()) {
                $posts[] = [
                    'type' => $row['type'],
                    'title' => $row['title'],
                    'content' => $row['content']
                ];
            }
            echo json_encode($posts);
        ?>;

        if (posts.length > 0) {
            posts.forEach(post => {
                const postDiv = document.createElement('div');
                postDiv.className = post.type; 
                postDiv.innerHTML = `<strong>${post.title}</strong>: ${post.content}`;
                postsContainer.appendChild(postDiv);
            });
        } else {
            const noPostsDiv = document.createElement('div');
            noPostsDiv.textContent = 'No posts available.';
            postsContainer.appendChild(noPostsDiv);
        }
    });
    </script>

</body>
</html>
