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
    $sql ='UPDATE Employees SET PwHash = :hash WHERE EmployeeID = :empID ;';   
    try {
            $statement = $pdo->prepare($sql);
            if($statement) {
                    $result = $statement->execute([
                        ':hash' => $hash = password_hash($pass, PASSWORD_DEFAULT),
                        ':empID' => $empID
                    ]);
                }
            }
     
     catch (PDOException $e){
        echo "    <p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";
    
    }
}


?>