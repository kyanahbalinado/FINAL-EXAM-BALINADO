<?php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'hr') {
    header("Location: index.php");
    exit();
}

if (isset($_POST['action']) && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];
    $action = $_POST['action'];

    if ($action == 'accept') {
        $status = 'accepted';
        $message = 'Congratulations! You have been accepted for the position.';
    } elseif ($action == 'reject') {
        $status = 'rejected';
        $message = 'We are sorry, but your application has been rejected.';
    } else {
        header("Location: view_application.php?status=error");
        exit();
    }

    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $application_id);

    if ($stmt->execute()) {
        $stmt = $conn->prepare("SELECT applicant_id FROM applications WHERE id = ?");
        $stmt->bind_param("i", $application_id);
        $stmt->execute();
        $stmt->bind_result($applicant_id);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $applicant_id);
        $stmt->execute();
        $stmt->bind_result($applicant_email);
        $stmt->fetch();
        $stmt->close();

        $subject = "Application Status Update";
        $body = $message;
        $headers = "From: hr@company.com";

        mail($applicant_email, $subject, $body, $headers);

        header("Location: index.php?status=success");
    } else {
        header("Location: index.php?status=error");
    }

    $stmt->close();
} else {
    header("Location: view_application.php?status=error");
    exit();
}

$conn->close();
?>
