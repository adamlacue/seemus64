<?php

class Utils {
    
    public static function prettyPrint($sqlStmt) { 
        global $conn;
        $stmt = $conn->prepare($sqlStmt);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($result) {
        $printedHeader=false;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            if(!$printedHeader) {
            echo "<table border=1>";   // Start Table
            echo "<tr>";      // Start Header Row
            foreach($row as $col_name => $val)
                {
                echo "<th>$col_name</th>";    // Print Each Field Name
                }
            echo "</tr>";
            $printedHeader=true;
            }
            echo "<tr>";      // Start Header Row
            foreach($row as $col_name => $val)
            {
            echo "<td>$val</td>";    // Print Each Field Name
            }
            echo "</tr>";      // Start Header Row
        }
        echo "</table>";
        } else {
        echo "0 results";
        }
    }
}

?>