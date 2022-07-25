<?php
    include '../lib/db.php';
    include '../lib/func.php';

    $pdo = connectdb();


    //collect form data
    if(isset($_POST['submit'])){
        $email = $_POST['email'];
        $empName = $_POST['empName'];
        $title = $_POST['title'];
        $pwd = $_POST['pwd'];
        $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);
    

        //insert data into db 
        $sql = "INSERT INTO Employees (Email, EmpName, Title, PwHash) 
                    VALUES ('$email', '$empName', '$title', '$hashed_pwd')";

        $pdo->exec($sql);

        //reload admin.php
        header("Location: admin.php");
    }
    if(isset($_POST['editAssociate'])){
        echo "edit button pushed! <br>"; 
        echo "emp ID is {$_POST['empID']}";

    }
    
    if(isset($_POST['deleteAssociate'])){
        echo "delete button pushed! <br>";
        echo "emp ID is {$_POST['empID']}";
        $empID = $_POST['empID'];
        $prepared = $pdo->exec("DELETE FROM Employees WHERE EmployeeID = $empID ");
        //$prepared->execute();

    }

?>
