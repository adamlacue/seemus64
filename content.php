<?php
include "include/top.inc.php";
include "class/Utils.class.php";

$activity = formRequest("activity");

$sql = "SELECT id,fdTitle, fdHTML FROM `tbContent` WHERE id = " . formRequest("id");

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

// Check if $result has anything in it or not (Returns a FALSE if no data in there).
if($result) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ob_clean();
        ?>
<html>
    <head>
        <title>
            <?= $row["fdTitle"];?>
        </title>
    </head>
    <body>
        <?= $row["fdHTML"];?>
    </body>
</html>
<?php
    }
}

?>