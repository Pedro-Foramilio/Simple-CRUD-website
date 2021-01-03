<?php
require_once "pdo.php";
session_start();
require_once "util.php";
?>
<!DOCTYPE html>
<head>
<title>Pedro Foramilio</title>
</head>
<body>
<h1>Resume Registry</h1>
<?php

flashMsg();

?>
<?php
echo '<table border="1"'."\n";
$sql = "SELECT users.name AS Name, profile.headline AS Headline, users.user_id AS user_id, profile.profile_id AS profile_id, profile.first_name AS first_name, profile.last_name AS last_name
    FROM users JOIN profile WHERE users.user_id = profile.user_id";
$stmt = $pdo->query($sql);
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
    echo "<thead><tr>";
    echo "<th>Name</th><th>Headline</th><th>Action</th>";
    echo "<tr><td>";
    echo '<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name'])." ".htmlentities($row['last_name']).'</a>';
    echo "</td><td>";
    echo htmlentities($row['Headline']);
    echo "</td>";
    if (isset($_SESSION['user_id'])){
        echo '<td><a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>';
        echo '<a href="delete.php?profile_id='.$row['profile_id'].'"> Delete</a></td>';
    }
    echo "</tr>\n";
}
echo "</table>\n";

if (isset($_SESSION["user_id"])){
    echo '<a href="logout.php">Logout</a><br>';
    //print out table linked to view.php
    echo '<a href="add.php">Add New Entry</a>';
}
else{
    echo '<a href="login.php">Please log in</a>';
}
?>
<br>
<a href="http://localhost/index.html">Back to main page</a>
</body>
</html>
