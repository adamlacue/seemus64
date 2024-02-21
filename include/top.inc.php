<?php
session_start();




function redirectJS($newActivity,$redirReason,$otherParams="") {
  echo("<script>window.location='index.php?activity=".$newActivity."&reason=".$redirReason."&".$otherParams."';</script>");
  return true;
}

function formRequest($formName) {
  if(isset($_REQUEST[$formName])) {
    return $_REQUEST[$formName];
  } else {
    return "";
  }
}

$servername = "twyxt.io";
$username = "ben";
$password = "Iamlike12";
$dbname = "seemus_ben";




try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Successful connection";
  } catch(PDOException $e) {
  echo $e->getMessage();
  }
  
?>