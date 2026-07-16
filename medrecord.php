<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'PHP/db_conn.php';
$userID = $_SESSION['userID'];
$role = $_SESSION['role'] ?? null;

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Medical Records</title>
    <link rel="icon" type="image/jpg" href="img/icon.png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/medrecord.css">
</head>


<body>

    <header class="header">
        <div class="main-header">
            <div class="container d-flex align-items-center justify-content-between">
                <button class="menu-toggle">☰</button>
                <div class="logo">
                    <a href="index.php">
                        <img src="img/logo-left.png" alt="VetAssistLogo" style="height: 70%; width: 70%;"
                            class="img-fluid">
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

                    <?php
                    $sql = "
                    SELECT 
                    m.recordID, m.diagnosis, m.treatment,
                    a.date, a.time,
                    u.username AS doctorName
                    FROM MedicalRecord m
                    JOIN Appointment a ON m.appointmentID = a.appointmentID
                    JOIN Doctor d ON m.doctorID = d.doctorID
                    JOIN User u ON u.userID = d.doctorID
                    WHERE a.customerID = ?
                    ORDER BY a.date DESC, a.time DESC
                    LIMIT 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $userID);
                    $stmt->execute();
                    $latest = $stmt->get_result();
                    $latestRow = mysqli_fetch_assoc($latest);
                    ?>


                    <h2>Latest Medical Record</h2>
                    <?php if (!$latestRow): ?>
                        <p>No recent medical records found.</p>
                    <?php else: ?>
                        <p><strong>Date:</strong> <?php echo $latestRow['date']; ?></p>
                        <p><strong>Time:</strong> <?php echo $latestRow['time']; ?></p>
                        <p><strong>Doctor:</strong> Dr. <?php echo htmlspecialchars($latestRow['doctorName']); ?></p>
                        <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($latestRow['diagnosis']); ?></p>
                        <p><strong>Treatment:</strong> <?php echo htmlspecialchars($latestRow['treatment']); ?></p>
                    <?php endif; ?>
                </div>


                <div class="right-box">
                    <h3>Medical History:</h3>
                    <?php
                    $date = date('Y-m-d');
                    $selectedDate = $_GET['date'] ?? $date;

                    $sql = "
                        SELECT m.recordID, m.diagnosis, m.treatment, m.nextTreatment, a.date, a.time, u.username AS doctorName
                        FROM MedicalRecord m
                        JOIN Appointment a ON m.appointmentID = a.appointmentID
                        JOIN Doctor d ON m.doctorID = d.doctorID
                        JOIN User u ON u.userID = d.doctorID
                        WHERE a.customerID = ?
                        AND a.date = ?
                        ORDER BY a.date DESC, a.time DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $userID, $selectedDate);
                    $stmt->execute();
                    $history = $stmt->get_result();

                    


                    echo '<form method="GET" class="mb-3">';
                    echo '<label>Select Appointment Date:</label>';
                    echo '<input type="date" name="date" max="' . date('Y-m-d') . '" value="' . $selectedDate . '" required>';
                    echo '<button type="submit" class="btn btn-sm btn-primary ml-2">Filter</button>';
                    echo '</form>';

                    if (mysqli_num_rows($history) === 0) {
                        echo "<p>No medical records found on selected date.</p>";
                    } else {
                        $index = 1;
                        while ($row = mysqli_fetch_assoc($history)) {
                            $status = ($row['nextTreatment'] == 1) ? "Need next treatment" : "Finished";
                            $diagnosis = htmlspecialchars($row['diagnosis']);
                            $treatment = htmlspecialchars($row['treatment']);
                            $doctor = htmlspecialchars($row['doctorName']);
                            $formattedDate = date("d / m / Y", strtotime($row['date']));
                            $time = $row['time'];
                            $recordID = $row['recordID'];
                            echo "<div class='history-item' onclick='showMedicalRecord($recordID)'>";
                            echo "No.$index <p><strong>Date:</strong> $formattedDate at $time</p>";
                            echo "</div>";

                            $index++;
                        }
                    }
                    ?>

                    <div id="medicalPopup" class="modal" style="display:none; position:fixed; max-height:96%; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; border:1px solid #ccc; padding:20px; z-index:1000; max-width:500px; width:90%; box-shadow:0 2px 10px rgba(0,0,0,0.2); border-radius:10px;">
                        <div style="text-align:center;">
                            <img src="img/logo-center.png" alt="VetAssist Logo" style="height:60px;">
                            <h5 style="color:#007bff; margin-top:10px;">VetAssist Medical Record</h5>
                            <hr>
                        </div>
                        <div id="medPopupContent"></div>
                        <div style="text-align:center; margin-top:15px;">
                            <button onclick="printMedical()" class="btn btn-success btn-sm">🖨️ Print</button>
                            <button onclick="closeMedical()" class="btn btn-danger btn-sm">Close</button>
                        </div>
                    </div>
                </div>







                <script src="js/jquery-3.2.1.min.js"></script>
                <script src="js/fontawesome-all.js"></script>
                <script src="js/bootstrap.bundle.min.js"></script>
<script>
            document.querySelector('.menu-toggle').addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
        </script>
                <script>
                    function showMedicalRecord(recordID) {
                        fetch('PHP/get_medical_records.php?recordID=' + recordID)
                            .then(res => res.text())
                            .then(data => {
                                document.getElementById('medPopupContent').innerHTML = data;
                                document.getElementById('medicalPopup').style.display = 'block';
                            });
                    }

                    function closeMedical() {
                        document.getElementById('medicalPopup').style.display = 'none';
                    }


                </script>

                <script>
function printMedical() {
    const popup = document.getElementById("medicalPopup");
    const content = popup.innerHTML;

    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write(`
        <html>
            <head>
                <title>VetAssist Medical Record</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 40px;
                        color: #333;
                    }
                    h5 {
                        color: #007bff;
                        text-align: center;
                        margin-top: 10px;
                    }
                    hr {
                        border: 1px solid #007bff;
                    }
                    .logo {
                        text-align: center;
                        margin-bottom: 10px;
                    }
                    .logo img {
                        height: 60px;
                    }
                    .record-content {
                        margin-top: 20px;
                        font-size: 15px;
                        line-height: 1.6;
                    }
                    strong {
                        color: #555;
                    }
                </style>
            </head>
            <body>
                <div class="logo">
                    <img src="img/logo-center.png" alt="VetAssist Logo">
                </div>
                <h5>VetAssist Medical Record</h5>
                <hr>
                <div class="record-content">
                    ${document.getElementById("medPopupContent").innerHTML}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}
</script>

</body>
