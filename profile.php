<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $profile_image = $_FILES['profile_image']['name'];

    if ($profile_image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);

        $sql = "UPDATE users SET username=?, profile_image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $target_file, $user_id);
    } else {
        $sql = "UPDATE users SET username=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $username, $user_id);
    }

    if ($stmt->execute() === TRUE) {
        header("Location: profile.php");
        exit;
    } else {
        echo "Hiba: " . $stmt->error;
    }
    $stmt->close();
}

$sql = "SELECT username, profile_image FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $profile_image);
$stmt->fetch();
$stmt->close();

$followers_sql = "SELECT COUNT(*) FROM follows WHERE followed_id = ?";
$stmt = $conn->prepare($followers_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($followers_count);
$stmt->fetch();
$stmt->close();

$sql_posts = "SELECT content, image, created_at FROM posts WHERE user_id=? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql_posts);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_posts = $stmt->get_result();
$stmt->close();

$sql_followers = "SELECT users.username FROM follows JOIN users ON follows.follower_id = users.id WHERE follows.followed_id=?";
$stmt = $conn->prepare($sql_followers);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_followers = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div id="menu">
        <a href="index.php"><img id="home-png" src="uploads/home.png" alt=""><a href="post.php"><img id="write-png" src="uploads/write_post.jpg"></a> <a href="profile.php"><img id="user-png" src="uploads/user.png"></a> <a href="logout.php"><img id="logout-png" src="uploads/logout.png"></a>
    </div>
    <div id="profil">
    <h2>Profilom</h2>
    <form method="post" enctype="multipart/form-data">
        Felhasználónév: <input id="change-usr" type="text" name="username" value="<?php echo $username; ?>" required><br>
        Profilkép: <input id="profile-pic" type="file" name="profile_image"><br>
        <input id="refresh" type="submit" name="update_profile" value="Frissítés">
    </form>

    <h3>Posztjaim</h3>
    <?php
    if ($result_posts->num_rows > 0) {
        while($row = $result_posts->fetch_assoc()) {
            echo "<div>";
            echo "<p>" . $row['content'] . "</p>";
            if ($row['image']) {
                echo "<img src='" . $row['image'] . "' width='200'><br>";
            }
            echo "<small>" . date('Y-m-d H:i', strtotime($row['created_at'])) . "</small>";
            echo "</div><hr>";
        }
    } else {
        echo "Nincsenek posztjaid.";
    }
    ?>

    <h3>Követőim száma: <?php echo $followers_count; ?></h3>
    <?php
    if ($result_followers->num_rows > 0) {
        while($row = $result_followers->fetch_assoc()) {
            echo "<p>" . $row['username'] . "</p>";
        }
    } else {
        echo "Nincsenek követőid.";
    }
    ?>
    </div>
</body>
</html>
