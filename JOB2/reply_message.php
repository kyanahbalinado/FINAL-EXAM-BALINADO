<?php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'hr') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hr_reply = $_POST['hr_reply'];
    $message_id = $_POST['message_id'];
    $hr_id = $_SESSION['user_id'];

    $hr_reply = htmlspecialchars($hr_reply);

    $stmt = $conn->prepare("SELECT sender_id FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $stmt->bind_result($sender_id);
    $stmt->fetch();
    $stmt->close();

    if ($sender_id) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, parent_message_id) 
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $hr_id, $sender_id, $hr_reply, $message_id);

        if ($stmt->execute()) {
            header("Location: view_message.php?message_id=" . $message_id); 
            echo "<p>Error sending reply: " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Error: Invalid message.</p>";
    }
}
?>
