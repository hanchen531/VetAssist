<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'PHP/db_conn.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
$role = $_SESSION['role'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF']);
?>


<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
    <link rel="icon" type="image/jpg" href="img/icon.png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                            <?php echo $_SESSION['username']; ?>
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
            <?php if ($role === 'Admin'): ?>
                <?php
                $appointmentData = [];
                $today = date('Y-m-d');
                $res1 = mysqli_query($conn, "
                    SELECT u.username AS doctorName, COUNT(*) AS count
                    FROM Appointment a
                    JOIN Doctor d ON a.doctorID = d.doctorID
                    JOIN User u ON u.userID = d.doctorID
                    WHERE a.date = '$today'
                    GROUP BY a.doctorID");
                while ($row = mysqli_fetch_assoc($res1)) {
                    $appointmentData[] = $row;
                }
                $vaccineData = [];
                $res2 = mysqli_query($conn, "SELECT name, quantity FROM VaccineStock");
                while ($row = mysqli_fetch_assoc($res2)) {
                    $vaccineData[] = $row;
                }
                ?>



                <div class="profile-card">
                    <h3 style="font-family: serif; font-weight: bold;">Clinic Overview Dashboard</h3>
                </div>

                <div style="height: 100%; display: flex; gap: 20px; flex-wrap: wrap; justify-content: space-between; align-items: stretch;">


                    <div class="data-card admin-stat-card" style="flex: 1 1 30%; min-width: 280px; padding: 20px;justify-content: center;">
                        <h5>Appointments Today</h5>
                        <canvas id="appointmentsChart" style="width: 100%; height: 180px;"></canvas>
                    </div>


                    <div class="data-card" style="flex: 1 1 30%; min-width: 280px; padding: 20px;justify-content: center;">
                        <h5>Vaccine Inventory</h5>
                        <canvas id="vaccineChart" style="width: 100%; height: 180px;"></canvas>
                    </div>


                    <div class="data-card finan-sum" style="flex: 1 1 30%; min-width: 280px; padding: 20px; min-height:auto ;justify-content: center;">
                        <h5>Financial Summary</h5>
                        <p style="font-size: 1.1rem;">
                            <strong>Total Income:</strong> RMB
                            <?php
                            $res = mysqli_query($conn, "SELECT SUM(amount) AS total FROM Payment");
                            $row = mysqli_fetch_assoc($res);
                            echo number_format($row['total'] ?? 0, 2);
                            ?>
                        </p>

                        <?php
                        $feeSql = "
                            SELECT 
                                SUM(registrationFee) AS reg,
                                SUM(labTestFee) AS lab,
                                SUM(hospitalizationFee) AS hos,
                                SUM(medicationFee) AS med,
                                SUM(otherFee) AS other
                            FROM ManageFinanceStatus
                        ";
                        $feeRes = mysqli_query($conn, $feeSql);
                        $fees = mysqli_fetch_assoc($feeRes);
                        ?>

                        <ul style="font-size: 0.95rem; line-height: 1.6; padding-left: 1.2em;">
                            <li><strong>Registration Fee:</strong> RMB <?php echo number_format($fees['reg'] ?? 0, 2); ?></li>
                            <li><strong>Lab Test Fee:</strong> RMB <?php echo number_format($fees['lab'] ?? 0, 2); ?></li>
                            <li><strong>Hospitalization Fee:</strong> RMB <?php echo number_format($fees['hos'] ?? 0, 2); ?></li>
                            <li><strong>Medication Fee:</strong> RMB <?php echo number_format($fees['med'] ?? 0, 2); ?></li>
                            <li><strong>Other Fee:</strong> RMB <?php echo number_format($fees['other'] ?? 0, 2); ?></li>
                        </ul>
                    </div>

                </div>






            <?php elseif ($role === 'Doctor'): ?>
                <div class="profile-card" style="margin-bottom: 20px; padding: 20px; background: #f5f5f5; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <h4 style="margin: 0; font-family: serif;">Today’s Work List</h4>
                </div>

                <div class="doctor-section">
                    <div class="data-card doc-card" style="flex: 1; overflow-y: auto; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                        <h5 style="margin-bottom: 15px; font-size: 18px; color: #333;">Today's Appointments</h5>
                        <?php
                        $today = date('Y-m-d');
                        $doctorID = intval($_SESSION['userID']);

                        $getAppointments = "
                            SELECT a.time, c.petName
                            FROM Appointment a
                            JOIN Customer c ON a.customerID = c.customerID
                            WHERE a.doctorID = $doctorID
                            AND a.date = '$today'
                            AND NOT EXISTS (
                            SELECT 1 FROM MedicalRecord m
                            WHERE m.appointmentID = a.appointmentID)
                            ORDER BY a.time ASC";

                        $AppNum = mysqli_query($conn, $getAppointments);

                        if ($AppNum && mysqli_num_rows($AppNum) > 0) {
                            while ($row = mysqli_fetch_assoc($AppNum)) {
                                echo "<div style='padding: 10px 0; border-bottom: 1px solid #eee; display: flex; align-items: center;'>";
                                echo "<i class='fas fa-clock' style='margin-right: 10px; color: #007bff;'></i>";
                                echo "<span style='font-size: 16px; color: #555;'>" . htmlspecialchars($row['time']) . " — " . htmlspecialchars($row['petName']) . "</span>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p style='color: #888;'>Today's appointments completed.</p>";
                        }
                        ?>
                    </div>

                    <div class="data-card doc-card" style="flex: 1; overflow-y: auto; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                        <h5 style="margin-bottom: 15px; font-size: 18px; color: #333;">Unfilled Medical Records</h5>
                        <?php
                        $query = "
                            SELECT a.date, a.time, u.username AS customerName
                            FROM Appointment a
                            JOIN Customer c ON a.customerID = c.customerID
                            JOIN User u ON u.userID = c.customerID
                            WHERE a.doctorID = $doctorID
                            AND NOT EXISTS (
                            SELECT 1 FROM MedicalRecord m
                            WHERE m.appointmentID = a.appointmentID)
                            ORDER BY a.date ASC, a.time ASC";
                        $res = mysqli_query($conn, $query);

                        if ($res && mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<div style='padding: 10px 0; border-bottom: 1px solid #eee; display: flex; align-items: center;'>";
                                echo "<i class='fas fa-notes-medical' style='margin-right: 10px; color: #28a745;'></i>";
                                echo "<span style='font-size: 16px; color: #555;'>" . htmlspecialchars($row['date']) . " " . htmlspecialchars($row['time']) . " — " . htmlspecialchars($row['customerName']) . "</span>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p style='color: #888;'>🎉 No pending records.</p>";
                        }
                        ?>
                    </div>
                </div>






            <?php elseif ($role === 'Staff'): ?>
                <div class="profile-card">
                    <h2 style="font-family: serif; padding: 5px">Staff Overview Dashboard</h2>
                </div>


                <div style="display: flex; flex-direction: column; gap: 20px; flex: 1;">
                    <div class="doctor-section">
                        <div class="data-card" style="flex: 1; min-height: fit-content;">
                            <h5>Staff Notes</h5>
                            <ol style="padding-left: 18px; font-size: 0.95rem;">
                                <li> Pay attention to checking the completed reservation items and conducting the billing.</li>
                                <li> Ensure waiting room cleanliness.</li>
                                <li> Help the doctor prepare the necessary medical equipment.</li>
                                <li> Remind customers to update pet profiles.</li>
                                <li> Clean the consultation room after the examination is completed.</li>
                            </ol>

                        </div>


                        <div class="data-card" style="flex: 1; min-height: fit-content;">
                            <h5>Vaccine Stock</h5>
                            <canvas id="vaccineChart"></canvas>

                            <?php
                            $vaccineNames = [];
                            $vaccineQuantities = [];

                            $vaxResult = mysqli_query($conn, "SELECT name, quantity FROM VaccineStock");
                            if ($vaxResult && mysqli_num_rows($vaxResult) > 0) {
                                while ($vax = mysqli_fetch_assoc($vaxResult)) {
                                    $vaccineNames[] = $vax['name'];
                                    $vaccineQuantities[] = $vax['quantity'];
                                }
                            }
                            ?>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const ctx = document.getElementById('vaccineChart').getContext('2d');

                                    const vaccineChart = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: <?php echo json_encode($vaccineNames); ?>,
                                            datasets: [{
                                                label: 'Remaining Doses',
                                                data: <?php echo json_encode($vaccineQuantities); ?>,
                                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                                borderColor: 'rgba(54, 162, 235, 1)',
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    stepSize: 1
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>

                            <style>
                                #vaccineChart {
                                    width: 100% !important;
                                    height: 200px !important;
                                }
                            </style>
                        </div>

                        <div class="data-card" style="flex: 1.2; max-height: 100%; overflow-y: auto;">
                            <h5>Today’s Appointments</h5>
                            <?php
                            $today = date('Y-m-d');
                            $query = "
                            SELECT a.time, a.emergency, c.petName, c.petSpecialInfo, u.username AS doctorName
                            FROM Appointment a
                            JOIN Customer c ON a.customerID = c.customerID
                            JOIN Doctor d ON a.doctorID = d.doctorID
                            JOIN User u ON u.userID = d.doctorID
                            WHERE a.date = '$today'
                            ORDER BY a.time ASC";

                            $result = mysqli_query($conn, $query);

                            if ($result && mysqli_num_rows($result) > 0):
                                while ($row = mysqli_fetch_assoc($result)):
                            ?>
                                    <div class="data-item">
                                        <div>
                                            <i class="fas fa-clock"></i>
                                            <span><?php $tag = ($row['emergency'] == 1) ? "[Emergency] " : "";
                                                    echo $tag . htmlspecialchars($row['time']) . ' — ' . htmlspecialchars($row['petName']) . ' (' . htmlspecialchars($row['petSpecialInfo']) . ')'; ?></span>
                                            <div class="sub">Dr. <?php echo htmlspecialchars($row['doctorName']); ?></div>
                                        </div>
                                    </div>
                                <?php endwhile;
                            else: ?>
                                <p>No appointments today.</p>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
















            <?php else: ?>
                <?php
                $userID = $_SESSION['userID'];
                $query = "SELECT petName, petSpecialInfo, petAge FROM Customer WHERE customerID = $userID";
                $result = mysqli_query($conn, $query);
                $pet = mysqli_fetch_assoc($result);
                ?>

                <div class="profile-card">
                    <div class="profile-left">
                        <img src="img/pet.jpg" alt="Pet" class="pet-img">
                        <div class="pet-name">
                            <div>Pet</div>
                            <div class="pet-main-name"><?php echo htmlspecialchars($pet['petName']); ?></div>
                        </div>
                    </div>
                    <div class="profile-right">
                        <p><strong>Species:</strong> <?php echo htmlspecialchars($pet['petSpecialInfo']); ?></p>
                        <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['petAge']); ?></p>
                    </div>
                </div>


                <div class="cus-area">

                    <div class="data-card" style="height: 100%; display: flex; flex-direction: column; padding: 15px 20px; border-radius: 12px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                        <h5 style="font-family: serif; margin-bottom: 15px;">Upcoming Appointment</h5>
                        <?php
                        $sql = "
                            SELECT a.date, a.time, a.emergency, u.username AS doctorName
                            FROM Appointment a
                            JOIN Doctor d ON a.doctorID = d.doctorID
                            JOIN User u ON u.userID = d.doctorID
                            WHERE a.customerID = $userID
                            AND NOT EXISTS (
                            SELECT 1 FROM MedicalRecord m WHERE m.appointmentID = a.appointmentID
                            )
                            ORDER BY a.date ASC, a.time ASC
                            LIMIT 2";
                        $result = mysqli_query($conn, $sql);
                        if ($result && mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                        ?>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-top: 1px solid #eee;">
                                    <div style="display: flex; align-items: center;">
                                        <i class="fas fa-notes-medical" style="font-size: 22px; margin-right: 10px;"></i>
                                        <div>
                                            <div style="font-weight: bold;"><?php $tag = ($row['emergency'] == 1) ? "[Emergency] " : "";
                                                                            echo $tag . htmlspecialchars($row['date']) . ' - at ' . htmlspecialchars($row['time']); ?></div>

                                            <div class="sub">Dr. <?php echo htmlspecialchars($row['doctorName']); ?></div>
                                        </div>
                                    </div>

                                </div>
                            <?php endwhile;
                        else: ?>
                            <p>No upcoming appointments.</p>
                        <?php endif; ?>
                    </div>


                    <div class="data-card" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; padding: 15px 20px; border-radius: 12px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h5 style="font-family: serif; margin-bottom: 15px;">Medication History</h5>

                        <?php
                        $userID = $_SESSION['userID'];
                        include 'PHP/db_conn.php';

                        $query = "
                            SELECT m.treatment, a.date
                            FROM MedicalRecord m
                            JOIN Appointment a ON m.appointmentID = a.appointmentID
                            WHERE a.customerID = $userID
                            ORDER BY a.date DESC, a.time DESC
                            LIMIT 2";

                        $result = mysqli_query($conn, $query);

                        if ($result && mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)):
                        ?>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-top: 1px solid #eee;">
                                    <div style="display: flex; align-items: center;">
                                        <i class="fas fa-pills" style="font-size: 22px; margin-right: 10px;"></i>
                                        <div>
                                            <div style="font-weight: bold;"><?php echo htmlspecialchars($row['treatment']); ?></div>
                                            <div class="sub"><?php echo htmlspecialchars($row['date']); ?></div>
                                        </div>
                                    </div>

                                </div>
                        <?php
                            endwhile;
                        else:
                            echo "<p>No medication records found.</p>";
                        endif;
                        ?>
                    </div>


                    <div class="data-card" style="height: 100%; display: flex; flex-direction: column; padding: 15px 20px; border-radius: 12px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h5 style="font-family: serif;">Health Summary</h5>

                        <?php
                        $userID = $_SESSION['userID'];

                        $sql = "
                            SELECT mr.diagnosis, a.date
                            FROM MedicalRecord mr
                            JOIN Appointment a ON mr.appointmentID = a.appointmentID
                            WHERE a.customerID = $userID
                            ORDER BY a.date DESC
                            LIMIT 3";


                        $result = mysqli_query($conn, $sql);
                        ?>

                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <ol style="margin-top: 10px; padding-left: 20px; line-height: 1.6;">
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <li><?php echo htmlspecialchars($row['diagnosis']); ?></li>
                                <?php endwhile; ?>
                            </ol>
                        <?php else: ?>
                            <p style="margin-top: 10px; color: gray;">No recent diagnosis found.</p>
                        <?php endif; ?>
                    </div>





                    <div class="data-card" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between; padding: 15px 20px; border-radius: 12px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <h5 style="font-family: serif;">Latest Bill Breakdown</h5>
                        <?php
                        // Get Lastest MFS from DB
                        $userID = $_SESSION['userID'];

                        $financeQuery = "
                            SELECT mfs.*
                            FROM ManageFinanceStatus mfs
                            JOIN Payment p ON mfs.financeID = p.paymentID
                            JOIN Appointment a ON p.appointmentID = a.appointmentID
                            JOIN Customer c ON a.customerID = c.customerID
                            WHERE c.customerID = $userID
                            ORDER BY p.date DESC
                            LIMIT 1";

                        $res = mysqli_query($conn, $financeQuery);
                        $mfs = mysqli_fetch_assoc($res);
                        ?>

                        <?php if ($mfs): ?>
                            <?php
                            // Non-zero Fees select
                            $feeLabels = [];
                            $feeData = [];

                            if ($mfs['registrationFee'] > 0) {
                                $feeLabels[] = 'Registration';
                                $feeData[] = (float)$mfs['registrationFee'];
                            }
                            if ($mfs['labTestFee'] > 0) {
                                $feeLabels[] = 'Lab Test';
                                $feeData[] = (float)$mfs['labTestFee'];
                            }
                            if ($mfs['hospitalizationFee'] > 0) {
                                $feeLabels[] = 'Hospitalization';
                                $feeData[] = (float)$mfs['hospitalizationFee'];
                            }
                            if ($mfs['medicationFee'] > 0) {
                                $feeLabels[] = 'Medication';
                                $feeData[] = (float)$mfs['medicationFee'];
                            }
                            if ($mfs['otherFee'] > 0) {
                                $feeLabels[] = 'Other';
                                $feeData[] = (float)$mfs['otherFee'];
                            }
                            ?>

                            <canvas id="financeChart" style="max-height: 275px;"></canvas>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const ctx = document.getElementById('financeChart').getContext('2d');

                                    const labels = <?php echo json_encode($feeLabels); ?>;
                                    const data = <?php echo json_encode($feeData); ?>;

                                    if (labels.length === 0 || data.length === 0) {
                                        console.warn('No finance data available for chart.');
                                        return;
                                    }

                                    new Chart(ctx, {
                                        type: 'doughnut',
                                        data: {
                                            labels: labels,
                                            datasets: [{
                                                label: 'Fee Breakdown',
                                                data: data,
                                                backgroundColor: ['#4caf50', '#f44336', '#2196f3', '#ff9800', '#9c27b0']
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: {
                                                legend: {
                                                    position: 'bottom'
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(context) {
                                                            return context.label + ': ¥' + context.raw.toFixed(2);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        <?php else: ?>
                            <p style="color: gray;">No billing data available.</p>
                        <?php endif; ?>
                    </div>

                </div>


            <?php endif; ?>
        </div>

    </div>


    <footer class="mobile-footer-mini d-sm-none">
        <a href="index.php"><i class="fas fa-home"></i></a>

        <?php if ($role === 'Customer'): ?>
            <a href="appointment.php"><i class="fas fa-calendar-check"></i></a>
            <a href="payments.php"><i class="fas fa-credit-card"></i></a>
            <a href="medrecord.php"><i class="fas fa-notes-medical"></i></a>

        <?php elseif ($role === 'Staff' || $role === 'Admin'): ?>
            <a href="dashboard.php"><i class="fas fa-chart-line"></i></a>
            <a href="management.php"><i class="fas fa-tools"></i></a>
            <a href="profile.php"><i class="fas fa-user-cog"></i></a>

        <?php elseif ($role === 'Doctor'): ?>
            <a href="medrecord.php"><i class="fas fa-notes-medical"></i></a>
            <a href="dashboard.php"><i class="fas fa-chart-line"></i></a>
            <a href="profile.php"><i class="fas fa-user-cog"></i></a>

        <?php endif; ?>
    </footer>


    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/fontawesome-all.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/userpaymentchart.js"></script>
    <script src="js/menu.min.js"></script>
    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appointmentLabels = <?php echo json_encode(array_column($appointmentData, 'doctorName')); ?>;
            const appointmentCounts = <?php echo json_encode(array_column($appointmentData, 'count')); ?>;

            new Chart(document.getElementById('appointmentsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: appointmentLabels,
                    datasets: [{
                        label: 'Appointments',
                        data: appointmentCounts,
                        backgroundColor: '#42a5f5'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });


            const vaccineLabels = <?php echo json_encode(array_column($vaccineData, 'name')); ?>;
            const vaccineCounts = <?php echo json_encode(array_column($vaccineData, 'quantity')); ?>;

            new Chart(document.getElementById('vaccineChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: vaccineLabels,
                    datasets: [{
                        data: vaccineCounts,
                        backgroundColor: ['#66bb6a', '#ef5350', '#ffa726', '#42a5f5', '#ab47bc']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>






</body>