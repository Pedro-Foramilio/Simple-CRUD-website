<?php
require_once "pdo.php";
require_once "util.php";
session_start();
if (isset($_POST["email"]) && isset($_POST["pass"])){
    unset($_SESSION["user_id"]);
    unset($_SESSION["name"]);
    $salt = 'XyZzy12*_';

    if (isset($_POST['cancel'])){
        header("Location: index.php");
        return;
    }

    $check = hash('md5', $salt.$_POST['pass']);
    $stmt = $pdo->prepare("SELECT user_id, name FROM users WHERE email= :em AND password= :pw");
    $stmt->execute(array(
        ':em' => $_POST['email'],
        ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row !== false){
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        error_log("Login Sucess for user_id = ".$row['user_id']);
        header("Location: index.php");
        return;
    } else {
        error_log("Login failed");
        $_SESSION['error'] = "Incorrect password";
        header("Location: login.php");
        return;
    }
}
?>
<!DOCTYPE html>
<head>
<title>Pedro Foramilio</title>
</head>
<body>
<h1>Please Log In</h1>
<?php
flashMsg();
?>
<form method="POST">

<label for="email">Email</label>
<input type="text" name="email" id="email"><br>
<label for="password">Password</label>
<input type="password" name="pass" id="password"><br>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">

</form>
<script>
function doValidate(){
    console.log('Validating...');
    try{
        addr = document.getElementById('email').value;
        pw = document.getElementById('password').value;
        console.log("Validating addr="+addr+" pw="+pw);

        if (addr == null || addr == "" || pw == null || pw == ""){
            alert("Both fields must be filled out");
            return false;
        }
        if (addr.indexOf('@') == -1 ){
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}

</script>
</body>
</html>
