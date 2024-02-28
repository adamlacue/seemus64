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
    if($_SESSION["fdEmail"]) { 
                ?>
                
                <a href="index.php?activity=FILES">FILES</a> | 
                <a href="index.php?activity=CONTENT">CONTENT</a> | 
                <a href="index.php?activity=USER-CREATE">USER-CREATE</a> | 
                <?php
            }

            if($_SESSION["fdEmail"]!="") {
              ?><a href="index.php?activity=LOGOUT" title="Logoff User: <?php echo $_SESSION["fdEmail"]; ?>">LOGOFF</a><?php
          } else {
              ?><a href="index.php?activity=USER">LOGON</a><?php
          }
            ?>


        <?php
        
         if(!formRequest("reason")=="") {
             echo "<div class=\"reason\">" . formRequest("reason") . "</div>";
         }


          

        
        switch($activity) {
            case "USER":
                // User Login
                if(!isset($_REQUEST["fdEmail"])) {
                    ?>
                    <form action="index.php" method="get">
                        <input type="hidden" name="activity" value="USER" />
                        <input type="text" name="fdEmail" placeholder="email" />
                        <input type="password" name="fdPassword" placeholder="Password" />
                        <input type="submit" value="Logon" />
                    </form>
                    <?php
                } else {
                    $fdEmail = $_REQUEST["fdEmail"];
                    $fdPassword = $_REQUEST["fdPassword"];
                    // Check credentials against database
                    $stmt = $conn->prepare("SELECT * FROM tbUsers WHERE fdEmail = :fdEmail");
                    $stmt->bindParam(':fdEmail', $fdEmail);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        if (password_verify($fdPassword, $user['fdPassword'])) {
                            echo "<br>" . $fdEmail . " is logged on!";
                            $_SESSION["fdEmail"] = $fdEmail;
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
                      







              case "CONTENT":

                ?>
                <br><form action="index.php">
                  <input type="hidden" name="activity" value="CONTENT-CREATE-PROCESS">
                  <input type="text" name="fdTitle" placeholder="fdTitle"><br>
                  <textarea name="fdHTML" style="width:500px; height:200px">
                  </textarea><br>
                  <input type="text" name="fdDateCreated" placeholder="fdDateCreated"><br>
                  <input type="text" name="fdDateUpdated" placeholder="fdDateUpdated"><br>
                  <input type="text" name="fdArchive" placeholder="fdArchive"><br>
                  <input type="submit" value="GO!"><br>
                </form>
                <?php

                $sql = "SELECT id,fdTitle,fdHTML,fdDateCreated,fdDateUpdated,fdArchive FROM `tbContent`";

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
                    echo "<td><a href=\"index.php?activity=CONTENT-DELETE-PROCESS&id=".$row["id"]."&order=$order\">DELETE</a></td>";
                    echo "<td><a href=\"content.php?id=".$row["id"]."\" target=\"_blank\">VIEW</a></td>";
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
            

            
            case "CONTENT-CREATE":
              // C of Crud
              //Insert (SQL Language) Data!
              //echo $activity . " in INSERT section";
              ?>
              <form action="index.php">
                <input type="hidden" name="activity" value="CONTENT-CREATE-PROCESS">
                <input type="text" name="fdTitle" placeholder="fdTitle"><br>
                <textarea name="fdHTML" style="width:500px; height:200px">
                  Insert text here
                </textarea><br>
                <input type="text" name="fdDateCreated" placeholder="fdDateCreated"><br>
                <input type="text" name="fdDateUpdated" placeholder="fdDateUpdated"><br>
                <input type="text" name="fdArchive" placeholder="fdArchive"><br>
                <input type="submit" value="GO!"><br>
              </form>
              <?php
              // Going to add forms to INSERT/CREATE new data in the DB
              // Have those forms submit with content to add to the DB
        
            break;
        
            case "CONTENT-UPDATE-FORM":
              // U of crUd
              //Show forms for update!
              //echo $activity . " in UPDATE-FORM section";
        
              $stmt = $conn->prepare("SELECT * FROM `tbContent` WHERE id = ". formRequest("id"));
              $stmt->execute();
              $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        
              // Check if $result has anything in it or not (Returns a FALSE if no data in there).
              if($result) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <form action="index.php">
                <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                <input type="hidden" name="activity" value="CONTENT-UPDATE-PROCESS">
                <input type="hidden" name="order" value="<?php echo formRequest("order"); ?>">
                <input type="text" name="fdTitle" placeholder="fdTitle" value="<?php echo $row["fdTitle"]; ?>"><br>
                <textarea name="fdHTML" style="width:500px; height:200px"><?php echo $row["fdHTML"]; ?>
                  Insert text here
                </textarea><br>
                <input type="text" name="fdDateCreated" placeholder="fdDateCreated" value="<?php echo $row["fdDateCreated"]; ?>"><br>
                <input type="text" name="fdDateUpdated" placeholder="fdDateUpdated" value="<?php echo $row["fdDateUpdated"]; ?>"><br>
                <input type="text" name="fdArchive" placeholder="fdArchive" value="<?php echo $row["fdArchive"]; ?>"><br>
                <input type="submit" value="UPDATE!"><br>
                </form>
                <?php
                }
              }
              break;
        
        
            case "CONTENT-CREATE-PROCESS":
              //echo $activity . " in INSERT Processing section";
        
              $fdTitle = formRequest("fdTitle");
              $fdHTML = formRequest("fdHTML");
              $fdDateCreated = formRequest("fdDateCreated");
              $fdDateUpdated = formRequest("fdDateUpdated");
              $fdArchive = formRequest("fdArchive");
        
              if($activity=="CONTENT-CREATE-PROCESS") {
                $sql = "INSERT INTO `tbContent` (`fdTitle`,         `fdHTML`,         `fdArchive`,     `fdDateCreated`, `fdDateUpdated`)
                                  VALUES ('" . $fdTitle . "', ? ,'" . $fdArchive . "',   now(),          now())";

                $stmt = $conn->prepare($sql);
                $stmt->execute($fdHTML);
               //echo $sql;
               //$conn->exec($sql);
                echo "INSERTED: " . $conn->lastInsertId() . "<BR><BR>";
              }
              //echo "<BR>".$sql."<BR>";
              //break; //no break needed so we show the records again  
            
            case "CONTENT-UPDATE-PROCESS":
              // U of crUd
              //Update actual Data!
              //echo $activity . " in UPDATE-PROCESS section";
          
              $fdTitle = formRequest("fdTitle");
              $fdHTML = formRequest("fdHTML");
              $fdDateCreated = formRequest("fdDateCreated");
              $fdDateUpdated = formRequest("fdDateUpdated");
              $fdArchive = formRequest("fdArchive");
          
            if($activity=="CONTENT-UPDATE-PROCESS") {
              $sql = "UPDATE `tbContent` 
                      SET `fdTitle`='" . $fdTitle . "',
                          `fdHTML`='" . $fdHTML . "', 
                          `fdDateCreated`='" . $fdDateCreated . "',
                          `fdDateUpdated`='" . $fdDateUpdated . "', 
                          `fdArchive`='" . $fdArchive . "'
                      WHERE id = " . formRequest("id");
              $conn->exec($sql);
              echo "UPDATED: " . formRequest("id") . "<BR>";
        
            }    
            //  echo "<BR>".$sql."<BR>";
              //break; // no Break to show the list again below!
        
            case "CONTENT-DELETE-PROCESS":
              //D of cruD
              //PROCESS for the Deleting of Data!
              //echo $activity . " in DELETE PROCESS section";
        
            if($activity=="CONTENT-DELETE-PROCESS") {
              $sql = "DELETE FROM `tbContent` WHERE `id` = " .formRequest("id");
              $conn->exec($sql);
              echo "DELETED: " . formRequest("id") . "<BR><BR>";
            }

            case "CONTENT": // File Listing

              $sql = "SELECT id,fdTitle,fdHTML,fdDateCreated,fdDateUpdated,fdArchive FROM `tbContent`";

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
                    echo "<th>VIEW</th>";
                    foreach($row as $col_name => $val) {
                      if($order == "`$col_name`") {
                        echo "<th><a href=\"index.php?activity=CONTENT&order=`$col_name` DESC\">$col_name</a></th>";    
                      } else {
                        echo "<th><a href=\"index.php?activity=CONTENT&order=`$col_name`\">$col_name</a></th>"; 
                      }
                    }
                    echo "</tr>";               // END Header Row
                    $firstRowPrinted = true;
                  }
                  echo "<tr>";               // Start Row
                  echo "<td>" . $i . "</td>";
                  $i=$i+1;
                  echo "<td><a href=\"index.php?activity=CONTENT-UPDATE-FORM&id=" . $row["id"] . "&order=$order\">UPDATE</a></td>";
                  echo "<td><a href=\"index.php?activity=CONTENT-DELETE-PROCESS&id=".$row["id"]."&order=$order\">DELETE</a></td>";
                  echo "<td><a href=\"view.php?id=".$row["id"]."\" target=\"_blank\">VIEW</a></td>";
                  foreach($row as $col_name => $val) {
                    echo "<td>$val</td>";    // Print Each Field VALUE
                  }
                  echo "</tr>";               // Start Row
                }
                echo "</table>";
              }

              break;








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
                <input type="text" name="fdArchive" placeholder="fdArchive"><br>
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
        
              $fdEmail = formRequest("fdEmail");
              $fdFullName = formRequest("fdFullName");
              $fdNickName = formRequest("fdNickName");
              $fdArchive = formRequest("fdArchive");
              $fdPassword = password_hash(formRequest("fdPassword"),null);

              if($activity=="USER-CREATE-PROCESS") {
                $sql = "INSERT INTO `tbUsers` (     `fdEmail`,         `fdFullName`,         `fdNickName`,         `fdArchive`,         `fdPassword`,     `fdCreated`,     `fdUpdated`,   `fdLastPassChanged`) 
                                       VALUES ('" . $fdEmail . "','" . $fdFullName . "','" . $fdNickName . "','" . $fdArchive . "','" . $fdPassword . "',  now(),          now(),          now())";

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
echo "<td><a href='index.php?activity=USER-DELETE-PROCESS&id=".$row["id"]."&order=$order'>DELETE</a></td>";

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