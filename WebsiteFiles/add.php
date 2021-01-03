<?php
require_once "util.php";
require_once "pdo.php";
session_start();

if (isset($_SESSION["user_id"]) && isset($_SESSION["name"])){
    if (isset($_POST['cancel'])){
        header("Location: index.php");
        return;
    }

    if (isset($_POST["first_name"]) && isset($_POST["last_name"]) && 
        isset($_POST["email"]) && isset($_POST["headline"]) && isset($_POST["summary"])){

            if ( empty($_POST["first_name"]) || empty($_POST["last_name"]) ||empty($_POST["email"]) ||
                 empty($_POST["headline"]) || empty($_POST["summary"]) ){

                    $_SESSION["error"] = "All fields are required";
                    error_log("All fields are required");
                    header("Location: add.php");
                    return;

                }

            if ( strpos($_POST["email"], '@') === false ){
                $_SESSION['error'] = "Email address must contain @";
                error_log("Email address must contain @");
                header("Location: add.php");
                return;
            }

            $msg = validatePos();
            if ( is_String($msg) ){
                $_SESSION['error'] = $msg;
                header("Location: add.php");
                return;
            }
            $msg = validateEdu();
            if ( is_String($msg) ){
                $_SESSION['error'] = $msg;
                header("Location: add.php");
                return;
            }

        
            $stmt = $pdo->prepare("INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary)
                VALUES (:uid, :fn, :ln, :em, :he, :su)");
            $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary']));

            $profile_id = $pdo->lastInsertId();

            insertPos($pdo, $profile_id);
            
            insertEducations($pdo, $profile_id);
            
            $_SESSION['ok'] = "Profile added";
            error_log("add success for first_name = ".$_POST["first_name"]);
            header("Location: index.php");
            return;
        }
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
<h1>Adding Profile for <?= htmlentities($_SESSION['name']) ?></h1>
<?php
flashMsg();
?>
<form method="post">
<p>First Name:
    <input type="text" name="first_name" size="60"/></p>
<p>Last Name:
    <input type="text" name="last_name" size="60"/></p>
<p>Email:
    <input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
    <input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
    <textarea name="summary" rows="8" cols="80"></textarea></p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countPos = 0;
countEdu = 0;
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

});
</script>
</body>
</html>
