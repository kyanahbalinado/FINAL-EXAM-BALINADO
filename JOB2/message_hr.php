<?php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'applicant') {
    header("Location: index.php");
    exit();
}

$hr_id = 2;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $message = htmlspecialchars($message);

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $_SESSION['user_id'], $hr_id, $message);

    if ($stmt->execute()) {
        echo "<p>Message sent successfully.</p>";
    } else {
        echo "<p>Error sending message: " . $conn->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message HR</title>
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
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            font-size: 24px;
        }
        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            resize: vertical;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            text-decoration: none;
            color: #333;
        }
        a:hover {
            color: #4CAF50;
        }
    </style>
</head>
<body>

<header>
    <h1>Send Message to HR</h1>
</header>

<div class="container">
    <form method="POST">
        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" required></textarea>

        <button type="submit">Send Message</button>
    </form>

    <a href="index.php">Back to Homepage</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
