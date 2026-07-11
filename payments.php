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
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Make Payments</title>
    <link rel="icon" type="image/jpg" href="img/icon.png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/payments.css">
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

                    if ($_SESSION['role'] === 'Customer') {
                        $customerID = $_SESSION['userID'];

                        // Get Unpaid Bills
                        $query = "
                        SELECT a.date AS apptDate, p.paymentID, p.amount, u.username AS staffName, p.date,
                        mfs.registrationFee, mfs.labTestFee, mfs.medicationFee, mfs.hospitalizationFee, mfs.otherFee, mfs.comment
                        FROM Payment p
                        JOIN Appointment a ON p.appointmentID = a.appointmentID
                        JOIN ManageFinanceStatus mfs ON p.paymentID = mfs.financeID
                        JOIN User u ON mfs.staffID = u.userID
                        WHERE a.customerID = $customerID AND p.status = 0
                        ORDER BY p.date DESC";

                        $result = mysqli_query($conn, $query);
                        $bills = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $bills[] = $row;
                        }

                        $totalBills = count($bills);
                        $currentIndex = isset($_GET['index']) ? intval($_GET['index']) : 0;
                        if ($currentIndex < 0) $currentIndex = 0;
                        if ($currentIndex >= $totalBills) $currentIndex = $totalBills - 1;

                        $bill = $bills[$currentIndex] ?? null;
                        
                        
                        
                        
                        
                    }
                    ?>
                    
                    
                <?php if ($_SESSION['role'] === 'Customer'): ?>
                    <?php if (isset($bill)): ?>
                        <h3>Pending Payment (<?php echo ($currentIndex + 1) . " of $totalBills"; ?>)</h3>
                        <p><strong>Check-in Date:</strong> <?php echo date('d / m / Y', strtotime($bill['apptDate'])); ?></p>
                        <p><strong>Handled by:</strong> <?php echo htmlspecialchars($bill['staffName']); ?></p>
                        <hr>
                        <p>Registration Fee: RMB <?php echo $bill['registrationFee']; ?></p>
                        <p>Lab Test Fee: RMB <?php echo $bill['labTestFee']; ?></p>
                        <p>Medication Fee: RMB <?php echo $bill['medicationFee']; ?></p>
                        <p>Hospital Fee: RMB <?php echo $bill['hospitalizationFee']; ?></p>
                        <p>Other Fee: RMB <?php echo $bill['otherFee']; ?></p>
                        <?php if (!empty($bill['comment'])): ?>
                            <p><em>Note:</em> <?php echo htmlspecialchars($bill['comment']); ?></p>
                        <?php endif; ?>
                        <hr>
                        <p><strong>Total: RMB </strong> <?php echo $bill['amount']; ?></p>

                        <form method="POST" action="PHP/pay_gateway.php">
                            <input type="hidden" name="paymentID" value="<?php echo $bill['paymentID']; ?>">
                            

                            <label><strong>Select Payment Method:</strong></label><br>
                            <div class="form-check">
                                <input type="radio" name="method" value="paypal" required> Paypal Pay
                            </div>
                            <button type="submit" class="btn btn-success mt-3">Proceed to Pay</button>
                        </form>

                        <div class="mt-3">
                            <?php if ($currentIndex > 0): ?>
                                <a href="?index=<?php echo $currentIndex - 1; ?>" class="btn btn-outline-secondary btn-sm">← Previous</a>
                            <?php endif; ?>
                            <?php if ($currentIndex < $totalBills - 1): ?>
                                <a href="?index=<?php echo $currentIndex + 1; ?>" class="btn btn-outline-secondary btn-sm">Next →</a>
                            <?php endif; ?>
                            
                        </div>

                    <?php else: ?>
                        <p>No unpaid bills found.</p>
                    <?php endif; ?>
                <?php endif; ?>






                    <?php
                    //For Staff
                    $date = date('Y-m-d');
                    $selectedDate = $_GET['date'] ?? $date;
                    $selectedAppointment = $_GET['appointmentID'] ?? null;

                    //Check Finished Med Rec but No Bills
                    $query = "
                    SELECT a.appointmentID, a.date, c.petName, u.username AS doctorName
                    FROM Appointment a
                    JOIN Customer c ON a.customerID = c.customerID
                    JOIN User u ON u.userID = a.doctorID
                    JOIN MedicalRecord m ON m.appointmentID = a.appointmentID
                    LEFT JOIN Payment p ON p.appointmentID = a.appointmentID
                    WHERE m.finished = 1
                    AND p.paymentID IS NULL
                    AND a.date = '$selectedDate'
                    ORDER BY a.time ASC";

                    $result = mysqli_query($conn, $query);
                    $appointments = [];
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $appointments[] = $row;
                        }
                    }



                    // Submit Bill
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        if (!isset($_POST['appointmentID'])) {
                            echo "<script>alert('Missing appointment ID.'); window.history.back();</script>";
                            exit();
                        }

                        $appointmentID = (int)$_POST['appointmentID'];
                        $staffID = $_SESSION['userID'];

                        // Get para
                        $getCustomerQuery = "SELECT customerID FROM Appointment WHERE appointmentID = $appointmentID";
                        $customerResult = mysqli_query($conn, $getCustomerQuery);
                        if (!$customerResult || mysqli_num_rows($customerResult) === 0) {
                            echo "<script>alert('Invalid appointment ID.'); window.history.back();</script>";
                            exit();
                        }
                        $customerRow = mysqli_fetch_assoc($customerResult);
                        $customerID = $customerRow['customerID'];

                        $regFee = (float)$_POST['regFee'];
                        $testFee = (float)$_POST['testFee'];
                        $treatFee = (float)$_POST['treatFee'];
                        $hospitalFee = (float)$_POST['hospitalFee'];
                        $otherFee = (float)$_POST['otherFee'];
                        $comment = mysqli_real_escape_string($conn, $_POST['comment']);
                        $totalAmount = $regFee + $testFee + $treatFee + $hospitalFee + $otherFee;

                        //Pass para to DB
                        $insertPayment = "
                        INSERT INTO Payment (appointmentID, customerID, amount, date, status)
                        VALUES ($appointmentID, $customerID, $totalAmount, NOW(), 0)";

                        if (mysqli_query($conn, $insertPayment)) {
                            $paymentID = mysqli_insert_id($conn);

                            $insertFinance = "
                            INSERT INTO ManageFinanceStatus
                            (financeID, registrationFee, labTestFee, medicationFee, hospitalizationFee, otherFee, comment, staffID)
                            VALUES ($paymentID, $regFee, $testFee, $treatFee, $hospitalFee, $otherFee, '$comment', $staffID)";
                            if (!mysqli_query($conn, $insertFinance)) {
                                echo "<script>alert('Failed to insert finance details.');</script>";
                            }

                            echo "<script>alert('Payment record created successfully.'); window.location.href='payments.php?date=$selectedDate';</script>";
                            exit();
                        } else {
                            echo "<script>alert('Failed to create payment record.');</script>";
                        }
                    }
                    //Page Element
                    if ($_SESSION['role'] == 'Staff') {

                        echo "<h3>Create Payment Bill</h3>";

                        echo '<form method="GET" class="mb-4">';
                        echo '<label>Select Date:</label>';
                        echo '<input type="date" name="date" max="' . date('Y-m-d') . '" value="' . $selectedDate . '">';
                        echo '&nbsp;&nbsp;&nbsp;';
                        echo '<button type="submit" class="btn btn-sm btn-primary">Select</button>';
                        echo '</form>';

                        if (!$selectedAppointment) {
                            if (count($appointments) === 0) {
                                echo '<p>No appointments found for selected date.</p>';
                            } else {
                                echo '<ul class="list-group mb-4">';
                                foreach ($appointments as $appt) {
                                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                    echo htmlspecialchars($appt['date']) . ' - ' . htmlspecialchars($appt['petName']) . ' (Dr. ' . htmlspecialchars($appt['doctorName']) . ')';
                                    echo ' <a href="payments.php?date=' . $selectedDate . '&appointmentID=' . $appt['appointmentID'] . '" class="btn btn-sm btn-outline-success">Create Bill</a>';
                                    echo '</li>';
                                }
                                echo '</ul>';
                            }
                        }

                        if ($selectedAppointment) {
                            echo '<h5>Fill in Billing Details for Appointment #' . $selectedAppointment . '</h5>';
                            echo '<form method="POST">';
                            echo '<input type="hidden" name="appointmentID" value="' . $selectedAppointment . '">';
                            echo '<div class="form-group"><label>Registration Fee</label><input type="number" name="regFee" class="form-control" step="0.01" required></div>';
                            echo '<div class="form-group"><label>Test Fee</label><input type="number" name="testFee" class="form-control" step="0.01" required></div>';
                            echo '<div class="form-group"><label>Treatment Fee</label><input type="number" name="treatFee" class="form-control" step="0.01" required></div>';
                            echo '<div class="form-group"><label>Hospital Fee</label><input type="number" name="hospitalFee" class="form-control" step="0.01" required></div>';
                            echo '<div class="form-group"><label>Other Fee</label><input type="number" name="otherFee" class="form-control" step="0.01"></div>';
                            echo '<div class="form-group"><label>Comment (for Other Fee)</label><textarea name="comment" class="form-control"></textarea></div>';
                            echo '<button type="submit" class="btn btn-primary">Submit Payment</button>';
                            echo '</form>';
                        }
                    }
                    ?>
                </div>







                <div class="right-box">

                    <?php
                    // For staff

                    $date = date('Y-m-d');
                    $selectedDate = $_GET['billdate'] ?? $date;

                    if ($_SESSION['role'] === 'Staff'):
                        $sql = "
                    SELECT a.date, p.amount, p.status,
                    u.username AS staffName,
        cu.username AS customerName
        FROM Payment p
        JOIN Appointment a ON p.appointmentID = a.appointmentID
        JOIN Customer c ON a.customerID = c.customerID
        JOIN User cu ON c.customerID = cu.userID
        JOIN ManageFinanceStatus mfs ON p.paymentID = mfs.financeID
        JOIN User u ON mfs.staffID = u.userID
        WHERE a.date = '$selectedDate'
        ORDER BY p.date DESC";

                        $billResult = mysqli_query($conn, $sql);
                    ?>

                        <h3>Bill History:</h3>

                        <form method="GET" class="mb-3">
                            <label>Choose Date:</label>
                            <input type="date" name="billdate" max="<?php echo date('Y-m-d'); ?>" value="<?php echo $selectedDate; ?>">
                            <button type="submit" class="btn btn-sm btn-primary">Select</button>
                        </form>

                        <?php

                        if ($billResult && mysqli_num_rows($billResult) > 0) {
                            while ($row = mysqli_fetch_assoc($billResult)) {
                                $formattedDate = date('d / m / Y', strtotime($row['date']));
                                $amount = $row['amount'];
                                $statusText = $row['status'] == 1 ? 'Paid' : 'Wait for payment';
                                $staffName = htmlspecialchars($row['staffName']);
                                $customerName = htmlspecialchars($row['customerName']);

                                echo '<div class="history-item">';
                                echo "<p><strong>$formattedDate</strong></p>";
                                echo "<p>Customer: $customerName<br>Amount: RMB $amount <br>Status: $statusText<br>Handled by: $staffName</p>";
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No billing records found for selected date or before.</p>';
                        }
                        ?>

                    <?php endif; ?>



                    <?php
                    $date = date('Y-m-d');
                    $selectedDate = $_GET['date'] ?? $date;

                    if ($_SESSION['role'] === 'Customer'):
                        $customerID = $_SESSION['userID'];
                        $sql = "
                        SELECT a.date AS apptDate, p.paymentID, p.date , p.amount, p.status,
                        u.username AS staffName,
                        cu.username AS customerName
                            FROM Payment p
                            JOIN Appointment a ON p.appointmentID = a.appointmentID
                            JOIN Customer c ON a.customerID = c.customerID
                            JOIN User cu ON c.customerID = cu.userID
                            JOIN ManageFinanceStatus mfs ON mfs.financeID = p.paymentID
                            JOIN User u ON mfs.staffID = u.userID
                            WHERE a.customerID = $customerID
                            AND p.status = 1
                            AND p.date = '$selectedDate'
                            ORDER BY p.date DESC";

                        $billResult = mysqli_query($conn, $sql);
                    ?>


                        <h3>Bill History:</h3>

                        <form method="GET" class="mb-3">
                            <label>Bill Generate Date:</label>
                            <input type="date" name="date" max="<?php echo date('Y-m-d'); ?>" value="<?php echo $selectedDate; ?>">
                            <button type="submit" class="btn btn-sm btn-primary">Choose</button>
                        </form>

                        <?php
                        if ($billResult && mysqli_num_rows($billResult) > 0) {
                            $index = 1;
                            while ($row = mysqli_fetch_assoc($billResult)) {
                                $formattedDate = date('d / m / Y', strtotime($row['date']));
                                $amount = $row['amount'];
                                $statusText = $row['status'] == 1 ? 'Paid' : 'Wait for payment';
                                $staffName = htmlspecialchars($row['staffName'] ?? '');
                                $customerName = htmlspecialchars($row['customerName'] ?? '');
                                $paymentID = $row['paymentID'];

                                echo '<div class="history-item" onclick="showPopup(' . $paymentID . ')">';
                                echo "<p><strong>No.$index </strong></p>";
                                echo "<p>Customer: $customerName<br>Amount: RMB $amount<br>Status: $statusText<br>Handled by: $staffName</p>";
                                echo '</div>';
                                $index++;
                            }
                        } else {
                            echo '<p>No billing records found for selected date or before.</p>';
                        }
                        ?>

                        <!-- Popup Modal -->
                        <div id="popupModal" class="modal" style="display:none; max-height: 96%; position:fixed; top:2%; left:50%; transform:translateX(-50%); background:#fff; border:1px solid #ccc; padding:20px; z-index:1000; max-width:500px; box-shadow:0 2px 10px rgba(0,0,0,0.2); overflow-y:auto;">
                            <h5>Bill Details</h5>
                            <div id="popupContent"></div>
                            <div class="receipt-actions">
                                <button onclick="printReceipt()" class="btn btn-success btn-sm">🖨️ Print</button>

                                <button onclick="closePopup()" class="btn btn-secondary btn-sm">Close</button>
                            </div>
                        </div>
                    <?php endif ?>




                </div>
            </div>






            <script>
                document.querySelector('.menu-toggle').addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('active');
                });
            </script>
            <script>
                function showPopup(paymentID) {
                    fetch('PHP/get_bill_details.php?paymentID=' + paymentID)
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('popupContent').innerHTML = data;
                            document.getElementById('popupModal').style.display = 'block';
                        });
                }

                function closePopup() {
                    document.getElementById('popupModal').style.display = 'none';
                }
            </script>
            <script>
                function showPopup(paymentID) {
                    fetch('PHP/get_bill_details.php?paymentID=' + paymentID)
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('popupContent').innerHTML = data;
                            document.getElementById('popupModal').style.display = 'block';
                        });
                }

                function closePopup() {
                    document.getElementById('popupModal').style.display = 'none';
                }

                function printReceipt() {
                    const content = document.getElementById("popupContent").innerHTML;
                    const win = window.open('', '', 'width=600,height=900');
                    win.document.write('<html><head><title>Receipt</title></head><body>' + content + '</body></html>');
                    win.document.close();
                    win.print();
                }


            </script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
            <script src="js/jquery-3.2.1.min.js"></script>
            <script src="js/fontawesome-all.js"></script>
            <script src="js/bootstrap.bundle.min.js"></script>



</body>