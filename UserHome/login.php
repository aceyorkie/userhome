<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/officerDashboardCopy/css/Login.css">
</head>
<body>
    <div class="text_primary">
        <h1>Be Part of Something <span class="text_main">Bigger</span>  and Unlock Your <span class="text_main">Potential</span>.</h1>
        <h2>Join OrgPortal and unlock a world of opportunities! Discover organizations, connect with like-minded individuals, explore events tailored to your interests. </h2>
    </div>
    <div class="form-container">
        <img src="greetings/umdc-logo.png" alt="Logo" class="logo">
        <h2>Login</h2>
        <form action="login_process.php" method="POST">
            ID No.: <input type="text" name="id_no" required><br>
            Password: <input type="password" name="password" required><br>
        <input type="submit" value="Login">
    </form>

    <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>
    
    
    
</body>
</html>
