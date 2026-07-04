

<?php
include 'db_conn.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!isset($_GET['paymentID'])) {
    echo "Missing payment ID.";
    exit;
}

$paymentID = intval($_GET['paymentID']);

$sql = "
    SELECT 
        p.paymentID, p.amount, p.date, p.status, a.date AS apptDate,
        mfs.registrationFee, mfs.labTestFee, mfs.medicationFee, mfs.hospitalizationFee, mfs.otherFee, mfs.comment,
        u.username AS staffName,
        cu.username AS customerName
        FROM Payment p
        JOIN Appointment a ON p.appointmentID = a.appointmentID
    JOIN Customer c ON a.customerID = c.customerID
    JOIN User cu ON c.customerID = cu.userID
    JOIN ManageFinanceStatus mfs ON mfs.financeID = p.paymentID
    JOIN User u ON mfs.staffID = u.userID
    WHERE p.paymentID = $paymentID
";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $statusText = $row['status'] == 1 ? 'Paid' : 'Wait for payment';

    echo '
    <div style="font-family: Arial, sans-serif; color: #333; padding: 20px;">
        <div style="text-align: center;">
            <img src="img/logo-center.png" alt="VetAssist Logo" style="height: 60px; width:auto">
            <h2 style="margin-top: 10px; color: #007bff;">VetAssist Payment Receipt</h2>
            <hr style="border: 1px solid #007bff;">
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 15px;">
            <tr><td><strong>Payment Date:</strong></td><td>' . htmlspecialchars($row['apptDate']) . '</td></tr>
            <tr><td><strong>Customer:</strong></td><td>' . htmlspecialchars($row['customerName']) . '</td></tr>
            <tr><td><strong>Handled by:</strong></td><td>' . htmlspecialchars($row['staffName']) . '</td></tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr><td><strong>Registration Fee:</strong></td><td>RMB ' . $row['registrationFee'] . '</td></tr>
            <tr><td><strong>Test Fee:</strong></td><td>RMB ' . $row['labTestFee'] . '</td></tr>
            <tr><td><strong>Treatment Fee:</strong></td><td>RMB ' . $row['medicationFee'] . '</td></tr>
            <tr><td><strong>Hospital Fee:</strong></td><td>RMB ' . $row['hospitalizationFee'] . '</td></tr>
            <tr><td><strong>Other Fee:</strong></td><td>RMB ' . $row['otherFee'] . '</td></tr>';

    if (!empty($row['comment'])) {
        echo '<tr><td><strong>Other Fee Note:</strong></td><td>' . htmlspecialchars($row['comment']) . '</td></tr>';
    }

    echo '
            <tr><td colspan="2"><hr></td></tr>
            <tr><td><strong>Total Amount:</strong></td><td style="color: #dc3545;"><strong>RMB ' . $row['amount'] . '</strong></td></tr>
            <tr><td><strong>Status:</strong></td><td>' . $statusText . '</td></tr>
        </table>

        <div style="text-align: center; margin-top: 20px;">
            <small style="color: #777;">Thank you for trusting VetAssist. Please contact us if you have any billing questions.</small>
        </div>
    </div>';
} else {
    echo "<p style='color: red;'>❌ No details found for this payment.</p>";
}

?>
