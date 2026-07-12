<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'PHP/db_conn.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$role = $_SESSION['role'] ?? null;
$userID = $_SESSION['userID'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }

    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    $username = $_POST['username'];

    if (!preg_match('/^[a-zA-Z0-9_ -]{1,50}$/', $username)) {
    http_response_code(400);
    exit('Invalid username');
}
    $petName = $_POST['petName'];
    $petGender = $_POST['petGender'];
    $petAge = $_POST['petAge'];
    if (!ctype_digit($petAge)) {
    http_response_code(400);
    exit('Invalid pet age!');
    }
    $petAge = (int) $petAge;

    if ($petAge < 0 || $petAge > 50) {
        http_response_code(400);
        exit('Invalid pet age! ');
    }

    $petSpecies = $_POST['petSpecies'];

    if (!empty($newPassword) && $newPassword === $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE User SET password='$hashedPassword' WHERE userID=$userID");
    }

    mysqli_query($conn, "UPDATE User SET username='$username' WHERE userID=$userID");
    mysqli_query($conn, "UPDATE Customer SET petName='$petName', petGender='$petGender', petAge ='$petAge', petSpecialInfo ='$petSpecies' WHERE customerID=$userID");

    $message = "Profile Updated Successfully!!";
}


$sql = "SELECT u.username, u.email, c.petName, c.petGender, c.petSpecialInfo, c.petAge FROM User u
        LEFT JOIN Customer c ON u.userID = c.customerID
        WHERE u.userID = $userID";

$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
mysqli_close($conn);
?>



<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/profile.css">
</head>

<body>

    <header class="header">
        <div class="main-header">
            <div class="container d-flex align-items-center justify-content-between">
                <button class="menu-toggle">☰</button>
                <div class="logo">
                    <a href="index.php">
                        <img src="img/logo-left.png" alt="VetAssistLogo" style="height: 70%; width: 70%;" class="img-fluid">
                    </a>
                </div>
                <ul class="text-right d-flex align-items-center m-0 p-0">
                    <li class="dropdown">
                        <a href="#" class="account-btn" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>
                            <i class="fas fa-caret-down ml-1"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right custom-dropdown" aria-labelledby="dropdownMenuLink">
                            <li><a href="profile.php">User Profile</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="display-area">
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        ?>
                <div class="sidebar">
            <ul>

                <li>
                    <a class="sidebar-dashboard <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>&nbsp;&nbsp;&nbsp;&nbsp;Dashboard
                        </a>
                </li>

                <?php if ($role === 'Customer'): ?>
                    <li>
                        <a class="sidebar-appointment <?php echo ($currentPage == 'appointment.php') ? 'active' : ''; ?>" href="appointment.php">
                            <i class="fas fa-calendar-check fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Appointment
                        </a>
                    </li>
                    <li>
                        <a class="sidebar-payments <?php echo ($currentPage == 'payments.php') ? 'active' : ''; ?>" href="payments.php">
                            <i class="fas fa-credit-card fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Payments
                        </a>
                    </li>
                    <li>
                        <a class="sidebar-medical <?php echo ($currentPage == 'medrecord.php') ? 'active' : ''; ?>" href="medrecord.php">
                            <i class="fas fa-file-medical fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Medical Records
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'Staff' || $role === 'Doctor' || $role === 'Admin'): ?>
                    <li>
                        <a class="sidebar-management <?php echo ($currentPage == 'management.php') ? 'active' : ''; ?>" href="management.php">
                            <i class="fas fa-tools fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Management
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'Staff'): ?>
                    <li>
                        <a class="sidebar-payments <?php echo ($currentPage == 'payments.php') ? 'active' : ''; ?>" href="payments.php">
                            <i class="fas fa-credit-card fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Payments
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'Admin'): ?>
                    <li>
                        <a class="sidebar-newstaff <?php echo ($currentPage == 'newstaff.php') ? 'active' : ''; ?>" href="newstaff.php">
                            <i class="fas fa-user-plus fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;New Staff
                        </a>
                    </li>
                <?php endif; ?>

                <li>
                    <a class="sidebar-profile <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>" href="profile.php">
                        <i class="fas fa-user-cog fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Profile Settings
                    </a>
                </li>

                <li>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Logout
                    </a>
                </li>
            </ul>
        </div>




        <div class="main-content">
            <form method="POST" onsubmit="return validatePassword();">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="component-area">
                    <div style="font-size: x-large; font-weight: bold;">USER PROFILE</div>

                    <?php if ($message): ?>
                        <p style="color: green;"><?php echo $message; ?></p>
                    <?php endif; ?>

                    <div class="opt-profile">
                        <p>Email: <span id="user-email"><?php echo htmlspecialchars($user['email']); ?></span></p>
                        <p>UID: <span id="user-id"><?php echo $userID; ?></span></p>
                    </div>

                    <div class="change-profile">

                        <div class="form-row">
                            <label for="password">New Password:</label>
                            <input type="password" name="newPassword" id="password" class="form-input"
                                placeholder="at least 8 characters" data-bs-toggle="tooltip" data-bs-placement="right" title="Password must include uppercase, lowercase, number, and symbol (with at least 8 characters)">
                        </div>

                        <div class="form-row">
                            <label for="confirmPassword">Confirm Password:</label>
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-input"
                                placeholder="type again to confirm">
                        </div>

                        <div class="form-row">
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" class="form-input"
                                value="<?php echo htmlspecialchars($user['username']); ?>">
                        </div>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Customer'): ?>
                            <div class="form-row">
                                <label for="petName">Pet Name:</label>
                                <input type="text" name="petName" id="petName" class="form-input"
                                    placeholder="Please enter pet name"
                                    value="<?php echo htmlspecialchars((string)$user['petName']); ?>">
                            </div>

                            <div class="form-row">
                                <label for="petGender">Pet Gender:</label>
                                <select name="petGender" id="petGender" class="form-input">
                                    <option value="Neuter" <?php if ($user['petGender'] === 'Neuter') echo 'selected'; ?>>Neuter</option>
                                    <option value="Male" <?php if ($user['petGender'] === 'Male') echo 'selected'; ?>>Male</option>
                                    <option value="Female" <?php if ($user['petGender'] === 'Female') echo 'selected'; ?>>Female</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label for="petAge">Pet Age:</label>
                                <input type="number" name="petAge" id="petAge" class="form-input" min="0" max="50"
                                    value="<?php echo htmlspecialchars((string)($user['petAge'] ?? 0)); ?>">
                            </div>

                            <div class="form-row">
                                <label for="petSpecies">Pet Species:</label>
                                <input type="text" name="petSpecies" id="petSpecies" class="form-input"
                                    placeholder="Enter your pet's species here"
                                    value="<?php echo htmlspecialchars((string)($user['petSpecialInfo'] ?? '')); ?>">
                            </div>
                        <?php endif; ?>

                    </div>


                    <div class="text-center">
                        <button class="submit-btn" type="submit">SAVE CHANGES</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/fontawesome-all.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/pwlimitation.js"></script>
    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>

</html>