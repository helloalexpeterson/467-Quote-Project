<?php  
session_start(['name' => 'quotes']); 

    include '../lib/db.php';
    include '../lib/func.php';

    $pdo = connectdb();

    //create new user
    if(isset($_POST['submit'])){
        $email = $_POST['email'];
        $empName = $_POST['empName'];
        $title = $_POST['title'];
        $pwd = $_POST['pwd'];
        $city = $_POST['city'];
        $street = $_POST['street'];
        $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);
    

        //insert data into db 
        $sql = "INSERT INTO Employees (Email, EmpName, City, Street, Title, PwHash) 
                    VALUES ('$email', '$empName', '$city', '$steet', '$title', '$hashed_pwd')";

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
                case 'Update city':
                    $val=$_POST['empCity'];
                    updateGiven($pdo, $empID, 'City', $val);
                    break;
                case 'Update street':
                    $val=$_POST['empStreet'];
                    updateGiven($pdo, $empID, 'Street', $val);
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
                    <h3>Update User:</h3>
                    <label for="empName">Name:</label>
                    <input type="text" id="empName" name="empName" placeholder="Associate name">
                    <input type="submit" name="action" value="Update name"><br>
                    <label for="pwd">Password:</label>
                    <input type="password" id="pwd" name="pwd" placeholder="Associate password">
                    <input type="submit" name="action" value="Update password"><br>
                    <label for="empName">City:</label>
                    <input type="text" name="empCity" placeholder="Associate city">
                    <input type="submit" name="action" value="Update city"><br>
                    <label for="empName">Street:</label>
                    <input type="text" name="empStreet" placeholder="Associate street">
                    <input type="submit" name="action" value="Update street"><br>
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="Associate E-mail">
                    <input type="submit" name="action" value="Update email"><br><br>
                    <label for="title">Title:</label>
                    <select id="title" name="title">
                        <option value="Sales Associate">Sales Associate</option>
                        <option value="Headquarters">Headquarters</option>
                        <option value="Administrator">Administrator</option>
                    </select>
                    <input type="submit" name="action" value="Update title"><br>
        HTML;
                    echo "<input type='hidden' name='editAssociate' value='{$editAssociate}'><br>";
                    echo "<input type='hidden' name='empID' value='{$empID}'><br>";
        echo "</form>";

        echo <<<HTML
        <form action='admin.php' method='POST'> <input type='submit' name='back' value='Return to admin'></form>
        HTML;
        
    }
    
    if(isset($_POST['deleteAssociate'])){
        echo "delete button pushed! <br>";
        echo "emp ID is {$_POST['empID']}";
        $empID = $_POST['empID'];
        $prepared = $pdo->exec("DELETE FROM Employees WHERE EmployeeID = $empID ");
        //$prepared->execute();
        header("Location: admin.php");

    }

function updateGiven($pdo, $empID, $columnName, $val) {
    try {
        $statement = $pdo->prepare("UPDATE Employees SET $columnName = :val WHERE EmployeeID = :empID ");
        if($statement) {
                $result = $statement->execute([
                    ':val' => $val,
                    ':empID' => $empID
                ]);
            }
        }

    catch (PDOException $e){
    echo "    <p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";

    }
}
?>
