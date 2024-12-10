<?php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'hr') {
    header("Location: index.php");
    exit();
}

$hr_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT messages.id, messages.sender_id, messages.message, messages.created_at, users.username AS sender_name FROM messages INNER JOIN users ON messages.sender_id = users.id WHERE messages.receiver_id = ? ORDER BY messages.created_at DESC");
$stmt->bind_param("i", $hr_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR - View Messages</title>
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
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            font-size: 24px;
        }
        .btn {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        td {
            word-wrap: break-word;
            max-width: 300px;
        }
        td a {
            color: #007bff;
        }
        td a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <h1>Messages from Applicants</h1>
</header>

<div class="container">
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Message</th>
                    <th>Sent At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['sender_name']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No messages from applicants yet.</p>
    <?php endif; ?>

    <br>
    <a href="index.php" class="btn btn-primary">Back to Homepage</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
