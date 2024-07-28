<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        $message = "Sikeres regisztráció! Tovább a bejelentkezéshez.";
        echo "<script type='text/javascript'>alert('$message');</script>";
        header("Location: login.php");
    } else {
        $errormsg = "Hiba: " . $sql . "<br>" . $conn->error;
        echo "<script type='text/javascript'>alert('$errormsg');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="sign.css">
</head>
<body>
    <h2>Regisztráció</h2>
    <div id="felulet">
    <form method="post">
        <p>Felhasználónév </p> <input id="username" type="text" name="username" required><br>
        <p>Jelszó </p> <input id="password" type="password" name="password" required><br>
        <input id="register" type="submit" value="Regisztráció"><br>
        <a id="link" href="login.php">Már van fiókom!</a>
    </form>
    </div>
</body>
</html>