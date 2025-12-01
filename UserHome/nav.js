function home() {
    window.location.href = 'HomePage.php'; 
}
function org() {
    let course = "<?php echo $course; ?>";

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
function about() {
    window.location.href = 'About.html'; 
}
function confirmLogout() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = 'logout.php';
    }
}