<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'orgportal';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $post_id = $conn->real_escape_string($_POST['post_id']);

    $id_no = $conn->real_escape_string($_POST['id_no']);
    $organization = $conn->real_escape_string($_POST['organization']);
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $check_sql = "SELECT * FROM user_activity 
                  WHERE id_no = '$id_no'
                  AND post_id = '$post_id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "You have already joined this activity!";
    } else {
        $student_sql = "SELECT name, department, year, course 
                        FROM student 
                        WHERE id_no='$id_no'";
        $student_result = $conn->query($student_sql);

        if ($student_result->num_rows > 0) {

            $student = $student_result->fetch_assoc();

            $name = $conn->real_escape_string($student['name']);
            $department = $conn->real_escape_string($student['department']);
            $year = $conn->real_escape_string($student['year']);
            $course = $conn->real_escape_string($student['course']);

            // INSERT with status = 'pending'
            $insert_sql = "INSERT INTO user_activity 
                           (id_no, post_id, name, department, year, course, organization, title, content, status)
                           VALUES 
                           ('$id_no', '$post_id', '$name', '$department', '$year', '$course', '$organization', '$title', '$content', 'pending')";

            if ($conn->query($insert_sql) === TRUE) {
                echo "Your request has been submitted and is now pending approval.";
            } else {
                echo "Error: " . $conn->error;
            }

        } else {
            echo "Student data not found!";
        }
    }

    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
