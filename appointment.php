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
if ($_SESSION['role'] === 'Customer') {
    $customerID = $_SESSION['userID'];

    $checkUnpaid = "
    SELECT COUNT(*) AS unpaidCount
    FROM Appointment a
    JOIN Payment p ON a.appointmentID = p.appointmentID
    WHERE a.customerID = ? AND p.status = 0
";

$stmt = $conn->prepare($checkUnpaid);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$res = $stmt->get_result();
$row = mysqli_fetch_assoc($res);

if ($row && $row['unpaidCount'] > 0) {
    echo "<script>
        alert('You have an unpaid bill. Please complete the payment before making a new appointment.');
        window.location.href = 'payments.php';
    </script>";
    exit();
}
}

$userName = $_SESSION['username'];
$customerID = $_SESSION['userID'];

$doctorOptions = "<option disabled selected>Select a doctor</option>";
$sql = "SELECT d.doctorID, u.username FROM Doctor d JOIN User u ON d.doctorID = u.userID";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['doctorID'];
    $name = htmlspecialchars($row['username']);
    $doctorOptions .= "<option value='$id'>$name</option>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    $type = $_POST['type'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $petInfo = $_POST['petInfo'];
    $doctorID = (int)$_POST['doctorID'];

    $sql = "INSERT INTO Appointment (date, time, doctorID, customerID) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $date, $time, $doctorID, $customerID);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment successfully booked!'); window.location.href='appointment.php';</script>";
        exit();
    } else {
        error_log("Appointment booking failed: " . $stmt->error);
        echo "<script>alert('Appointment booking failed. Please try again later.');</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en-GB">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Make Appointment</title>
    <link rel="icon" type="image/jpg" href="img/icon.png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/appointment.css">
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
                            <?php echo htmlspecialchars($userName); ?>
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
            <div class="left-box">
                <div class="appointment-form">
                    <h3 class="form-title">Make New Appointment</h3>
                    <form method="POST">

                        <div class="form-group">
                            <label>Appointment type</label>
                            <select name="type" class="form-control" required>
                                <option disabled selected>Choose appointment type</option>
                                <option value="vaccination">Vaccination</option>
                                <option value="checkup">Checkup</option>
                                <option value="treatment">Treatment</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Appointment date</label>
                            <input type="date" name="date" class="form-control" id="appointment-date" required>
                        </div>

                        <div class="form-group">
                            <label>Appointment time</label>
                            <input type="time" name="time" class="form-control" required min="08:00" max="18:00" data-bs-toggle="tooltip" 
                            data-bs-placement="right" title="Appointment time start from 8:00 AM, end at 18:00 PM">
                        </div>

                        <div class="form-group">
                            <label>Pet Information</label>
                            <input type="text" name="petInfo" class="form-control" placeholder="Enter your pet's name or ID" required>
                        </div>

                        <div class="form-group">
                            <label>Choose Doctor</label>
                            <select name="doctorID" class="form-control" required>
                                <?php echo $doctorOptions; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-submit">SUBMIT</button>
                    </form>
                </div>
            </div>



            <div class="right-box">
                <?php
                include 'PHP/db_conn.php';
                $appointments = [];
                $sql = "
                SELECT a.appointmentID, a.emergency, a.date, a.time, u.username
                FROM Appointment a
                JOIN Doctor d ON a.doctorID = d.doctorID
                JOIN User u ON d.doctorID = u.userID
                WHERE a.customerID = ?
                AND NOT EXISTS (
                SELECT 1 FROM MedicalRecord m
                WHERE m.appointmentID = a.appointmentID)
                ORDER BY a.date ASC, a.time ASC
                LIMIT 5";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $customerID);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $appointments[] = $row;
                    }
                }
                ?>
                <div class="top-small-box">
                    <h5>Upcoming Appointment</h5>
                    <?php if (count($appointments) === 0): ?>
                        <div class="data-item">
                            <div>
                                <i class="fas fa-info-circle"></i>
                                <span>No upcoming appointments</span>
                                <div class="sub">Please book a new one</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($appointments as $appt): ?>
                            <div class="data-item">
                                <div>
                                    <i class="fas fa-calendar-check"></i>
                                    <span><?php $tag = ($appt['emergency'] == 1) ? '[Emergency] ' : '';
                                            echo $tag . htmlspecialchars(date('d/m/Y', strtotime($appt['date'])) . ' at ' . $appt['time']); ?></span>
                                    <div class="sub">Dr. <?php echo htmlspecialchars($appt['username']); ?></div>
                                </div>
                                <form method="POST" action="PHP/delete_appointment.php" style="display:inline;" onsubmit="return confirm('Are you sure to delete this appointment?');">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($appt['appointmentID']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <button type="submit" style="border:none; background:transparent; padding:0; cursor:pointer;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="bottom-small-box">
                    <h5>Vaccination Stock</h5>
                    <?php
                    $stmt = $conn->prepare("SELECT name, quantity FROM VaccineStock");
                    $stmt->execute();
                    $vaxResult = $stmt->get_result();
                    if ($vaxResult && mysqli_num_rows($vaxResult) > 0):
                        while ($vax = mysqli_fetch_assoc($vaxResult)):
                    ?>
                            <div class="stock-item">
                                <div><strong><?php echo htmlspecialchars($vax['name']); ?></strong></div>
                                <div class="sub">Stock : <?php echo htmlspecialchars($vax['quantity']); ?></div>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <p>No vaccine data available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <footer class="mobile-footer-mini d-sm-none">
                <a href="index.php"><i class="fas fa-home"></i></a>
                <a href="medrecord.php"><i class="fas fa-calendar-check"></i></a>
                <a href="payments.php"><i class="fas fa-notes-medical"></i></a>
                <a href="profile.php"><i class="fas fa-user"></i></a>
            </footer>

        </div>


        <script src="js/timechoicelimitation1.js"></script>
        <script src="js/delete.js"></script>
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/fontawesome-all.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
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
