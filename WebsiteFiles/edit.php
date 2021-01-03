<?php
require_once "pdo.php";
require_once "util.php";
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

    if (isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["email"]) && isset($_POST["headline"])
        && isset($_POST["summary"]) && isset($_POST["profile_id"])){

            if ( empty($_POST["first_name"]) || empty($_POST["last_name"]) ||empty($_POST["email"]) ||
            empty($_POST["headline"]) || empty($_POST["summary"]) ){

               $_SESSION["error"] = "All fields are required";
               error_log("All fields are required");
               header("Location: edit.php?profile_id=" . $_GET["profile_id"]);
               return;

           }

       if ( strpos($_POST["email"], '@') === false ){
           $_SESSION['error'] = "Email address must contain @";
           error_log("Email address must contain @");
           header("Location: edit.php?profile_id=" . $_GET["profile_id"]);
           return;
        }

        $_SESSION["ok"] = "Profile added";
        $sql = "UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :hl, summary = :sm WHERE profile_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
                ":fn" => $_POST["first_name"],
                ":ln" => $_POST["last_name"],
                ":em" => $_POST["email"],
                ":hl" => $_POST["headline"],
                ":sm" => $_POST["summary"],
                ":id" => $_POST["profile_id"]
            ));
        
        //clearing out
        $stmt = $pdo->prepare("DELETE FROM Position WHERE profile_id = :pid");
        $stmt->execute(array(":pid" => $_POST["profile_id"]));
        $stmt = $pdo->prepare("DELETE FROM Education WHERE profile_id = :pid");
        $stmt->execute(array(":pid" => $_POST["profile_id"]));



        //insert position entries
        $msg = validatePos();
        if ( is_String($msg) ){
            $_SESSION['error'] = $msg;
           header("Location: edit.php?profile_id=" . $_GET["profile_id"]);
            return;
        }
        $msg = validateEdu();
        if ( is_String($msg) ){
            $_SESSION['error'] = $msg;
           header("Location: edit.php?profile_id=" . $_GET["profile_id"]);
            return;
        }

        $profile_id = $_POST["profile_id"];
        insertPos($pdo, $profile_id);
        insertEducations($pdo, $profile_id);

        error_log("updated profile_id = " . $_POST["profile_id"]);
        header("Location: index.php");
        return;

    }

$positions = loadPos($pdo, $_GET["profile_id"]);
$schools = loadEdu($pdo, $_GET['profile_id']);
}else{die("ACCESS DENIED");}
?>
<!DOCTYPE html>
<html>
<head>
<title>Pedro Foramilio</title>
<script src="https://code.jquery.com/jquery-3.2.1.js" 
integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.2.1.js" 
integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" 
integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>
<h1>Editing Profile for <?= htmlentities($_SESSION['name']) ?></h1>
<?php
flashMsg();
?>
<form method="post" action="edit.php">
<p>First Name:
    <input type="text" name="first_name" size="60"value= <?= $f_nm ?> >
</p>
<p>Last Name:
    <input type="text" name="last_name" size="60"value=<?= $l_nm ?> >
</p>
<p>Email:
    <input type="text" name="email" size="30"value=<?= $e_ml ?> >
</p>
<p>Headline:<br/>
    <input type="text" name="headline" size="80"value=<?= $h_ln ?> >
</p>
<p>Summary:<br/>
    <textarea name="summary" rows="8" cols="80"> <?=$smry?> </textarea>
</p>
<?php
$countEdu = 0;
echo('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
echo('<div id="edu_fields">'."\n");
if (count($schools) > 0){
    foreach( $schools as $school){
        $countEdu++;
        echo('<div id="edu'.$countEdu.'">');
        echo
'<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.$school['year'].'" />
<input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove();return false;"</p>
<p>School: <input type="text" size="80" name="edu_school'.$countEdu.'" class="school"
value="'.htmlentities($school['name']).'" />';
        echo "\n</div>\n";
    }
}
echo("</div></p>\n");


?>
<?php
$pos = 0;
echo('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
echo('<div id="position_fields">'."\n");
foreach( $positions as $position){
    $pos++;
    echo('<div id="position'.$pos.'">'."\n");
    echo('<p>Year: <input type="text" name="year'.$pos.'"');
    echo(' value="'.$position['year'].'" />'."\n");
    echo('<input type="button" value="-"');
    echo('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
    echo("</p>\n");
    echo('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
    echo(htmlentities($position['description'])."\n");
    echo("\n</textarea>\n</div>\n");
}
echo("</div></p>\n");

?>
<p>
    <input type="hidden" name="profile_id" value= <?= $p_id ?> >
    <input type="submit" value="Save">
    <input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script id="edu-template" type="text">
    <div id="edu@COUNT@">
    <p>Year: <input type="text" name="edu_year@COUNT@" value="" />
    <input type="button" value="-" onclick="$('#edu@COUNT@').remove(); return false;"><br>
    <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" />
    </p>
    </div>
</script>
<script>
countPos = <?= $pos ?>;
countEdu = <?= $countEdu ?>;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);

        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" />\
            </p></div>'
        );

        $('.school').autocomplete({
            source: "school.php"
        });

    });
    $('.school').autocomplete({
            source: "school.php"
        });
});
</script>
</body>
</html>
