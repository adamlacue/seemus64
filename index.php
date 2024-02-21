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
        <a href="index.php?activity=USER">LOGON</a> | <a href="index.php?activity=LOGOUT">LOGOUT</a> | <a href="index.php?activity=">LOGOUT</a> |

        <?php
        switch($activity) {
            case "USER":
                // User Login
                if(!isset($_REQUEST["username"])) {
                    ?>
                    <form action="index.php" method="post">
                        <input type="hidden" name="activity" value="USER" />
                        <input type="text" name="username" placeholder="Username / Email" />
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
            

            
            case "UPDATE-FORM":
                    // U of crUd
                    //Show forms for update!
                    //echo $activity . " in UPDATE-FORM section";
              
                    $stmt = $conn->prepare("SELECT * FROM `tbPeople_Bob` WHERE id = ". formRequest("id"));
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
                      <input type="text" name="FirstName" placeholder="FirstName" value="<?php echo $row["First Name"]; ?>"><br>
                      <input type="text" name="LastName" placeholder="LastName" value="<?php echo $row["Last Name"]; ?>"><br>
                      <input type="text" name="Email" placeholder="Email" value="<?php echo $row["Email"]; ?>"><br>
                      <input type="text" name="Phone" placeholder="Phone" value="<?php echo $row["Phone"]; ?>"><br>
                      <input type="submit" value="UPDATE!"><br>
                      </form>
                      <?php
                      }
                    }
                
            break;

            case "CREATE-PROCESS":
                //echo $activity . " in INSERT Processing section";
          
                $FirstName = formRequest("FirstName");
                $LastName = formRequest("LastName");
                $Email = formRequest("Email");
                $Phone = formRequest("Phone");
          
                if($activity=="CREATE-PROCESS") {
                  $sql = "INSERT INTO `tbPeople_Bob` (`First Name`, `Last Name`, `Email`, `Phone`) VALUES ('" . $FirstName . "','" . $LastName . "','" . $Email . "','" . $Phone . "')";
                  $conn->exec($sql);
                  echo "INSERTED: " . $conn->lastInsertId() . "<BR><BR>";
                }
                //echo "<BR>".$sql."<BR>";
                
            break;
            
            case "CREATE":
                    // C of Crud
                    //Insert (SQL Language) Data!
                    //echo $activity . " in INSERT section";
                    ?>
                    <form action="index.php">
                      <input type="hidden" name="activity" value="CREATE-PROCESS">
                      <input type="text" name="FirstName" placeholder="FirstName"><br>
                      <input type="text" name="LastName" placeholder="LastName"><br>
                      <input type="text" name="Email" placeholder="Email"><br>
                      <input type="text" name="Phone" placeholder="Phone"><br>
                      <input type="submit" value="GO!"><br>
                    </form>
                    <?php
                
                
            break;
            

        case "DELETE-PROCESS":
            //D of cruD
            //PROCESS for the Deleting of Data!
            //echo $activity . " in DELETE PROCESS section";
      
          if($activity=="DELETE-PROCESS") {
            $sql = "DELETE FROM `tbPeople_Bob` WHERE `id` = " .formRequest("id");
            $conn->exec($sql);
            echo "DELETED: " . formRequest("id") . "<BR><BR>";
          }


     default:
                //RUN DEFAULT or READ always if blank or READ
      $sql = "SELECT * FROM `tbPeople_Bob`";
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

echo "<BR><BR>[". $_SESSION["username"] . "] is current user<br>";

?>