<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$connection = new mysqli("localhost", "root", "", "hrms");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get current user's email from usersignup table
$user_id = $_SESSION['user_id'];
$email_query = "SELECT email FROM usersignup WHERE id = ?";
$stmt = $connection->prepare($email_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$email_result = $stmt->get_result();
$user_email = $email_result->fetch_assoc()['email'];

// Fetch user data from both tables using email
$sql = "SELECT 
            u.id, u.Name, u.Address, u.Gender, u.Contact, u.dob, u.username, u.email, u.status,
            e.department, e.job_position, e.qualification, e.join_date, e.profile_picture,
            e.active_status, e.cv
        FROM usersignup u
        LEFT JOIN employee e ON u.email = e.email
        WHERE u.id = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found");
}

$user = $result->fetch_assoc();

// Function to format date
function formatDate($date)
{
    return date('d M Y', strtotime($date));
}

// Function to get profile image path
function getProfileImagePath($profile_picture)
{
    if (!empty($profile_picture) && $profile_picture !== 'uploads/default.png') {
        $path = $profile_picture;
        if (file_exists($path)) {
            return $path;
        }
    }
    return 'uploads/default.png';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css">
    <title>Employee Profile</title>

    <style>
        /* * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
        }*/

        .pic {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        } 

        .profile-header {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 3px solid #f0f0f0;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-header h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .profile-header p {
            color: #666;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }

        .col-md-6 {
            flex: 1;
            min-width: 300px;
        }

        .info-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .info-card h4 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            font-size: 20px;
        }

        .info-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 16px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Status badge styles */
        .status-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .status-active {
            background-color: #28a745;
            color: white;
        }

        .status-inactive {
            background-color: #dc3545;
            color: white;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }

            .col-md-6 {
                width: 100%;
            }

            .container {
                padding: 10px;
            }

            .profile-header {
                padding: 20px;
            }
        }

        .profile-header {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 1rem;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #212529;
            font-weight: 500;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container">

        <!-- Include Sidebar -->
        <?php include 'user_sidebar.php'; ?>

        <script>
            document.querySelector('a[href="user_profile.php"]').classList.add('active-page');
        </script>

        <main>
            <header>
                <h1>Details</h1>
            </header>
            <div class="pic">
                <div class="profile-header position-relative">
                    <div class="profile-picture">
                        <img src="<?php echo htmlspecialchars(getProfileImagePath($user['profile_picture'])); ?>"
                            alt="Profile Picture">
                    </div>

                    <!-- Status Badges -->


                    <div class="text-center">
                        <h2 class="mb-1"><?php echo htmlspecialchars($user['Name']); ?></h2>
                        <p class="text-muted mb-2"><?php echo htmlspecialchars($user['job_position'] ?? 'Position not set'); ?></p>
                        <p class="text-muted"><?php echo htmlspecialchars($user['department'] ?? 'Department not set'); ?></p>
                    </div>
                </div>

                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6 mb-4">
                        <div class="info-card">
                            <h4 class="mb-4">Personal Information</h4>

                            <div class="mb-3">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Username</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Contact</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['Contact'] ?? 'Not provided'); ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Gender</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['Gender'] ?? 'Not specified'); ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">
                                    <?php echo $user['dob'] ? formatDate($user['dob']) : 'Not provided'; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['Address'] ?? 'Not provided'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="col-md-6 mb-4">
                        <div class="info-card">
                            <h4 class="mb-4">Professional Information</h4>

                            <div class="mb-3">
                                <div class="info-label">Department</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['department'] ?? 'Not assigned'); ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Job Position</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['job_position'] ?? 'Not assigned'); ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Qualification</div>
                                <div class="info-value"><?php echo htmlspecialchars($user['qualification'] ?? 'Not provided'); ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="info-label">Join Date</div>
                                <div class="info-value">
                                    <?php echo $user['join_date'] ? formatDate($user['join_date']) : 'Not recorded'; ?>
                                </div>
                            </div>

                            <?php if (!empty($user['cv'])): ?>
                                <div class="mb-3">
                                    <div class="info-label">CV</div>
                                    <div class="info-value">
                                        <a href="<?php echo htmlspecialchars($user['cv']); ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            target="_blank">View CV</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>

</html>