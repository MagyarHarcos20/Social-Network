<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];
    $image = "";

    if ($_FILES['image']['name']) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = $target_file;
    }

    $sql = "INSERT INTO posts (user_id, content, image) VALUES ('$user_id', '$content', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo "Sikeres posztolás!";
    } else {
        echo "Hiba: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Új poszt</title>
</head>
<body>
    <h2>Új poszt</h2>
    <form method="post" enctype="multipart/form-data">
        Szöveg: <textarea name="content"></textarea><br>
        Kép: <input type="file" name="image"><br>
        <input type="submit" value="Posztolás">
    </form>
</body>
</html>
