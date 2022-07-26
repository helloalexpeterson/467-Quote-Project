<?php
function createQuote($pdo, $id, $name, $city, $street, $contact, $email)
{   
    //setting empid and status to be default while I figure out how to use employeeID with session variables
    $status = "open";
    $empid = "1";
    $sql ='INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus) 
    VALUES (:CustomerID,:CustomerName,:City,:Street, :Contact,:Email,:EmployeeID,:OrderStatus )';
    $result = false;    
        try {
            $statement = $pdo->prepare($sql);
            if($statement) {
                    $result = $statement->execute([
                        ':CustomerID' => $id,
                        ':CustomerName' => $name,
                        ':City' => $city,
                        ':Street' => $street,
                        ':Contact' => $contact,
                        ':Email' => $email,
                        ':EmployeeID' => $empid,
                        ':OrderStatus' => $status, 
                    ]);
            } else {
                echo "    <p>Could not query  database for unknown reason.</p>\n";
            }
        } catch (PDOException $e){
            echo "    <p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";
        }       

        return $quoteID = $pdo->lastInsertId();

}

function updateName($pdo, $empID, $val){
    $sql ='UPDATE Employees SET EmpName = :val WHERE EmployeeID = :empID ;';   
    try {
            $statement = $pdo->prepare($sql);
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

function updateEmail($pdo, $empID, $val){
    $sql ='UPDATE Employees SET Email = :val WHERE EmployeeID = :empID ;';   
    try {
            $statement = $pdo->prepare($sql);
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

function updateTitle($pdo, $empID, $val){
    $sql ='UPDATE Employees SET Title = :val WHERE EmployeeID = :empID ;';   
    try {
            $statement = $pdo->prepare($sql);
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

function updatePassword($pdo, $empID, $pass){
    $sql ='UPDATE Employees SET PwHash = :hash, PwText = :pass WHERE EmployeeID = :empID ;';   
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    try {
            $statement = $pdo->prepare($sql);
            if($statement) {
                    $result = $statement->execute([
                        ':hash' => $hash,
                        ':pass' => $pass,
                        ':empID' => $empID
                    ]);
                }
            }
     
     catch (PDOException $e){
        echo "    <p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";
    
    }
}

function login($user, $pass){
    $pdo = connectdb();
    $sql ='SELECT * FROM Employees WHERE :user = EmpName';  
    try {
        $statement = $pdo->prepare($sql);
        if(!$statement){ 
            echo "<br>pdo prepare failed";  echo "<br>";
        } else {
            $result = $statement->execute([':user' => $user]);
        } if(!$result){ 
            echo "<br>pdo execute failed";  echo "<br>";
        }

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        //echo "<br>statement is: "; print_r($statement);     echo "<br>";
        echo "<br>result is: "; print_r($row);     echo "<br>";
        echo "<br>pw hash is: "; print_r($row['PwHash']);     echo "<br>";
        echo "<br>pw text is: "; print_r($row['PwText']);     echo "<br>";

    }
 
    catch (PDOException $e){
    echo "<p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";
    }
    
    if(password_verify($pass, $row['PwHash'])){
        echo "<br> Password verified! <br>";

        $_SESSION["userID"] = $row['EmployeeID'];
        $_SESSION["userType"] = $row['Title'];
        switch( $row['Title'] ){
            case 'Sales Associate':
            header("Location: open.php");
            break;

            case 'Headquarters':
            header("Location: open.php");
            break;

            case 'Administrator':
            header("Location: admin.php");
            break;

            case 'Superuser':
            header("Location: admin.php");
            break;

        } 
    } else {echo "<br> Password not verified! <br>";}


}


?>