<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Utils::prettyPrint("SELECT * FROM Seemus.tbTable;");

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
  
  
  

// try {

//     $conn->beginTransaction();

//     $conn->exec("INSERT INTO MyGuests (firstname, lastname, email) 
//     VALUES ('John3', 'Doe', 'john@example.com')");
//     $conn->exec("INSERT INTO MyGuests (firstname, lastname, email) 
//     VALUES ('Mary', 'Moe', 'mary@example.com')");
//     $conn->exec("INSERT INTO MyGuests (firstname, lastname, email) 
//     VALUES ('Julie', 'Dooley', 'julie@example.com')");
    
//     $conn->commit();
//     echo "\nNew record created successfully";
//   } catch(PDOException $e) {
//     echo $sql . "<br>" . $e->getMessage();
//   }

