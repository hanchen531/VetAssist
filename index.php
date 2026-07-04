<?php
include 'PHP/db_conn.php';
session_start();


$userID = $_SESSION['userID'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';
$role = $_SESSION['role'] ?? null;

if (isset($_GET['emergency']) && $_GET['emergency'] === 'success') {
    echo "<script>alert('Emergency appointment booked successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>VetAssist Home</title>
    <link rel="icon" type="image/jpg" href="img/icon.png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/index.css">

    <style>
        body {
            background-image: url('img/index-bg.jpg');
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            background-size: cover;
            z-index: -10;
        }
    </style>
</head>


<body>

    <header class="header">
        <div class="main-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 d-flex align-items-center">
                        <div class="logo" data-animate="fadeInUp" data-delay=".7">
                            <a href="index.php">
                                <img src="img/logo-left.png" alt="VetAssistLogo" style="height: 80%; width: 80%;">
                            </a>
                        </div>

                    </div>

                    <div class="col-lg-7 col-md-5 col-sm-5 col-6">
                        <nav data-animate="fadeInUp" data-delay=".9">
                            <div class="header-menu">
                                <ul class="d-flex gap-3 list-unstyled header-btn">
                                    <li><a href="dashboard.php">Dashboard</a></li>

                                    <?php
                                    $role = $_SESSION['role'] ?? 'User';

                                    if ($role === 'Customer') {
                                        echo '<li><a href="appointment.php">Appointment</a></li>';
                                        echo '<li><a href="payments.php">Payment</a></li>';
                                    }

                                    if ($role === 'Admin') {
                                        echo '<li><a href="management.php">Management</a></li>';
                                        echo '<li><a href="profile.php">Profile</a></li>';
                                    } elseif ($role === 'Staff') {
                                        echo '<li><a href="management.php">Management</a></li>';
                                        echo '<li><a href="profile.php">Profile</a></li>';
                                    } elseif ($role === 'Doctor') {
                                        echo '<li><a href="management.php">Management</a></li>';
                                        echo '<li><a href="profile.php">Profile</a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </nav>
                    </div>



                    <div class="col-lg-3 col-md-4 col-sm-3 d-none d-sm-block">
                        <div class="dropdownbutton d-flex justify-content-end align-items-center">
                            <ul class="client-area text-right list-inline m-0" data-animate="fadeInUp" data-delay="1.1">
                                <?php if (isset($_SESSION['username'])): ?>
                                    <li class="dropdown">
                                        <a href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                                            <i class="fas fa-caret-down"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right client-links"
                                            aria-labelledby="dropdownMenuLink">
                                            <li><a href="profile.php">Profile</a></li>
                                            <li><a href="logout.php">Logout</a></li>
                                        </ul>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a href="login.html">Login</a>
                                    </li>
                                <?php endif; ?>
                            </ul>

                        </div>

                    </div>

                </div>

            </div>
            <div class="col-auto d-flex d-sm-none justify-content-end align-items-center">
                <button class="menu-toggle btn btn-link" style="font-size: 1.5rem;">
                    ☰
                </button>
            </div>
        </div>


    </header>

    <div class="container">
        <div class="main-content">
            <div class="mob-welcome">Welcome, <?php echo htmlspecialchars($username); ?> (ID: <?php echo htmlspecialchars($userID); ?>)</div>
            <div class="title mt-5 mb-4">New online & offline service, <br>new experience</div>
            <div class="subtitle mb-4">Check up, treat, or vaccinate your pet with our experienced veterinarians</div>


            <div class="action-button-container" style="display: flex; gap: 10px; flex-wrap: wrap; max-width: 600px;">
                <?php
                $role = $_SESSION['role'] ?? null;

                if ($role === 'Customer'): ?>
                    <a class="btn btn-mob btn-primary mb-3" style="width:200px"href="appointment.php">Make Appointment</a>
                    <form method="POST" action="PHP/emergency_appointment.php">
                        <button type="submit" class="btn btn-mob btn-secondary mb-3">Emergency Appointment</button>
                    </form>

                <div class="mob-only">
                    <a class="btn btn-mob btn-primary mb-3" href="payments.php" >Payment</a>
                    <a class="btn btn-mob  btn-primary mb-3" href="medrecord.oho">Record</a>
                
                <?php elseif ($role === 'Doctor'): ?>
                    <a class="btn btn-mob btn-primary mb-3" href="management.php">Medical Record</a>

                <?php elseif ($role === 'Staff'): ?>
                    <a class="btn btn-mob btn-primary  mb-3" href="management.php">Management</a>

                <?php elseif ($role === 'Admin'): ?>
                    <a class="btn btn-mob btn-primary mb-3" href="management.php">Management</a>
                    <a class="btn btn-mob btn-secondary mb-3" href="newstaff.php">New Staff</a>
                <?php endif; ?>
            </div>
            </div>





            <div class="desktop-content">
                <div class="slogan"><i class="far fa-calendar-alt fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Finding
                    availability time</div>
                <div class="slogan"><i class="fas fa-notes-medical fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Unlimited review
                    of medical records</div>
                <div class="slogan"><i class="fas fa-chart-line fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Easy to understand
                    user dashboard</div>
                <div class="slogan"><i class="fas fa-dollar-sign fa-fw"></i>&nbsp;&nbsp;&nbsp;&nbsp;Multiple online
                    payment support</div>
            </div>
            </div>


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

            <div class="mob-act">Your Coming Activities</div>
            <ul class="mob-activity-box mob-only">

                    <?php
                    include 'PHP/db_conn.php';

                    $today = date('Y-m-d');

                    if ($role === 'Customer') {
                        $sql = "
                            SELECT a.date, a.time, u.username AS doctorName
                            FROM Appointment a
                            JOIN Doctor d ON a.doctorID = d.doctorID
                            JOIN User u ON u.userID = d.doctorID
                            WHERE a.customerID = $userID AND a.date >= '$today'
                            ORDER BY a.date, a.time
                            LIMIT 5
                        ";
                        $res = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $date = date("d / M", strtotime($row['date']));
                                $time = date("g:i A", strtotime($row['time']));
                                echo "<li>$date - $time with Dr. {$row['doctorName']}</li>";
                            }
                        } else {
                            echo "<li>No upcoming appointments found.</li>";
                        }
                    } elseif ($role === 'Doctor') {
                        $sql = "
                            SELECT a.date, a.time, u.username AS customerName
                            FROM Appointment a
                            JOIN Customer c ON a.customerID = c.customerID
                            JOIN User u ON u.userID = c.customerID
                            WHERE a.doctorID = $userID AND a.date >= '$today'
                            ORDER BY a.date, a.time
                            LIMIT 5
                        ";
                        $res = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $date = date("d / M", strtotime($row['date']));
                                $time = date("g:i A", strtotime($row['time']));
                                echo "<li>$date - $time with {$row['customerName']}</li>";
                            }
                        } else {
                            echo "<li>No upcoming appointments found.</li>";
                        }
                    } elseif ($role === 'Staff') {
                        $sql = "
                            SELECT a.date, a.time, u.username AS customerName, d.username AS doctorName
                            FROM Appointment a
                            JOIN Customer c ON a.customerID = c.customerID
                            JOIN User u ON u.userID = c.customerID
                            JOIN Doctor doc ON a.doctorID = doc.doctorID
                            JOIN User d ON d.userID = doc.doctorID
                            WHERE a.date >= '$today'
                            ORDER BY a.date, a.time
                            LIMIT 5
                        ";
                        $res = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $date = date("d / M", strtotime($row['date']));
                                $time = date("g:i A", strtotime($row['time']));
                                echo "<li>$date - $time {$row['customerName']} with Dr. {$row['doctorName']}</li>";
                            }
                        } else {
                            echo "<li>No activities found.</li>";
                        }
                    } elseif ($role === 'Admin') {
     
                        $totalRes = mysqli_query($conn, "SELECT SUM(amount) AS totalIncome FROM Payment WHERE status = 1");
                        $totalIncome = mysqli_fetch_assoc($totalRes)['totalIncome'] ?? 0.00;

                        $monthRes = mysqli_query($conn, "
                            SELECT SUM(amount) AS monthIncome 
                            FROM Payment 
                            WHERE status = 1 AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())");
                        $monthIncome = mysqli_fetch_assoc($monthRes)['monthIncome'] ?? 0.00;

                        $todayRes = mysqli_query($conn, "
                            SELECT SUM(amount) AS todayIncome 
                            FROM Payment 
                            WHERE status = 1 AND date = '$today'");
                        $todayIncome = mysqli_fetch_assoc($todayRes)['todayIncome'] ?? 0.00;

                        echo "<li><strong>Total Income:</strong> RM " . number_format($totalIncome, 2) . "</li><br>";
                        echo "<li><strong>This Month:</strong> RM " . number_format($monthIncome, 2) . "</li><br>";
                        echo "<li><strong>Today:</strong> RM " . number_format($todayIncome, 2) . "</li><br>";
                    } else {
                        echo "<li>No data available.</li>";
                    }
                    ?>

            </ul>
        </div>


        <script>
            document.querySelector('.menu-toggle').addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
        </script>
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/fontawesome-all.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/menu.min.js"></script>
</body>


<footer class="mob-footer d-sm-none">
    <div class="footer-icons">
        <a href="index.php"><i class="fas fa-home"></i></a>
        <a href="dashboard.php"><i class="fas fa-list-alt"></i></a>
        <a href="profile.php"><i class="fas fa-info-circle"></i></a>
    </div>


    <div class="footer-text">
        <p>© 2018 Nuanxin Pet Clinic. All rights reserved.</p>
        <p><a href="#">Terms of Service</a></p>
    </div>
</footer>