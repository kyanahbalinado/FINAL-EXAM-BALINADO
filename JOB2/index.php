<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role']; 
$user_id = $_SESSION['user_id'];

include('db_connection.php');

$chat_users = [];
if ($role == 'hr') {
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'applicant'");
} else {
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'hr'");
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $chat_users[] = $row;
}

$job_posts = [];
if ($role == 'applicant') {
    $stmt = $conn->prepare("SELECT a.status, j.title FROM applications a 
                            LEFT JOIN job_posts j ON a.job_post_id = j.id 
                            WHERE a.applicant_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $application_status = $row['status']; 
        if ($application_status == 'accepted') {
            $accepted_job_title = $row['title'];
        }
    }

    $stmt = $conn->prepare("SELECT id, title, description FROM job_posts");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $job_posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 24px;
        }

        nav {
            display: flex;
            justify-content: center;
            background-color: #4CAF50;
            padding: 15px;
        }

        nav a {
            color: white;
            padding: 14px 25px;
            text-decoration: none;
            font-size: 18px;
        }

        nav a:hover {
            background-color: #45a049;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .role-message {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
            color: #333;
        }

        .job-posts-board, .accepted-jobs, .rejected-jobs, .chat-section {
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #eef9f3;
        }

        h2, h3 {
            color: #4CAF50;
        }

        .job-posts-board table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .job-posts-board th, .job-posts-board td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .job-posts-board th {
            background-color: #4CAF50;
            color: white;
        }

        .job-posts-board td a {
            color: #4CAF50;
            font-weight: bold;
            text-decoration: none;
        }

        .job-posts-board td a:hover {
            text-decoration: underline;
        }

        .chat-users {
            list-style: none;
            padding-left: 0;
        }

        .chat-users li {
            margin-bottom: 12px;
        }

        .chat-users a {
            color: #4CAF50;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
        }

        .chat-users a:hover {
            text-decoration: underline;
        }

        .logout {
            text-align: center;
            margin-top: 30px;
        }

        .logout button {
            background-color: #d9534f;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .logout button:hover {
            background-color: #c9302c;
        }

        .accepted-jobs, .rejected-jobs {
            background-color: #d4edda;
        }

        .rejected-jobs {
            background-color: #f8d7da;
        }

        .accepted-jobs li, .rejected-jobs li {
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 6px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to the Kyanah Job Portal</h1>
</header>

<nav>
    <?php if ($role == 'hr'): ?>
        <a href="create_job_post.php">Create Job Post</a>
        <a href="view_applications.php">View Applications</a>
        <a href="view_messages.php">View Messages</a>
    <?php elseif ($role == 'applicant'): ?>
        <a href="message_hr.php">Message HR</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <div class="role-message">
        <?php if ($role == 'hr'): ?>
            <p>You are logged in as HR. You can post jobs, review applications, and interact with applicants.</p>
        <?php elseif ($role == 'applicant'): ?>
            <p>You are logged in as an Applicant. You can apply for jobs and message HR representatives.</p>
        <?php endif; ?>
    </div>

    <?php if ($role == 'applicant'): ?>
    <div class="job-posts-board">
        <h2>Available Job Posts</h2>
        <?php if (empty($job_posts)): ?>
            <p>No job posts available at the moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Description</th>
                        <th>Apply</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($job_posts as $job): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['description']); ?></td>
                            <td><a href="apply_job.php?job_post_id=<?php echo $job['id']; ?>">Apply</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($role == 'applicant'): ?>
    <div class="accepted-jobs">
        <?php
            $stmt = $conn->prepare("SELECT j.title FROM applications a 
                                    LEFT JOIN job_posts j ON a.job_post_id = j.id 
                                    WHERE a.applicant_id = ? AND a.status = 'accepted'");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0): ?>
                <h3>Accepted Job Titles</h3>
                <ul>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($row['title']); ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No accepted jobs yet.</p>
            <?php endif; ?>
    </div>

    <div class="rejected-jobs">
        <?php
            $stmt = $conn->prepare("SELECT j.title FROM applications a 
                                    LEFT JOIN job_posts j ON a.job_post_id = j.id 
                                    WHERE a.applicant_id = ? AND a.status = 'rejected'");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0): ?>
                <h3>Rejected Job Titles</h3>
                <ul>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($row['title']); ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No rejections yet.</p>
            <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="chat-section">
        <h2>Start a Chat</h2>
        <?php if (empty($chat_users)): ?>
            <p>No users available for chat at the moment.</p>
        <?php else: ?>
            <ul class="chat-users">
                <?php foreach ($chat_users as $user): ?>
                    <li>
                        <a href="chat.php?recipient_id=<?php echo $user['id']; ?>">
                            Chat with <?php echo htmlspecialchars($user['username']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
