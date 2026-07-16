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
$role = $_SESSION['role'] ?? null;
$userID = $_SESSION['userID'];
$message = "";


?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Portal</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/management.css">
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

            <?php if ($role === 'Staff' || $role === 'Admin'): ?>
                <div class="component-area">
                    <h3 style="margin-bottom: 20px;">Vaccine Stock Management</h3>
                    <h5>Add New Vaccine</h5>
                    <form method="POST" action="PHP/add_vaccine.php" class="mb-4">
                        <div class="form-group">
                            <label>Vaccine Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Initial Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-success">Add Vaccine</button>
                    </form>




                    <h5 class="mt-4">Edit Existing Stock</h5>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM VaccineStock");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result && mysqli_num_rows($result) > 0): ?>
                        <table class="table table-bordered mt-3">
                            <thead class="thead-light">
                                <tr>
                                    <th>Vaccine Name</th>
                                    <th>Quantity</th>
                                    <th>Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <form method="POST" action="PHP/update_vaccine.php">
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td>
                                                <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="0" class="form-control">
                                                <input type="hidden" name="vaccineID" value="<?php echo $row['vaccineID']; ?>">
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                            </td>
                                        </form>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No vaccines found.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>





            <!--Med Reco-->
            <?php if ($role === 'Doctor'): ?>
                <div class="component-area">
                    <h3>New Medical Record</h3>
                    <form method="GET" class="mb-3">
                        <label>Select Date:</label>
                        <input type="date" name="date" max="<?php echo date('Y-m-d'); ?>" value="<?php echo $_GET['date'] ?? ''; ?>" required>
                        <button type="submit" class="btn btn-sm btn-secondary">Select</button>
                    </form>
                    <form method="POST" action="PHP/add_medical_record.php">
                        <?php
                        $selectedDate = $_GET['date'] ?? null;

                        if ($selectedDate):
                        ?>
                            <div class="form-group">
                                <label>Choose Appointment (<?php echo $selectedDate; ?>)</label>
                                <select name="appointmentID" class="form-control" required>
                                    <?php
                                    $query = "
                                        SELECT a.appointmentID, a.time, u.username AS customerName
                                        FROM Appointment a
                                        JOIN Customer c ON a.customerID = c.customerID
                                        JOIN User u ON u.userID = c.customerID
                                        WHERE a.doctorID = ?
                                        AND a.date = ?
                                        AND NOT EXISTS (
                                            SELECT 1 FROM MedicalRecord m WHERE m.appointmentID = a.appointmentID
                                        )
                                        ORDER BY a.time ASC";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("is", $userID, $selectedDate);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    if (mysqli_num_rows($res) === 0) {
                                        echo "<option disabled>No appointment available</option>";
                                    } else {
                                        while ($row = mysqli_fetch_assoc($res)) {
                                            echo "<option value='{$row['appointmentID']}'>[{$row['time']}] {$row['customerName']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Diagnosis</label>
                            <input type="text" name="diagnosis" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Treatment</label>
                            <textarea name="treatment" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Follow-Up Treatment</label>
                            <select name="followupTreatment" class="form-control">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="finished" name="finished" value="1">
                            <label class="form-check-label" for="finished">Finished</label>
                        </div>

                        <input type="hidden" name="doctorID" value="<?php echo $userID; ?>">
                        <button type="submit" class="btn btn-primary mt-3">Submit Record</button>
                    </form>
                </div>
            <?php endif; ?>




            <?php if ($role === 'Staff'): ?>
                <div class="component-area">
                    <?php
                    include 'PHP/db_conn.php';

                    $today = date('Y-m-d');
                    $selectedDate = $_GET['date'] ?? $today;


                    $sql = "
                        SELECT a.appointmentID, a.date, a.time, u.username AS customerName, d.username AS doctorName
                        FROM Appointment a
                        JOIN Customer c ON a.customerID = c.customerID
                        JOIN User u ON u.userID = c.customerID
                        JOIN Doctor doc ON a.doctorID = doc.doctorID
                        JOIN User d ON d.userID = doc.doctorID
                        JOIN MedicalRecord m ON m.appointmentID = a.appointmentID
                        LEFT JOIN Payment p ON p.appointmentID = a.appointmentID
                        WHERE m.finished = 1
                        AND p.appointmentID IS NULL
                        AND a.date = ?
                        ORDER BY a.time ASC;";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $selectedDate);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    ?>


                    <h3 class="mb-3">Unprocessed Bill</h3>

                    <form method="GET" class="form-inline mb-3">
                        <label for="date" class="mr-2">Select Date:</label>
                        <input type="date" name="date" id="date" class="form-control mr-2" max="<?php echo $today; ?>" value="<?php echo $selectedDate; ?>" required>
                        <button type="submit" class="btn btn-primary btn-sm">Show</button>
                    </form>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Customer</th>
                                    <th>Doctor</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['time']); ?></td>
                                        <td><?php echo htmlspecialchars($row['customerName']); ?></td>
                                        <td><?php echo htmlspecialchars($row['doctorName']); ?></td>
                                        <td>
                                            <a href="payments.php?date=<?php echo urlencode($row['date']); ?>&appointmentID=<?php echo urlencode($row['appointmentID']); ?>" class="btn btn-success btn-sm">Create Bill</a>

                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No completed appointments without payment on <?php echo $selectedDate; ?>.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>


            <?php if ($role === 'Doctor'): ?>
                <div class="component-area">
                    <h4>Your Medical Records</h4>


                    <form method="GET" class="mb-3">
                        <label>Filter by Date:</label>
                        <input type="date" name="viewDate" max="<?php echo date('Y-m-d'); ?>" value="<?php echo $_GET['viewDate'] ?? ''; ?>">
                        <button type="submit" class="btn btn-sm btn-secondary">Choose</button>
                    </form>

                    <?php
                    $viewDate = $_GET['viewDate'] ?? null;

                    $sql = "
                        SELECT m.*, m.patientName, a.date
                        FROM MedicalRecord m
                        JOIN Appointment a ON m.appointmentID = a.appointmentID
                        JOIN Doctor d ON m.doctorID = d.doctorID
                        JOIN User u ON u.userID = d.doctorID
                        WHERE m.doctorID = ?";

                    if ($viewDate) {
                        $sql .= " AND a.date = ?";
                    }

                    $sql .= " ORDER BY a.date DESC";

                    $stmt = $conn->prepare($sql);
                    if ($viewDate) {
                        $stmt->bind_param("is", $userID, $viewDate);
                    } else {
                        $stmt->bind_param("i", $userID);
                    }
                    $stmt->execute();
                    $records = $stmt->get_result();

                    if (mysqli_num_rows($records) === 0) {
                        echo "<p>No records found for selected date.</p>";
                    } else {
                        while ($rec = mysqli_fetch_assoc($records)) {
                            $date = htmlspecialchars($rec['date']);
                            $name = htmlspecialchars($rec['patientName']);
                            $diagnosis = htmlspecialchars($rec['diagnosis']);
                            $treatment = htmlspecialchars($rec['treatment']);
                            echo "<p><strong>[$date] $name</strong>: $diagnosis - $treatment</p>";
                        }
                    }
                    ?>
                </div>



            <?php elseif ($role === 'Admin'): ?>
                <div class="component-area">
                    <h4>All Medical Records</h4>
                    <form method="GET" class="mb-3">
                        <label for="adminDate">Select Date:</label>
                        <input type="date" name="adminDate" id="adminDate"
                            max="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo $_GET['adminDate'] ?? date('Y-m-d'); ?>">
                        <button type="submit" class="btn btn-sm btn-primary ml-2">Filter</button>
                    </form>

                    <?php
                    $adminDate = $_GET['adminDate'] ?? date('Y-m-d');

                    $sql = "
                        SELECT m.*, u.username AS doctorName, a.date, a.time
                        FROM MedicalRecord m
                        JOIN Doctor d ON m.doctorID = d.doctorID
                        JOIN User u ON u.userID = d.doctorID
                        JOIN Appointment a ON m.appointmentID = a.appointmentID
                        WHERE a.date = ?
                        ORDER BY a.date DESC, a.time DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $adminDate);
                    $stmt->execute();
                    $records = $stmt->get_result();

                    if (mysqli_num_rows($records) === 0) {
                        echo "<p>No medical records found for selected date.</p>";
                    } else {
                        $count = 1;
                        while ($rec = mysqli_fetch_assoc($records)) {
                            $formattedDate = date("d / m / Y", strtotime($rec['date']));
                            $status = $rec['nextTreatment'] ? 'Need next treatment' : 'Finished';
                            echo "<div class='record-item' style='padding: 10px; border: 1px solid #ddd; margin-bottom: 10px; border-radius: 6px;'>";
                            echo "<strong>No.$count</strong><br>";
                            echo "<strong>Patient:</strong> " . htmlspecialchars($rec['patientName']) . "<br>";
                            echo "<strong>Date:</strong> $formattedDate at {$rec['time']}<br>";
                            echo "<strong>Doctor:</strong> Dr. " . htmlspecialchars($rec['doctorName']) . "<br>";
                            echo "<strong>Diagnosis:</strong> " . htmlspecialchars($rec['diagnosis']) . "<br>";
                            echo "<strong>Treatment:</strong> " . htmlspecialchars($rec['treatment']) . "<br>";
                            echo "<strong>Status:</strong> $status";
                            echo "</div>";
                            $count++;
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>


        <script>
            document.querySelector('.menu-toggle').addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
            });
        </script>
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/fontawesome-all.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>
