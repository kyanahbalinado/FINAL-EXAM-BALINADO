<?php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'hr') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO job_posts (title, description, hr_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $description, $_SESSION['user_id']);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"], textarea {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        textarea {
            height: 150px;
        }
        input[type="text"]:focus, textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<header>
    <h1>Create Job Post</h1>
</header>

<div class="container">
    <a href="index.php" class="btn btn-primary">Back to Homepage</a>
    <form method="POST">
        <label for="title">Job:</label>
        <input type="text" id="title" name="title" required><br>
        
        <label for="description">Job Description:</label>
        <textarea id="description" name="description" required></textarea><br>
        
        <button type="submit">Post Job</button>
    </form>
</div>

</body>
</html>
