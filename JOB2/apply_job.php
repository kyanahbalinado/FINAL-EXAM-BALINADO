<?php
session_start();
include('db_connection.php');

$upload_dir = "uploads/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SESSION['role'] != 'applicant') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resume = $_FILES['resume']['name'];
    $description = $_POST['description'];

    if ($_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        $resume_path = $upload_dir . basename($resume);

        if (move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
            $stmt = $conn->prepare("INSERT INTO applications (job_post_id, applicant_id, resume, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss",$_GET['job_post_id'], $_SESSION['user_id'], $resume, $description);
            $stmt->execute();

            header("Location: index.php");
            exit();
        } else {
            echo "Failed to upload the resume. Please try again.";
        }
    } else {
        echo "Error uploading the resume.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
        nav {
            display: flex;
            justify-content: center;
            background-color: #4CAF50;
            padding: 10px;
        }
        nav a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            text-align: center;
        }
        nav a:hover {
            background-color: #ddd;
            color: black;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-size: 16px;
            display: block;
            margin-bottom: 5px;
        }
        textarea, input[type="file"], button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>Job Application</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="message_hr.php">Message HR</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <h2>Apply for a Job</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="description">number or email:</label>
            <textarea name="description" id="description" required></textarea>
        </div>

        <div class="form-group">
            <label for="resume">Resume (PDF):</label>
            <input type="file" name="resume" id="resume" accept=".pdf" required>
        </div>

        <button type="submit">Apply</button>
    </div>
</div>

</body>
</html>
