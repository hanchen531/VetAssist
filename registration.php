<!DOCTYPE html>
<html lang="en-US">
<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
?>


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Registration</title>
    <link rel="icon" type="image/jpg" href="img/icon.png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/general.css">
    <link rel="stylesheet" href="css/registration.css">
</head>


<body>

    <header class="header">
        <div class="main-header">
            <div class="container">
                <div class="d-flex align-items-center justify-content-center">
                    <a href="index.php" class="justify-content-center">
                        <img src="img/logo-center.png" alt="VetAssistLogo" style="height: 50px; width: 156px;">
                    </a>
                </div>
            </div>
        </div>
    </header>


    <div class="container d-flex justify-content-center">
        <div class="login-container ">
            <form action="PHP/regi.php" method="POST" onsubmit="return validatePassword();">

                <div class="mb-3">
                    <label for="username" class="form-label">USERNAME</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">EMAIL</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3 ">
                    <label for="OTP" class="form-label">OTP From Email</label>
                    <div class="d-flex"><input type="text" name="otp" id="otp" class="form-control me-3" placeholder="Enter OTP" required>
                    <button type="button" class="btn-otp" onclick="sendOTP()">Send OTP</button></div>
                    
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">PASSWORD</label>
                    <input type="password" id="password" name="password" class="form-control" data-bs-toggle="tooltip"
                        data-bs-placement="right"
                        title="Password must include uppercase, lowercase, number, and symbol (with at least 8 characters)"
                        required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">CONFIRM PASSWORD</label>
                    <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
                </div>
                <div class="mb-3 btn-center">
                    <button type="submit" class="btn btn-secondary btn-custom">REGISTRATION</button>
                </div>


            </form>
        </div>
    </div>



    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/fontawesome-all.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/menu.min.js"></script>
    <script src="js/pwlimitation.js"></script>
    <script>
        function sendOTP() {
            const email = document.getElementById('email').value;
            if (!email) return alert("Please enter your email first.");

            fetch("PHP/OTP_SENDER.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "email=" + encodeURIComponent(email)
            })
                .then(response => response.text())
                .then(msg => alert(msg))
                .catch(() => alert("Failed to send OTP."));
        }
    </script>

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>

</body>