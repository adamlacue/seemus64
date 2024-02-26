<?php
session_start(); //allow for session variables in the app.

include "include/top.inc.php";
include "class/Utils.class.php";

// Utils::prettyPrint("SELECT * FROM Seemus.tbTable;")

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if(isset($_REQUEST["activity"])) {
    $activity = $_REQUEST["activity"];
} else {
    $activity = "DEFAULT";
}

?>
<html>
    <head>
        <title><?php echo $activity ?></title>
    </head>
    <body>
<?php 
    if($_SESSION["username"]) { 
                ?>
                
                <a href="index.php?activity=FILES">FILES</a>
                
                <?php
            }

            if($_SESSION["Email"]!="") {
              ?><a href="index.php?activity=LOGOUT" title="Logoff User: <?php echo $_SESSION["username"]; ?>">LOGOFF</a><?php
          } else {
              ?><a href="index.php?activity=USER">LOGON</a><?php
          }
            ?>
      |<a href="index.php?activity=USER-CREATE">USER-CREATE</a>

        <?php
        
         if(!formRequest("reason")=="") {
             echo "<div class=\"reason\">" . formRequest("reason") . "</div>";
         }


          

        
        switch($activity) {
            case "USER":
                // User Login
                if(!isset($_REQUEST["username"])) {
                    ?>
                    <form action="index.php" method="post">
                        <input type="hidden" name="activity" value="USER" />
                        <input type="text" name="username" placeholder="Username / email" />
                        <input type="password" name="password" placeholder="Password" />
                        <input type="submit" value="Logon" />
                    </form>
                    <?php
                } else {
                    $username = $_REQUEST["username"];
                    $password = $_REQUEST["password"];
                    // Check credentials against database
                    $stmt = $conn->prepare("SELECT * FROM tbUsers WHERE username = :username");
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        if (password_verify($password, $user['password'])) {
                            echo "<br>" . $username . " is logged on!";
                            $_SESSION["username"] = $username;
                        } else {
                            echo "<br>Invalid password";
                        }
                    } else {
                        echo "<br>Invalid username";
                    }
                }
                
            break;



            case "FILE-CREATE-PROCESS":
              // echo $_FILES['File']["name"];
            if($activity=="FILE-CREATE-PROCESS") {
              if (count($_FILES) > 0) {
                if (is_uploaded_file($_FILES['File']['tmp_name'])) {
                    $fdFile = file_get_contents($_FILES['File']['tmp_name']);
                    $fdFileType = $_FILES['File']['type'];
                    $fdFileName = $_FILES['File']['name'];
                    $fdFileSize = $_FILES['File']['size'];
                    $fdDateTime = date('Y-M-D G:i:s');
                    
                    $sql = "INSERT INTO tbFiles ( fdFileType , fdFile, fdFileName, fdFileSize, fdDateTime, fdArchive) 
                                        VALUES  (:fdFileType ,:fdFile,:fdFileName,:fdFileSize, now(),0)";
                    $statement = $conn->prepare($sql);
                    $statement->bindParam('fdFile',    $fdFile,      PDO::PARAM_STR);
                    $statement->bindParam('fdFileType',$fdFileType,  PDO::PARAM_STR);
                    $statement->bindParam('fdFileName',$fdFileName,  PDO::PARAM_STR);
                    $statement->bindParam('fdFileSize',$fdFileSize,  PDO::PARAM_INT);
                    
                    $current_id = $statement->execute();
                }
            }
          }

          case "FILE-DELETE-PROCESS":
            if($activity=="FILE-DELETE-PROCESS") {
                    $sql = "DELETE FROM tbFiles WHERE id = ". formRequest("id");
                    $statement = $conn->prepare($sql);
                    $current_id = $statement->execute();
            }


          case "FILES": // File Listing

              ?><br>
              <form action="index.php" method="post" enctype="multipart/form-data">
              <input type="hidden" name="activity" value="FILE-CREATE-PROCESS">
              <input type="hidden" name="order" value="<?php echo formRequest("order"); ?>">
              <input type="file" name="File" placeholder="File" value="">
              <input type="submit" name="Submit" value="UPLOAD!"><br>
              </form>
              <?php

              $sql = "SELECT id,fdFilename,fdFileType,fdFileSize,fdDateTime,fdArchive FROM `tbFiles`";

              $order=formRequest("order");
              if($order!=""){
                $sql = $sql . "ORDER BY $order";
              }
              
              $stmt = $conn->prepare($sql);
              $stmt->execute();
              $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
              // Check if $result has anything in it or not (Returns a FALSE if no data in there).
              if($result) {
                echo "<table border=1>";   // Start Table
                $firstRowPrinted = false;
                $i=1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  if($firstRowPrinted == false) {
                    echo "<tr>";               // Start HEADER Row
                    echo "<th>##</th>";
                    //echo "<th>UPDATE</th>";
                    echo "<th>DELETE</th>";
                    echo "<th>VIEW</th>";
                    foreach($row as $col_name => $val) {
                      if($order == "`$col_name`") {
                        echo "<th><a href=\"index.php?activity=FILES&order=`$col_name` DESC\">$col_name</a></th>";    
                      } else {
                        echo "<th><a href=\"index.php?activity=FILES&order=`$col_name`\">$col_name</a></th>"; 
                      }
                    }
                    echo "</tr>";               // END Header Row
                    $firstRowPrinted = true;
                  }
                  echo "<tr>";               // Start Row
                  echo "<td>" . $i . "</td>";
                  $i=$i+1;
                  //echo "<td><a href=\"index.php?activity=FILE-UPDATE-FORM&id=" . $row["id"] . "&order=$order\">UPDATE</a></td>";
                  echo "<td><a href=\"index.php?activity=FILE-DELETE-PROCESS&id=".$row["id"]."&order=$order\">DELETE</a></td>";
                  echo "<td><a href=\"view.php?id=".$row["id"]."\" target=\"_blank\">VIEW</a></td>";
                  foreach($row as $col_name => $val) {
                    echo "<td>$val</td>";    // Print Each Field VALUE
                  }
                  echo "</tr>";               // Start Row
                }
                echo "</table>";
              }

              break;
                      

                    


                    
           

            case "LOGOUT":
                // User Logout

                // remove all session variables
                session_unset();

                // destroy the session
                session_destroy(); 
            break;

            case "VIEW":
                // View List of Content

            break;
            

            
            case "CREATE":
              // C of Crud
              //Insert (SQL Language) Data!
              //echo $activity . " in INSERT section";
              ?>
              <form action="index.php">
                <input type="hidden" name="activity" value="CREATE-PROCESS">
                <input type="text" name="firstname" placeholder="firstname"><br>
                <input type="text" name="lastname" placeholder="lastname"><br>
                <input type="text" name="email" placeholder="email"><br>
                <input type="text" name="phone" placeholder="phone"><br>
                <input type="submit" value="GO!"><br>
              </form>
              <?php
              // Going to add forms to INSERT/CREATE new data in the DB
              // Have those forms submit with content to add to the DB
        
            break;
        
            case "UPDATE-FORM":
              // U of crUd
              //Show forms for update!
              //echo $activity . " in UPDATE-FORM section";
        
              $stmt = $conn->prepare("SELECT * FROM `tbUser` WHERE id = ". formRequest("id"));
              $stmt->execute();
              $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
              // Check if $result has anything in it or not (Returns a FALSE if no data in there).
              if($result) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <form action="index.php">
                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                <input type="hidden" name="activity" value="UPDATE-PROCESS">
                <input type="hidden" name="order" value="<?php echo formRequest("order"); ?>">
                <input type="text" name="firstname" placeholder="firstname" value="<?php echo $row["firstname"]; ?>"><br>
                <input type="text" name="lastname" placeholder="lastname" value="<?php echo $row["lastname"]; ?>"><br>
                <input type="text" name="email" placeholder="email" value="<?php echo $row["email"]; ?>"><br>
                <input type="text" name="username" placeholder="username" value="<?php echo $row["username"]; ?>"><br>
                <input type="submit" value="UPDATE!"><br>
                </form>
                <?php
                }
              }
              break;
        
        
            case "CREATE-PROCESS":
              //echo $activity . " in INSERT Processing section";
        
              $firstname = formRequest("firstname");
              $lastname = formRequest("lastname");
              $email = formRequest("email");
              $phone = formRequest("phone");
        
              if($activity=="CREATE-PROCESS") {
                $sql = "INSERT INTO `tbUsers` (`firstname`, `lastname`, `email`, `username`) VALUES ('" . $firstname . "','" . $lastname . "','" . $email . "','" . $phone . "')";
                $conn->exec($sql);
                echo "INSERTED: " . $conn->lastInsertId() . "<BR><BR>";
              }
              //echo "<BR>".$sql."<BR>";
              //break; //no break needed so we show the records again  
            
            case "UPDATE-PROCESS":
              // U of crUd
              //Update actual Data!
              //echo $activity . " in UPDATE-PROCESS section";
          
              $firstname = formRequest("firstname");
              $lastname = formRequest("lastname");
              $email = formRequest("email");
              $phone = formRequest("phone");
          
            if($activity=="UPDATE-PROCESS") {
              $sql = "UPDATE `tbUsers` 
                      SET `firstname`='" . $firstname . "',
                          `lastname`='" . $lastname . "', 
                          `email`='" . $email . "', 
                          `phone`='" . $phone . "'
                      WHERE id = " . formRequest("id");
              $conn->exec($sql);
              echo "UPDATED: " . formRequest("id") . "<BR>";
        
            }    
            //  echo "<BR>".$sql."<BR>";
              //break; // no Break to show the list again below!
        
            case "DELETE-PROCESS":
              //D of cruD
              //PROCESS for the Deleting of Data!
              //echo $activity . " in DELETE PROCESS section";
        
            if($activity=="DELETE-PROCESS") {
              $sql = "DELETE FROM `tbUsers` WHERE `id` = " .formRequest("id");
              $conn->exec($sql);
              echo "DELETED: " . formRequest("id") . "<BR><BR>";
            }










            case "USER-CREATE":
              // C of Crud
              //Insert (SQL Language) Data!
              //echo $activity . " in INSERT section";
              ?>
              <form action="index.php">
                <input type="hidden" name="activity" value="USER-CREATE-PROCESS">
                <input type="text" name="fdEmail" placeholder="fdEmail"><br>
                <input type="text" name="fdFullName" placeholder="fdFullName"><br>
                <input type="text" name="fdNickName" placeholder="fdNickName"><br>
                <input type="text" name="fdAdmin" placeholder="fdAdmin"><br>
                <input type="text" name="fdPassword" placeholder="fdPassword"><br>
                <input type="submit" value="GO!"><br>
              </form>
              <?php
              // Going to add forms to INSERT/CREATE new data in the DB
              // Have those forms submit with content to add to the DB
        
            break;
        
            case "USER-UPDATE-FORM":
              // U of crUd
              //Show forms for update!
              //echo $activity . " in UPDATE-FORM section";
        
              $stmt = $conn->prepare("SELECT * FROM `tbUser` WHERE id = ". formRequest("id"));
              $stmt->execute();
              $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
              // Check if $result has anything in it or not (Returns a FALSE if no data in there).
              if($result) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <form action="index.php">
                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                <input type="hidden" name="activity" value="UPDATE-PROCESS">
                <input type="hidden" name="order" value="<?php echo formRequest("order"); ?>">
                <input type="text" name="firstname" placeholder="firstname" value="<?php echo $row["firstname"]; ?>"><br>
                <input type="text" name="lastname" placeholder="lastname" value="<?php echo $row["lastname"]; ?>"><br>
                <input type="text" name="email" placeholder="email" value="<?php echo $row["email"]; ?>"><br>
                <input type="text" name="username" placeholder="username" value="<?php echo $row["username"]; ?>"><br>
                <input type="submit" value="UPDATE!"><br>
                </form>
                <?php
                }
              }
              break;
        
        
            case "USER-CREATE-PROCESS":
              //echo $activity . " in INSERT Processing section";
        
              $firstname = formRequest("firstname");
              $lastname = formRequest("lastname");
              $email = formRequest("email");
              $phone = formRequest("phone");
        
              if($activity=="USER-CREATE-PROCESS") {
                $sql = "INSERT INTO `tbUsers` (`firstname`, `lastname`, `email`, `username`) VALUES ('" . $firstname . "','" . $lastname . "','" . $email . "','" . $phone . "')";
                $conn->exec($sql);
                echo "INSERTED: " . $conn->lastInsertId() . "<BR><BR>";
              }
              //echo "<BR>".$sql."<BR>";
              //break; //no break needed so we show the records again  
            
            case "USER-UPDATE-PROCESS":
              // U of crUd
              //Update actual Data!
              //echo $activity . " in UPDATE-PROCESS section";
          
              $firstname = formRequest("firstname");
              $lastname = formRequest("lastname");
              $email = formRequest("email");
              $phone = formRequest("phone");
          
            if($activity=="USER-UPDATE-PROCESS") {
              $sql = "UPDATE `tbUsers` 
                      SET `firstname`='" . $firstname . "',
                          `lastname`='" . $lastname . "', 
                          `email`='" . $email . "', 
                          `phone`='" . $phone . "'
                      WHERE id = " . formRequest("id");
              $conn->exec($sql);
              echo "UPDATED: " . formRequest("id") . "<BR>";
        
            }    
            //  echo "<BR>".$sql."<BR>";
              //break; // no Break to show the list again below!
        
            case "USER-DELETE-PROCESS":
              //D of cruD
              //PROCESS for the Deleting of Data!
              //echo $activity . " in DELETE PROCESS section";
        
            if($activity=="USER-DELETE-PROCESS") {
              $sql = "DELETE FROM `tbUsers` WHERE `id` = " .formRequest("id");
              $conn->exec($sql);
              echo "DELETED: " . formRequest("id") . "<BR><BR>";
            }













     default://RUN DEFAULT or READ always if blank or READ
     $sql = "SELECT * FROM `tbUsers`";
     $order=formRequest("order");
     if($order!=""){
       $sql = $sql . "ORDER BY $order";
     }
     $stmt = $conn->prepare($sql);
     $stmt->execute();
     $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

     // Check if $result has anything in it or not (Returns a FALSE if no data in there).
     if($result) {
       echo "<table border=1>";   // Start Table
       $firstRowPrinted = false;
       $i=1;
       while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         if($firstRowPrinted == false) {
           echo "<tr>";               // Start HEADER Row
           echo "<th>##</th>";
           echo "<th>UPDATE</th>";
           echo "<th>DELETE</th>";
           foreach($row as $col_name => $val) {
             if($order == "`$col_name`") {
               echo "<th><a href=\"index.php?order=`$col_name` DESC\">$col_name</a></th>";    
             } else {
               echo "<th><a href=\"index.php?order=`$col_name`\">$col_name</a></th>"; 
             }
           }
           echo "</tr>";               // END Header Row
           $firstRowPrinted = true;
         }
         echo "<tr>";               // Start Row
         echo "<td>" . $i . "</td>";
         $i=$i+1;
         echo "<td><a href=\"index.php?activity=UPDATE-FORM&id=" . $row["id"] . "&order=$order\">UPDATE</a></td>";
echo "<td><a href='index.php?activity=DELETE-PROCESS&id=".$row["id"]."&order=$order'>DELETE</a></td>";

         foreach($row as $col_name => $val) {
           echo "<td>$val</td>";    // Print Each Field VALUE
         }
         echo "</tr>";               // Start Row
       }
       echo "</table>";
     }
 }
     
    
        ?>
    </body>
</html>

<?php
include "include/bottom.inc.php";   



?>