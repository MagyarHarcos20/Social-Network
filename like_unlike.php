<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST['post_id'];

    if (isset($_POST['like'])) {
        $sql = "INSERT INTO likes (user_id, post_id) VALUES ('$user_id', '$post_id')";
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Hiba: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['unlike'])) {
        $sql = "DELETE FROM likes WHERE user_id='$user_id' AND post_id='$post_id'";
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Hiba: " . $sql . "<br>" . $conn->error;
        }
    }
}
$conn->close();
