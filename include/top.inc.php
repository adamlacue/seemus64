<?php

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
  

  function formRequest($inputName) {
    if(isset($_REQUEST[$inputName])) {
      return $_REQUEST[$inputName];
    }
    return "";
  }
  
  
  

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

