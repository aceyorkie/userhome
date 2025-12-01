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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_no = $_POST['id_no'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $year = $_POST['year'];
    $course = $_POST['course'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_query = "SELECT id_no FROM student WHERE id_no='$id_no'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "ID No. already exists.";
    } else {
        $sql = "INSERT INTO student (id_no, name, department, year, course, password) VALUES ('$id_no', '$name', '$department', '$year', '$course', '$password')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['id_no'] = $id_no;
            $_SESSION['name'] = $name;
            $_SESSION['department'] = $department;
            $_SESSION['year'] = $year;
            $_SESSION['course'] = $course;

            if ($course === "BS IN INFORMATION TECHNOLOGY") {
                header("Location: DTP.php");
            } else {
                header("Location: profile.php");
            }
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
