<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = $_POST["password"];

    $hash = password_hash($password, PASSWORD_DEFAULT);

    echo "<h3>Generated Hash:</h3>";
    echo "<textarea rows='5' cols='80'>";
    echo $hash;
    echo "</textarea>";
}
?>

<form method="POST">
    <input type="text" name="password" placeholder="Enter password">
    <button type="submit">Generate Hash</button>
</form>