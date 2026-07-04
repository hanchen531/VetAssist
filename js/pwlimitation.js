function validatePassword() {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (password.length === 0 && confirmPassword.length === 0) {
        return true;
    }

    const minLength = 8;
    const hasUppercase = /[A-Z]/;
    const hasLowercase = /[a-z]/;
    const hasNumber = /[0-9]/;
    const hasSymbol = /[!@#$%^&*(),.?":{}|<>]/;

    if (password.length < minLength) {
        alert("Password must be at least 8 characters long!");
        return false;
    }
    if (!hasUppercase.test(password)) {
        alert("Password must contain at least one uppercase letter!");
        return false;
    }
    if (!hasLowercase.test(password)) {
        alert("Password must contain at least one lowercase letter!");
        return false;
    }
    if (!hasNumber.test(password)) {
        alert("Password must contain at least one number!");
        return false;
    }
    if (!hasSymbol.test(password)) {
        alert("Password must contain at least one special character!");
        return false;
    }
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false;
    }

    return true;
}


