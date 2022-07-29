<?php
include '../config/secrets.php';
function createQuote($pdo, $id, $name, $city, $street, $contact, $email)
{   
    //setting empid and status to be default while I figure out how to use employeeID with session variables
    $status = "open";
    $empid = $_SESSION['userID'];
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

function updateCommission($pdo, $empID, $commission){
    $sql ='UPDATE Employees SET CommissionTotal = :comm WHERE EmployeeID = :empID ;';   
    try {
            $statement = $pdo->prepare($sql);
            if($statement) {
                    $result = $statement->execute([
                        ':comm' => $commission,
                        ':empID' => $empID
                    ]);
                }
            }
     
     catch (PDOException $e){
        echo "    <p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";
    }
}


function updateAddress($pdo, $empID, $street){
    $sql ='UPDATE Employees SET Street = :street WHERE EmployeeID = :empID ;';   
    try {
            $statement = $pdo->prepare($sql);
            if($statement) {
                    $result = $statement->execute([
                        ':street' => $street,
                        ':empID' => $empID
                    ]);
                }
            }
     
     catch (PDOException $e){
        echo "    <p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";
    }

}

function login($user, $pass){
    global $debug;
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
        if($debug){
            echo "<br>result is: "; print_r($row);     echo "<br>";
            echo "<br>pw hash is: "; print_r($row['PwHash']);     echo "<br>";
            echo "<br>pw text is: "; print_r($row['PwText']);     echo "<br>";
        }

    }
 
    catch (PDOException $e){
    echo "<p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";
    }
    
    if(password_verify($pass, $row['PwHash'])){
        echo "<br> Password verified! <br>";

        $_SESSION["userID"] = $row['EmployeeID'];
        $_SESSION["userType"] = $row['Title'];
        $_SESSION["username"] = $row['EmpName'];
        switch( $row['Title'] ){
            case 'Sales Associate':    
            header("Location: open.php?type=open", 303);
            break;

            case 'Headquarters':
            header("Location: quotes.php?type=finalized", 303);
            break;

            case 'Administrator':
            header("Location: admin.php", 303);
            break;

        } 
    } else {echo "<br> Password not verified! <br>";}

}

function advanceQuoteStatus($quoteID, $buttonText){
    
    $pdo = connectdb();
    $sql = "SELECT * FROM Quotes WHERE QuoteID = ?";
    $prepared = $pdo->prepare($sql);
    if($prepared){ 
        $prepared->execute([$quoteID]);
        $quote = $prepared->fetch(PDO::FETCH_ASSOC);
    } 
    switch($buttonText){

        case 'Finalize Quote':
            $sql = "UPDATE Quotes SET OrderStatus = 'finalized' WHERE QuoteID = $quoteID";
            $prepared = $pdo->prepare($sql);
            if($prepared){ 
                $prepared->execute();
            } 
            return $msg = "Quote $quoteID finalized and sent to Headquarters.";
            break;

        case 'Sanction Quote':
            $sql = "UPDATE Quotes SET OrderStatus = 'sanctioned' WHERE QuoteID = $quoteID";
            $prepared = $pdo->prepare($sql);
            if($prepared){ 
                $prepared->execute();
            } 
            return $msg ="Quote $quoteID Sanctioned and ready for purchase order. A draft of this quote has been sent to {$quote['Email']}";
            break;

        case 'Order Quote':     
            $result = '';     
            if(submitPO($quoteID, $quote['EmployeeID'], $result)){
                $sql = "UPDATE Quotes SET OrderStatus = 'ordered' WHERE QuoteID = $quoteID";
                $prepared = $pdo->prepare($sql);
                    if($prepared){ 
                        $prepared->execute();
                    } 
                $msg = "Quote $quoteID submitted for purchasing. A copy of this order has been sent to {$quote['Email']}";
            } else {   
                $msg = "Error with submitting purchase order to processor: $result";
            }
            return $msg; 
            break;
            
          default:
          echo "advance quote status error";
        }
}

function submitPO($quoteID, $EmployeeID, &$result){
    $pdo = connectdb();
    $sql = "SELECT Quotes.OrderTotal, Quotes.CustomerName, Quotes.CustomerID, Quotes.QuoteID, Quotes.EmployeeID, Employees.EmpName FROM Quotes  JOIN Employees ON Quotes.EmployeeID = Employees.EmployeeID AND QuoteID = :quoteID";
            $prepared = $pdo->prepare($sql);
            if($prepared){ 
                $prepared->execute([':quoteID' => $quoteID]);
                $rows = $prepared->fetch(PDO::FETCH_ASSOC);
            } 

    $url = 'http://blitz.cs.niu.edu/PurchaseOrder/';
    $data = array(
        'order' => $rows['QuoteID'], 
        'associate' => $rows['EmployeeID'],
        'custid' => $rows['CustomerID'], 
        'amount' => $rows['OrderTotal']);
            
    $options = array(
        'http' => array(
            'header' => array('Content-type: application/json', 'Accept: application/json'),
            'method'  => 'POST',
            'content' => json_encode($data)
        )
    );
    
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result, true);
    if(isset($result['errors'])){ 
        $result = implode(',', $result['errors']);
        return false;
    }
    createPO($pdo, $result);
    return true;
}

function createPO($pdo, $result){
    global $debug;
    $sql = 'INSERT INTO PurchaseOrders(QuoteID , EmployeeID , CustomerID , OrderTotal , CustomerName , CommissionTotal, OrderTime)
    VALUES (:order, :associate, :custid, :amount, :name, :commission, :timeStamp)';
    $statement = $pdo->prepare($sql);
    if($statement){
    $timeStamp = date('Y-m-d H:i', $result['timeStamp']/1000);
        $statement->execute([
        ':order' => $result['order'],    
        ':associate' => $result['associate'],
        ':custid' => $result['custid'],
        ':amount'  => $result['amount'],
        ':name' => $result['name'],
        ':commission'  => $result['commission'],
        ':timeStamp' => $timeStamp]);
    }
    if($debug){echo "<br>****PO inserted into table!!****<br>";}
    
    payCommission($pdo,$result['associate'], $result['amount'], $result['commission']);
}

function payCommission($pdo, $emp, $total, $commission){
    global $debug;

    if($debug){echo "<br>****Begin pay commission****<br>";}
    //$sql = 'SELECT CommissionTotal FROM Employees WHERE EmployeeID = :empID';
    //$statement = $pdo->prepare($sql);
    //if($statement){
        //$statement->execute([':empID' => $emp]);
        //$row = $statement->fetch(PDO::FETCH_ASSOC);

        //if($row){

            $commission = str_replace('%', '', $commission);
            $total = $total * ($commission/100.00);
            if($debug){echo "<br>the total commish to pay employee number {$emp} is: {$total}<br>";}

            //$total =  $row['CommissionTotal'] + $total;

            if($debug){echo "<br>the employee number {$emp} has: {$total} commission<br> ";}

       // }

        $sql = 'UPDATE Employees SET CommissionTotal = CommissionTotal+? WHERE EmployeeID = ?';
        $statement = $pdo->prepare($sql);
        if($statement){
            $statement->bindValue(1, $total);
            $statement->bindValue(2, $emp);
            $statement->execute();
        }
    //}
}
?>