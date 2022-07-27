
<!DOCTYPE html>
<html lang="en">
<head>
<title>Reset Database</title>
<meta charset="utf-8">
<link rel="stylesheet" href="css/color.css">
</head>
<body>
<?php
include '../lib/db.php';
include '../lib/func.php';
$pdo = connectdb();
 $sql = "SELECT * FROM Employees";
        $result = $pdo->query($sql);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as $employee => $val){ 
        updatePassword($pdo, $val['EmployeeID'], '123');
        }
echo "reset all passwords to 123";

?>
    </body>
</html>