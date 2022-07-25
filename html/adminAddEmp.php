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

    //This menu appears if you click edit on admin.php as opposed to creating a new user
    if(isset($_POST['editAssociate'])){
        $editAssociate = $_POST['editAssociate']; 
        $empID = $_POST['empID'];
        $val='';
        echo "<pre>"; print_r($_POST); echo "</pre>";
        echo "edit button pushed! <br>"; 
        echo "emp ID is {$_POST['empID']} <br>";       

        if(isset($_POST['action'])){
            //echo "an update button pushed, it was: ";
            //echo $_POST['action']; "<br>";     
            switch($_POST['action']){
                case 'Update name':
                    if(!$_POST['empName']){
                        echo  "<br> Error: Name field must be valid. <br>";
                        break;
                    }
                    $val=$_POST['empName'];
                    updateName($pdo, $empID, $val);
                    break;
                case 'Update email':
                    if(!$_POST['email']){
                        echo  "<br> Error: Email field must be valid. <br>";
                        break;
                    }
                    $val=$_POST['email'];
                    updateEmail($pdo, $empID, $val);
                    break;
                case 'Update title':
                    $val=$_POST['title'];
                    updateTitle($pdo, $empID, $val);
                    break;
                case 'Update password':
                    if(!$_POST['pwd']){
                        echo  "<br> Error: password field must be valid. <br>";
                        break;
                    }
                    updatePassword($pdo, $empID, $_POST['pwd']);
                    break;


            }
        }
        
        echo <<<HTML
        <form method="POST" action="adminAddEmp.php"> 
                    <br>
                    <label for="empName">Name:</label>
                    <input type="text" id="empName" name="empName" placeholder="Associate name"><br>
                    <input type="submit" name="action" value="Update name"><br>
                    <label for="pwd">Password:</label>
                    <input type="password" id="pwd" name="pwd" placeholder="Associate password"><br>
                    <input type="submit" name="action" value="Update password"><br>
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="Associate E-mail"><br><br>
                    <input type="submit" name="action" value="Update email"><br>
                    <label for="title">Title:</label>
                    <select id="title" name="title">
                        <option value="Sales Associate">Sales Associate</option>
                        <option value="Headquarters">Headquarters</option>
                        <option value="Administrator">Administrator</option>
                    </select><br><br>
                    <input type="submit" name="action" value="Update title"><br>
        HTML;
                    echo "<input type='hidden' name='editAssociate' value='{$editAssociate}'><br>";
                    echo "<input type='hidden' name='empID' value='{$empID}'><br>";
        echo "</form>";
        
    }
    
    if(isset($_POST['deleteAssociate'])){
        echo "delete button pushed! <br>";
        echo "emp ID is {$_POST['empID']}";
        $empID = $_POST['empID'];
        $prepared = $pdo->exec("DELETE FROM Employees WHERE EmployeeID = $empID ");
        //$prepared->execute();

    }

?>
