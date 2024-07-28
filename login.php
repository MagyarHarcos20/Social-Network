<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
        } else {
            $message = "Hibás jelszó/felhasználónév - Kérlek próbáld újra";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
    } else {
        $message = "Hibás jelszó/felhasználónév - Kérlek próbáld újra";
        echo "<script type='text/javascript'>alert('$message');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="sign.css">
</head>
<body>
    <h2>Bejelentkezés</h2>
    <div id="felulet">
    <form method="post">
        <p>Felhasználónév </p> <input id="username" type="text" name="username" required><br>
        <p>Jelszó </p> <input id="password" type="password" name="password" required><br>
        <input id="login" type="submit" value="Bejelentkezés"><br>
        <a id="link" href="register.php">Még nincs fiókom!</a>
    </form>
    </div>
</body>
</html>