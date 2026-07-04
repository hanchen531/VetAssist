<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access denied: Admin only'); window.location.href='dashboard.html';</script>";
    exit();
}
$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? null;

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add New Staff</title>
    <link rel="icon" type="image/jpg" href="img/icon.png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/newstaff.css">
</head>


<body>

    <header class="header">
        <div class="main-header">
            <div class="container d-flex align-items-center justify-content-between">
                <button class="menu-toggle">☰</button>
                <div class="logo">
                    <a href="index.php  ">
                        <img src="img/logo-left.png" alt="VetAssistLogo" style="height: 70%; width: 70%;"
                            class="img-fluid">
                    </a>
                </div>
                <ul class="text-right d-flex align-items-center m-0 p-0">
                    <li class="dropdown">
                        <a href="#" class="account-btn" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $_SESSION['username']; ?>
                            <i class="fas fa-caret-down ml-1"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right custom-dropdown" aria-labelledby="dropdownMenuLink">
                            <li><a href="profile.php">User Profile</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                </ul>
                </li>
                </ul>
            </div>
        </div>
    </header>




    <div class="display-area">

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
    <form action="PHP/createnewstaff.php" method="POST" onsubmit="return validatePassword();">
        <div class="component-area">
            <div style="font-size: x-large; font-weight: bold;">USER PROFILE</div>
            <div class="create-profile">

                <div class="form-row">
                    <label for="create-email">Email:</label>
                    <input type="email" name="email" id="create-email" class="form-input" required>
                </div>

                <div class="form-row">
                    <label for="password">New Password:</label>
                    <input type="password" name="password" id="password" class="form-input"
                        placeholder="at least 8 characters"
                        data-bs-toggle="tooltip"
                        data-bs-placement="right"
                        title="Password must include uppercase, lowercase, number, and symbol (with at least 8 characters)"
                        required>
                </div>

                <div class="form-row">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" name="confirm" id="confirmPassword" class="form-input"
                        placeholder="type password again to confirm" required>
                </div>

                <div class="form-row">
                    <label for="create-username">Username:</label>
                    <input type="text" name="username" id="create-username" class="form-input"
                        placeholder="e.g. MARK JONES" required>
                </div>

                <div class="form-row">
                    <label for="create-position">Position:</label>
                    <select name="position" id="create-position" class="form-input" required>
                        <option value="default">Please Select</option>
                        <option value="Staff">Staff</option>
                        <option value="Doctor">Doctor</option>
                    </select>
                </div>

            </div>

            <div class="text-center">
                <button class="submit-btn" type="submit">CREATE</button>
            </div>
        </div>
    </form>
</div>



        



        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/fontawesome-all.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/pwlimitation.js"></script>
        <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
        </script>
        <script>
            document.querySelector('.menu-toggle').addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
        </script>

</body>