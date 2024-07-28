<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    $message = "Lejárt munkamenet, kérlek jelentkezz be újból.";
    echo "<script type='text/javascript'>alert('$message');</script>";
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$delete_sql = "DELETE FROM posts WHERE created_at < NOW() - INTERVAL 1 DAY";
$conn->query($delete_sql);

$sql = "SELECT posts.id, posts.content, posts.image, posts.created_at, users.username, users.profile_image,
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
        (SELECT COUNT(*) FROM likes WHERE likes.user_id = '$user_id' AND likes.post_id = posts.id) AS user_liked
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Főoldal</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Üdv, <?php echo $_SESSION['username']; ?>!</h2>
    <div id="menu">
        <a href="post.php"><img id="write-png" src="uploads/write_post.jpg"></a> <a href="profile.php"><img id="user-png" src="uploads/user.png"></a> <a href="logout.php"><img id="logout-png" src="uploads/logout.png"></a>
        <h2 id="posztok-felirat">Posztok</h2>
    </div>
    
    <div id="posts">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $formatted_date = date('Y-m-d H:i', strtotime($row['created_at']));
            echo "<div id='post_" . $row['id'] . "'>";
            if ($row['profile_image']) {
                echo "<img src='" . $row['profile_image'] . "' width='50' height='50' alt='Profilkép' class='profile-image'>";
            }
            echo "<h3 id='username'>" . $row['username'] . "</h3>";
            echo "<p>" . $row['content'] . "</p>";
            if ($row['image']) {
                echo "<img src='" . $row['image'] . "' width='200'><br>";
            }
            echo "<small>" . $formatted_date . "</small><br>";
            echo "<small>Like-ok száma: <span id='like_count_" . $row['id'] . "'>" . $row['like_count'] . "</span></small><br>";
            echo "<form method='post' class='like_form' data-postid='" . $row['id'] . "'>";
            if ($row['user_liked'] > 0) {
                echo "<input type='submit' name='unlike' value='Unlike'>";
            } else {
                echo "<input type='submit' name='like' value='Like'>";
            }
            echo "</form>";
            echo "</div><hr>";
        }
    } else {
        echo "Nincsenek posztok.";
    }
    $conn->close();
    ?>
    </div>

    <script>
    $(document).ready(function() {
        $('.like_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var post_id = form.data('postid');
            var action = form.find('input[type="submit"]').attr('name');

            $.ajax({
                url: 'like_unlike.php',
                type: 'POST',
                data: {
                    post_id: post_id,
                    [action]: true
                },
                success: function(response) {
                    var like_count_span = $('#like_count_' + post_id);
                    var like_count = parseInt(like_count_span.text());
                    
                    if (action == 'like') {
                        like_count_span.text(like_count + 1);
                        form.find('input[type="submit"]').attr('name', 'unlike').val('Unlike');
                    } else {
                        like_count_span.text(like_count - 1);
                        form.find('input[type="submit"]').attr('name', 'like').val('Like');
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
