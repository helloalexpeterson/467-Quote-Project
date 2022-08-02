<?php
include '../config/secrets.php';

// createQuote
// args: 
// $pdo - pdo object, connection to our db
// $id, $city, $street, $name, $contact, $email - variables from submitted from POST in open.php 
//
// inserts a new quote into Quotes table
// returns the quote id of the new quote
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

// updateName
// args: $pdo - db connection object
//       $empID - id of user from Employee table
//       $val - text of name to update in Employee table 
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

// updateEmail
// args: $pdo - db connection object
//       $empID - id of user from Employee table
//       $val - text of email to update in Employee table 
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

// updateTitle
// args: $pdo - db connection object
//       $empID - id of user from Employee table
//       $val - text of title to update in Employee table 
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

// updatePassword
// args: $pdo - db connection object
//       $empID - id of user from Employee table
//       $pass - plaintext of password to hash and update in Employee table 
function updatePassword($pdo, $empID, $pass){
    $sql ='UPDATE Employees SET PwHash = :hash WHERE EmployeeID = :empID ;';   
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    try {
            $statement = $pdo->prepare($sql);
            if($statement) {
                    $result = $statement->execute([
                        ':hash' => $hash,
                        ':empID' => $empID
                    ]);
                }
            }
     
     catch (PDOException $e){
        echo "    <p>Could not query from database. PDO Exception: {$e->getMessage()}</p>\n";
    
    }
}

// updateCommission
// args: $pdo - db connection object
//       $empID - id of user from Employee table
//       $commission - value of commission $ to override exisiting value in Employee table
//       The commission on myCommissions.php will show the sum total of purchase orders,
//       as well as show the difference between the sum and the admin adjustment value  
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

// updateAddress
// args: $pdo - db connection object
//       $empID - id of user from Employee table
//       $street - text of address to update in Employee table 
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

// login
// args: $user - a userID from Employees table, $pass - plaintext password
// if the username and password verify true - set the SESSION to the user's info
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
    
    if($row && password_verify($pass, $row['PwHash'])){
        echo "<br> Password verified! <br>";

        $_SESSION["userID"] = $row['EmployeeID'];
        $_SESSION["userType"] = $row['Title'];
        $_SESSION["username"] = $row['EmpName'];
        switch( $row['Title'] ){
            case 'Superuser':
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
    } else { 
        echo " <div class='alert alert-danger' role='alert'> Username or Password invalid!</div>";
    }

}

// advanceQuoteStatus
// input: quoteID, submit button value from quoteTemplate
// returns success or error message as string
//
// advance the state of a quote [open -> finalized -> sanctioned -> ordered]
// emails to customer when sanctioning or ordering
// ordering calls submitPO()
function advanceQuoteStatus($quoteID, $buttonText){
    // get quote info
    $pdo = connectdb();
    $sql = "SELECT * FROM Quotes WHERE QuoteID = ?";
    $prepared = $pdo->prepare($sql);
    if($prepared){ 
        $prepared->execute([$quoteID]);
        $quote = $prepared->fetch(PDO::FETCH_ASSOC);
    } 
    // set status based on button value, which was itself based on quote status
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
            //try sending email

            /*if(!sendCustomerEmail($pdo, $quoteID, $quote['Email'], 'quote')) {
                $msg += "Error: Email failed to send during sanctioning\n";
            } */
            return $msg ="Quote $quoteID Sanctioned and ready for purchase order. A draft of this quote has been sent to {$quote['Email']}";
            break;

        case 'Order Quote':     
            $result = '';   
            // submit purchase order to external processing system  
            if(submitPO($quoteID, $quote['EmployeeID'], $result)){
                $sql = "UPDATE Quotes SET OrderStatus = 'ordered' WHERE QuoteID = $quoteID";
                $prepared = $pdo->prepare($sql);
                    if($prepared){ 
                        $prepared->execute();
                    } 
                // calc commission and pay to show
                // show order total
                    $percent = str_replace('%', '', $result['commission']);
                    $pay = $result['amount'] * ($percent/100.00);    
                $msg = "Quote $quoteID submitted for purchasing.<br> 
                A copy of this order has been sent to {$quote['Email']} <br> 
                The total for this order is &#36;{$result['amount']} <br>
                The commission rate for this order is {$result['commission']} <br> 
                The total commission paid to the associate is &#36;{$pay}";

                $sql = "UPDATE Quotes SET CommissionRate = ? WHERE QuoteID = $quoteID";
                $prepared = $pdo->prepare($sql);
                    if($prepared){ 
                        $prepared->execute([$percent]);
                    }     

            } else {   
                $msg = "Error with submitting purchase order to processor: $result";
            }
            // try sending email
            /*if(!sendCustomerEmail($pdo, $quoteID, $quote['Email'], 'order')) {
                $msg += "\nError: Email failed to send during order creation";
            }*/

            return $msg; 
            break;
            
          default:
          echo "advance quote status error";
        }
}

// submitPO
// input: quoteID, employeeID, result that will be modified to be used as an array
// returns boolean success or failure
//
// submit purchase order to external processing system
// based on example given by Raimund Ege  
// calls createPO() to create purchase order in internal system
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
    // create PO in internal system
    createPO($pdo, $result);
    return true;
}

// createPO
// input: internal database pdo, result info array
//
// create purchase order in internal system
// calls payCommission() to pay sale associate
function createPO($pdo, $result){
    $sql = 'INSERT INTO PurchaseOrders(QuoteID , EmployeeID , CustomerID , OrderTotal , CustomerName , CommissionRate, OrderTime)
    VALUES (:order, :associate, :custid, :amount, :name, :commission, :timeStamp)';
    $percent = str_replace('%', '', $result['commission']);
    $statement = $pdo->prepare($sql);
    if($statement){
    $timeStamp = date('Y-m-d H:i', $result['timeStamp']/1000);
        $statement->execute([
        ':order' => $result['order'],    
        ':associate' => $result['associate'],
        ':custid' => $result['custid'],
        ':amount'  => $result['amount'],
        ':name' => $result['name'],
        ':commission'  => $percent,
        ':timeStamp' => $timeStamp]);
    }
    
    payCommission($pdo,$result['associate'], $result['amount'], $result['commission']);
}

// payCommission
// input: internal database pdo, employeeID, order total, commission percent
//
// pay sale associate
function payCommission($pdo, $emp, $total, $commission){
    $commission = str_replace('%', '', $commission);
    $total = $total * ($commission/100.00);

    $sql = 'UPDATE Employees SET CommissionTotal = CommissionTotal+? WHERE EmployeeID = ?';
    $statement = $pdo->prepare($sql);
    if($statement){
        $statement->bindValue(1, $total);
        $statement->bindValue(2, $emp);
        $statement->execute();
    }
}

// sendCustomerEmail
// input: internal database pdo, quoteID, receiving email, email type
//
// send email with php's mail()
//      send quote info when sanctioning
//      send purchase order info when ordering
// niu's turing and hopper does not seem to be configured to actually send emails
//      despite mail() not returning false
function sendCustomerEmail($pdo, $quoteID, $email, $type) {
    $msg = "First line of text\nSecond line of text";
    $pdo = connectdb();
    switch($type) {
        case 'quote':
            $prepared = $pdo->prepare("SELECT * FROM Quotes WHERE QuoteID = ?");
            $prepared->execute([$quoteID]);
            $quote = $prepared->fetch(PDO::FETCH_ASSOC);

            $msg = implode(" ", $quote);
            break;
        case 'order':
            $prepared = $pdo->prepare("SELECT * FROM PurchaseOrders WHERE QuoteID = ?");
            $prepared->execute([$quoteID]);
            $order = $prepared->fetch(PDO::FETCH_ASSOC);

            $msg = implode(" ", $order);
            break;
        default:
            echo "No type given, no email sent.";
    }

    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg,70);
    // send email
    $subject = 'the subject';
    $headers = array(
        'From' => 'webmaster@example.com',
        'Reply-To' => 'webmaster@example.com',
        'X-Mailer' => 'PHP/' . phpversion()
    );

    return mail($email, $subject, $msg, $headers);
}
?>