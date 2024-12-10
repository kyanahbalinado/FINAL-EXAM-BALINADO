<?php
session_start();
include('db_connection.php');

if ($_SESSION['role'] != 'hr') {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT a.applicant_id, a.resume, a.description, u.username, a.id AS application_id, a.status FROM applications a INNER JOIN users u ON a.applicant_id = u.id");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Applicants</title>
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
        h2 {
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
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
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
    <h1>All Applicants</h1>
</header>

<div class="container">
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
            echo "<p style='color: green;'>Application processed successfully!</p>";
        } else {
            echo "<p style='color: red;'>An error occurred while processing the application. Please try again.</p>";
        }
    }
    ?>

    <a href="index.php" class="btn btn-primary">Back to Homepage</a>

    <table>
        <thead>
            <tr>
                <th>Applicant Name</th>
                <th>number or email</th>
                <th>Resume</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <a href="uploads/<?php echo htmlspecialchars($row['resume']); ?>" target="_blank">Download Resume</a>
                    </td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['status'] == 'pending') { ?>
                            <form action="process_application.php" method="POST" style="display:inline;">
                                <button type="submit" name="action" value="accept" class="btn btn-success" onclick="return confirm('Are you sure you want to accept this applicant?')">Accept</button>
                                <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>" />
                            </form>
                            <form action="process_application.php" method="POST" style="display:inline;">
                                <button type="submit" name="action" value="reject" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this applicant?')">Reject</button>
                                <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>" />
                            </form>
                        <?php } else { ?>
                            <em>Already <?php echo htmlspecialchars($row['status']); ?></em>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
