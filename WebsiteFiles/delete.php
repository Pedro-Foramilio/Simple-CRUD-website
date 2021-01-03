<?php
require_once "util.php";
require_once "pdo.php";
session_start();
if (isset($_SESSION["user_id"]) && isset($_SESSION["name"])){
    if (isset($_POST['cancel'])){
        header("Location: index.php");
        return;
    }
    if (!isset($_GET["profile_id"]) && isset($_POST["profile_id"])){
        $_GET["profile_id"] = $_POST["profile_id"];
    }

    loadProfileData($pdo);
    
    if (isset($_POST["delete"]) && isset($_POST["profile_id"])){
        $sql = "DELETE FROM profile WHERE profile_id = :p_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(":p_id" => $_POST["profile_id"]));
        $_SESSION["ok"] = "Profile deleted";
        error_log("Deleted profil_id = ".$_POST["profile_id"]);
        header("Location: index.php");
        return;
    }
}else{die("ACCESS DENIED");}
?>
<!DOCTYPE html>
<html>
<head>
<title>Pedro Foramilio</title>
</head>
<body>
<h1>Deleteing Profile</h1>
<form method="post" action="delete.php">
<p>First Name: <?= $f_nm ?> </p>
<p>Last Name: <?= $l_nm ?> </p>
<input type="hidden" name="profile_id" value= <?= $p_id ?> >
<input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
</body>
</html>
