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
        <a href="index.php?activity=USER">LOGON</a> | <a href="index.php?activity=LOGOUT">LOGOUT</a> | 

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
            

            case "EDIT":
                // User Edit
                
            break;

            case "DELETE":
                // User Delete
                
            break;
            
            case "CREATE":
                
                
            break;
            


            default:
                //default viewing of content
                ?>
                <BR>
                I'm in default
 
                <?php
            break;
        }

        ?>
    </body>
</html>

<?php
include "include/bottom.inc.php";   

echo "<BR><BR>[". $_SESSION["username"] . "] is current user<br>";

?>