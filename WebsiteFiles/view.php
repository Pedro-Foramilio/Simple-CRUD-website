<?php
require_once "util.php";
require_once "pdo.php";
session_start();

if (isset($_GET["profile_id"])){

    loadProfileData($pdo);
    $positions = loadPos($pdo, $_GET["profile_id"]);
    $schools = loadEdu($pdo, $_GET['profile_id']);


}else{
    error_log("no profile id");
    $_SESSION["error"] = "No profile specified";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<head>
<title>Pedro Foramilio</title>
</head>
<body>
<h1>Profile information</h1>
<p>First Name: <?= $f_nm ?> </p>
<p>Last Name: <?= $l_nm ?> </p>
<p>Email: <?= $e_ml ?> </p>
<p>Headline:<br/> <?= $h_ln ?> </p>
<p>Summary:<br/>
<p> <?= $smry ?> </p>

<?php
$pos = 0;
echo("\n<p>Position</p><ul>");
foreach($positions as $position){
    echo('<li>'.$position['year'].': '.htmlentities($position['description'])."</li>\n");
}
echo("</ul>");

$edu =0;
echo("\n<p>Education</p><ul>");
foreach($schools as $school){
    echo('<li>'.$school['year'].': '.htmlentities($school['name'])."</li>\n");
}

?>
<a href="index.php">Done</a>
</body>
</html>
