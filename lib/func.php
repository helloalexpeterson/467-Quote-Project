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

function getUsers($pdo){};

?>