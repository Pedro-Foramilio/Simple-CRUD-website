<?php
function loadPos($pdo, $profile_id){
    $stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank");
    $stmt->execute(array(":prof" => $profile_id));
    $positions = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $positions[] = $row;
    }
    return $positions;
}

function loadEdu($pdo, $profile_id){
  $stmt = $pdo->prepare('SELECT year, name FROM Education
      JOIN Institution ON Education.institution_id = Institution.institution_id
      WHERE profile_id = :prof ORDER BY rank');
  $stmt->execute(array(':prof' => $profile_id));
  $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $educations;
}

function flashMsg(){
    if (isset($_SESSION['ok'])){
        echo '<p style="color:green">';
        echo $_SESSION['ok'];
        echo '</p>';
        unset($_SESSION['ok']);
    }
    if (isset($_SESSION['error'])){
        echo '<p style="color:red">';
        echo $_SESSION['error'];
        echo '</p>';
        unset($_SESSION['error']);
    }
}

function validatePos() {
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['year'.$i]) ) continue;
      if ( ! isset($_POST['desc'.$i]) ) continue;
  
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
  
      if ( strlen($year) == 0 || strlen($desc) == 0 ) {
        return "All fields are required";
      }
  
      if ( ! is_numeric($year) ) {
        return "Position year must be numeric";
      }
    }
    return true;
}

function insertEducations($pdo, $profile_id){
    $rank = 1;
    for ($i = 1; $i <= 9; $i++){
        if (! isset($_POST['edu_year'.$i])) continue;
        if (! isset($_POST['edu_school'.$i])) continue;
        $year = $_POST['edu_year'.$i];
        $school = $_POST['edu_school'.$i];

        $institution_id = false;
        $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
        $stmt->execute(array(':name' => $school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row !== false) $institution_id = $row['institution_id'];

        //if there was no inst, insert it
        if ($institution_id === false) {
            $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
            $stmt->execute(array(':name' => $school));
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, rank, year, institution_id)
            VALUES (:pid, :rank, :year, :iid)');
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':iid' => $institution_id
        ));
        $rank++;

    }
}

function insertPos($pdo, $profile_id){
  $rank = 1;
  for ($i = 1; $i <= 9; $i++) {
    if (!isset($_POST['year' . $i])) continue;
    if (!isset($_POST['desc' . $i])) continue;

    $year = $_POST['year' . $i];
    $desc = $_POST['desc' . $i];

    $stmt = $pdo->prepare("INSERT INTO Position (profile_id, rank, year, description) 
          VALUES ( :pid, :rank, :year, :desc)");

    $stmt->execute(array(
      ':pid' => $profile_id,
      ':rank' => $rank,
      ':year' => $year,
      ':desc' => $desc
    ));
    $rank++;
  }
}

function validateEdu() {
    for( $i=1; $i<=9; $i++){
      if ( ! isset($_POST['edu_year'.$i]) ) continue;
      if ( ! isset($_POST['edu_school'.$i]) ) continue;

      $year = $_POST['edu_year'.$i];
      $school = $_POST['edu_school'.$i];

      if ( strlen($year) == 0 || strlen($school) == 0 ) {
        return "All fields are required";
      }
      if ( ! is_numeric($year) ) {
        return "Position year must be numeric";
      }
      return true;

    }
}

function loadProfileData($pdo){
  $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :pid ");
  $stmt->execute(array(":pid" => $_GET["profile_id"]));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row === false){
        $_SESSION["error"] = "Could not load profile";
        error_log("Error retrieving profile_id='" . $_GET["profile_id"] . "'");
        header("Location: index.php");
        return;
  }
  //else
  global $u_id, $p_id, $f_nm, $l_nm, $e_ml, $h_ln, $smry;
  $u_id = $row["user_id"];
  $p_id = $row["profile_id"];
  $f_nm = htmlentities($row["first_name"]);
  $l_nm = htmlentities($row["last_name"]);
  $e_ml = htmlentities($row["email"]);
  $h_ln = htmlentities($row["headline"]);
  $smry = htmlentities($row["summary"]);
}
