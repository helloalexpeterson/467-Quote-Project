<!--
THIS IS SO YOU DON'T HAVE GO ONTO MARIA DB TO RESET THE DATABASE
-->
<!DOCTYPE html>
<html lang="en">
<head>
<title>Reset Database</title>
<meta charset="utf-8">
<link rel="stylesheet" href="css/color.css">
</head>
<body>
THIS IS SO YOU DON'T HAVE GO ONTO MARIA DB TO RESET THE DATABASE</br>
<div id="Reset">
    <b>Reset Database</b>
    <form action="" method="POST">
        <input type="hidden" name="reset" value="1"/>
        <input type="submit" value="reset"/>
    </form>
</div>
<div id="Reset Data">
    <b>Reset Database and Load Default Data</b>
    <form action="" method="POST">
        <input type="hidden" name="reset" value="2"/>
        <input type="submit" value="reset"/>
    </form>
</div>
<div id="Reset Passwords">
    <b>reset all passwords to 123</b>
    <form action="" method="POST">
        <input type="hidden" name="reset" value="3"/>
        <input type="submit" value="reset"/>
    </form>
</div>
<?php
error_reporting(E_ALL);
include('../lib/db.php');
try {
    $pdo = connectdb();
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $reset = isset($_POST['reset']) ? $_POST['reset'] : '';
    if($reset == 1 || $reset == 2) {
        $sql = file_get_contents("../sql/createtables.sql");
        $prepared = $pdo->prepare($sql);
        $prepared->execute();
        echo "Successfully reset the database.";
        if ($reset == 2) {
            $sql = file_get_contents("../sql/loadtables.sql");
            $prepared = $pdo->prepare($sql);
            $prepared->execute();
            echo "Successfully loaded data.";  
        }
    }
    else if ($reset == 3) {
        include '../lib/func.php';
         $sql = "SELECT * FROM Employees";
                $result = $pdo->query($sql);
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows as $employee => $val){ 
                updatePassword($pdo, $val['EmployeeID'], '123');
                }
        echo "Successfully reset all passwords to 123";
    }
}
catch(PDOexception $e) {
    echo "Connection to database failed: " . $e->getMessage();
}
?>
</body>
</html>