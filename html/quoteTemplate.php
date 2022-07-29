<?php 
session_start(['name' => 'quotes']); 

include '../lib/db.php';
include '../lib/func.php';

$msg = ''; 
if(isset($_POST['submitBtn']) && isset($_POST['submitBtn'])){

    $msg = advanceQuoteStatus($_GET['quoteID'], $_POST['submitBtn'] ); 
    unset($_POST['submitBtn']);
}    
$pagetitle="View Quote";
include 'header.php';

//debug print
if($debug){
    echo "ignore this - debug info"; 
    echo "<br>";
    echo "<pre>  'SESSION'";  
    print_r($_SESSION);   
    echo "</pre>" ;

    echo "<br>";
    echo "<pre>  'POST'";  
    print_r($_POST);   
    echo "</pre>" ;
}

error_reporting(E_ALL);
try {

    $pdo = connectdb();
    $legacy = connectlegacy();
   
    //if "Create New Quote is pushed"
    if(!isset($_GET["quoteID"])){
    
        $id = $_POST['id']; 
        $sql = "SELECT * FROM customers WHERE id = $id";
        $result = $legacy->query($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);   
        $quoteID = createQuote($pdo,  $_POST['id'], $row['name'], $row['city'], $row['street'], $row['contact'], $_POST['email']);

        header("Location: quoteTemplate.php?quoteID=$quoteID", 303);
        die();
    }

    // quoteID from form, else use newly created one
    $quoteID = isset($_GET['quoteID']) ? $_GET['quoteID']: $quoteID;
    $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
    $quote = $result->fetch(PDO::FETCH_ASSOC);
    $orderTotal = $quote['OrderTotal'];

    // custID from quote
    $customerID = $quote["CustomerID"];
    $result = $legacy->query("SELECT * FROM customers where id = $customerID");
    $cust = $result->fetch(PDO::FETCH_ASSOC);

    // POST values
    $formAction = isset($_POST['formAction']) ? $_POST['formAction'] : '';

    // Legacy DB values
    $email = isset($_POST['Email']) ? $_POST['Email'] : $quote['Email'];
    $CustomerName = isset($cust['name']) ? $cust['name'] : "Invalid customer selected";
    $city = isset($cust['city']) ? $cust['city'] : "No city found";
    $street = isset($cust['street']) ? $cust['street'] : "No street found";
    $contact = isset($cust['contact']) ? $cust['contact'] : "No contact found";

    //FORM FUNCTIONS
    $errorMsg = '';

    switch($formAction){
        case 'email':
            $prepared = $pdo->prepare("UPDATE Quotes SET Email=? WHERE QuoteID = $quoteID");
            $prepared->execute([$email]);
            break;
        case 'editLine':
            // update total and requery quote
            if (isset($_POST['lineID']) && isset($_POST['editDesc']) && is_numeric($_POST['editCost'])) {
                $quote = editTotal($pdo, $quote, $quoteID, $_POST['lineID'], $_POST['editCost']);
                $orderTotal = $quote['OrderTotal'];
                $prepared = $pdo->prepare("UPDATE LineItems SET ServiceDesc=?, Cost=? WHERE LineID=? AND quoteID = $quoteID");
                $prepared->execute([$_POST['editDesc'], $_POST['editCost'], $_POST['lineID']]);
            }
            else { $errorMsg = "Error editing line item"; }
            break;
        case 'deleteLine':
            // update total and requery quote
            if (isset($_POST['lineID'])) {
                $prepared = $pdo->prepare("DELETE FROM LineItems WHERE LineID=? AND quoteID=$quoteID");
                $prepared->execute([$_POST['lineID']]);
                $quote = addToTotal($pdo, $quote, $quoteID, (-1 * $_POST['editCost']));
                $orderTotal = $quote['OrderTotal'];
            }
            else { $errorMsg = "Error deleting line"; }
            break;
        case 'addLine':
            // update total and requery quote
            if (isset($_POST['addDesc']) && is_numeric($_POST['addCost'])) {
                $prepared = $pdo->prepare("INSERT INTO LineItems SET ServiceDesc=?, Cost=?, quoteID=$quoteID");
                $prepared->execute([$_POST['addDesc'], $_POST['addCost']]);

                $quote = addToTotal($pdo, $quote, $quoteID, $_POST['addCost']);
                $orderTotal = $quote['OrderTotal'];
            }
            else { $errorMsg = "Error adding line"; }
            break;
        case 'editNote':
            if (isset($_POST['noteID']) && isset($_POST['editNote'])) {
                $prepared = $pdo->prepare("UPDATE Notes SET Note=? WHERE NoteID=? AND quoteID = $quoteID");
                $prepared->execute([$_POST['editNote'], $_POST['noteID']]);
            }
            else { $errorMsg = "Error editing secret note"; }
            break;
        case 'deleteNote':
            if (isset($_POST['noteID'])) {
                $prepared = $pdo->prepare("DELETE FROM Notes WHERE NoteID=? AND quoteID=$quoteID");
                $prepared->execute([$_POST['noteID']]);
            }
            else { $errorMsg = "Error deleting secret note"; }
            break;
        case 'addNote':
            if (isset($_POST['addNote'])) {
                $prepared = $pdo->prepare("INSERT INTO Notes SET Note=?, quoteID=$quoteID");
                $prepared->execute([$_POST['addNote']]);
            }
            else { $errorMsg = "Error adding secret note"; }
            break;
        case 'discountPercent':
            // query line items
            $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
            $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
            $lineTotal=0;
            // sum line item costs
            foreach($lineItems as $line){
                $lineTotal= $lineTotal + ($line['Cost']); 
            }
            // calc new total
            $orderTotal =   $lineTotal  - (0.01 * (float)$_POST['discount'] * $lineTotal);
            $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
            $prepared->execute([$orderTotal]);
            // requery quote
            $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
            $quote = $result->fetch(PDO::FETCH_ASSOC);
            break;
        case 'discountAmount':
            // query line items
            $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
            $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
            $lineTotal=0;
            // sum line item costs
            foreach($lineItems as $line){
                $lineTotal= $lineTotal + ($line['Cost']); 
            }
            $discount = (float)$_POST['discount'];
            
            if ($discount > $lineTotal) { $errorMsg = "Error: discount too large"; }
            // requery quote
            else {
                $orderTotal = $lineTotal - $discount;
                $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
                $prepared->execute([$orderTotal]);

                $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
                $quote = $result->fetch(PDO::FETCH_ASSOC);
            }
            break;
    }

    //END OF FORM FUNC

    //Switch to disable email, notes, lines, discounts fields based on order status


  
    switch($quote['OrderStatus']){
        case 'open':
            $disableEmail = '';
            $disableLines = '';
            $disableNotes = '';
            $disableDiscount = '';
            $buttonMsg = "To finalize this quote and submit it to processing in headquarters, click here: ";
            break;
        case 'finalized':
            $disableEmail = 'disabled';
            $disableLines = '';
            $disableNotes = '';
            $disableDiscount = '';
            $buttonMsg = "To sanction this quote and email it to the customer, click here: ";
            break;
        case 'sanctioned':
            $disableEmail = 'disabled';
            $disableLines = 'disabled';
            $disableNotes = 'disabled';
            $disableDiscount = '';
            $buttonMsg = "To convert this quote into an order and process it, click here: ";            
            break;
        case 'ordered': 
            $disableEmail = 'disabled';
            $disableLines = 'disabled';
            $disableNotes = 'disabled';
            $disableDiscount = 'disabled';
            $buttonText = '';
            break;
    }

    if($_SESSION['userType'] == "Sales Associate" && $quote['OrderStatus'] !== "open" || $_SESSION['userType'] == "Administrator" ){
        $disableEmail = 'disabled';
        $disableLines = 'disabled';
        $disableNotes = 'disabled';
        $disableDiscount = 'disabled';
        $buttonText = '';
    }   
 
    echo <<<HTML
        <div class='errorMsg'>$errorMsg</div>
        <h2>Quote $quoteID -  status: {$quote['OrderStatus']} </h2>
        <!--- Print message confirming order -->  
        <p>$msg</p>  
        <h2>$CustomerName</h2>
        <div class=\"address\">$city<br>$street<br>$contact<br></div>
    HTML;

    // Email
    echo "<form class=\"email\" action=\"\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
        echo "Email: ";
        echo "<input type=\"email\" name=\"Email\" value=\"$email\" required $disableEmail>";
        echo "<button type=\"submit\" name=\"formAction\" value=\"email\" $disableEmail>Save email</button>";
    echo "</form>";

    // Line Items
    echo "<h1>Line Items:</h1>";
    
    $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
    $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
    $lineCount = 0;
    foreach ($lineItems as $row) {
        $lineCount++;
        // jump to element after action
        if (isset($_POST['lineID']))
            if ($lineCount == $_POST['lineID'])
                echo "<div id='editedLine'>";
        echo "<form action='#editedLine' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type=\"text\" name='editDesc' value=\"{$row["ServiceDesc"]}\" required $disableLines>";
            echo "<input type=\"number\" name='editCost' value=\"{$row["Cost"]}\"] min='0' step='0.01' required $disableLines>";
            echo "<input type=\"hidden\" name=\"lineID\" value=\"{$row['LineID']}\"/>";
            echo "<button type=\"submit\" name='formAction' value='editLine' $disableLines>Edit</button>";
            echo "<button type=\"submit\" name='formAction' value='deleteLine' $disableLines>Delete</button><br>";
        echo "</form>";
    }

    if ($lineCount == 0) { echo "<div class='noItem'>No line items</div>"; }

    if (!$disableLines) {
        echo "<form id='addedLine' action='#addedLine' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type='text' name='addDesc' placeholder='Service Description' required $disableLines>";
            echo "<input type='number' name='addCost' placeholder='Service Cost' min='0' step='0.01' required $disableLines>";
            echo "<button type='submit'name='formAction' value='addLine' $disableLines>Add Line Item</button >";
        echo "</form >";
    }

    // Secret Notes
    echo "<h1>Secret Notes:</h1>";

    $result = $pdo->query("SELECT * FROM Notes where QuoteID = $quoteID");
    $secretNotes = $result->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;
    foreach ($secretNotes as $row) {
        $count++;
        // jump to element after action
        if (isset($_POST['noteID']))
            if ($count == $_POST['noteID'])
                echo "<div id='editedNote'>";
        echo "<form action='#editedNote' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type=\"text\" name='editNote' value=\"{$row['Note']}\" required $disableNotes>";
            echo "<input type=\"hidden\" name=\"noteID\" value=\"{$row['NoteID']}\"/>";
            echo "<button type=\"submit\" name=\"formAction\" value=\"editNote\" $disableNotes>Edit</button>";
            echo "<button type=\"submit\" name=\"formAction\" value=\"deleteNote\"$disableNotes>Delete</button><br>";
         echo "</form>";
    }

    if ($count == 0) { echo "<div class='noItem'>No secret notes</div>"; }

    if (!$disableNotes) {
        echo "<form id='addedNote' action='#addedNote' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type='text' name='addNote' placeholder='Note' required $disableNotes>";
            echo "<button type='submit' name='formAction' value='addNote' $disableNotes>Add Secret Note</button>";
        echo "</form>";
    }

    // Discount
        echo "<form id='discounted' class='discountPercent' action='#discounted' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<label>Discount %: </label>";
            echo "<input type='number' name='discount' placeholder='' min='0' max='100' required $disableDiscount >";
            echo "<button type='submit' name='formAction' value='discountPercent' required $disableDiscount >Apply</button><br>";
        echo "</form>";
        echo "<form class='discountAmount' action='#discounted' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<label>Discount Amount: </label>";
            echo "<input type='number' name='discount' placeholder='' min='0' max='{$quote['OrderTotal']}' step='0.01' required  $disableDiscount >";
            echo "<button type='submit' name='formAction' value='discountAmount' $disableDiscount >Apply</button><br>";

            $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
            $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
            $lineTotal=0;
            foreach($lineItems as $line){
                $lineTotal= $lineTotal + ($line['Cost']); 
            }
            $discountTotal = $lineTotal - $orderTotal;
            $discountTotal = round($discountTotal, $precision = 2);
            echo "<label>Current discount: &dollar;{$discountTotal}</label><br>";
            $orderTotal = round($orderTotal, $precision = 2);
            echo "<label>Amount: &dollar;{$orderTotal}</label>";
        echo "</form>";

    //Finalize quote/Sanction Quote/Order quote button
    if($quote['OrderStatus'] !== 'ordered'){ 
        $disableSubmit = '';
        if ($lineCount <= 0 && $quote['OrderStatus'] != 'sanctioned') {
            $disableSubmit = 'disabled';
            $buttonMsg = 'At least one line item is required to ';
        }
        echo "<form method=\"POST\">";
        
            $quoteID = isset($_GET['quoteID']) ? $_GET['quoteID']: $quoteID;
            $email = isset($_POST['Email']) ? $_POST['Email'] : $quote['Email'];
            //foreach($quote as $k => $v){
           // echo "<input type=hidden name='{$k}' value={$v}>";
           // }
            echo "<input type=hidden name='quoteID' value={$quoteID}>";
            //echo "<input type=hidden name='email' value={$email}>";
            if(($_SESSION['userType'] == "Superuser" || $_SESSION['userType']=='Sales Associate') && $quote['OrderStatus'] == "open"){
                echo "<label for=submitBtn><p>{$buttonMsg}</p> </label>";
                echo "<button type=submit name=submitBtn value='Finalize Quote' id=submitBtn $disableSubmit>Finalize Quote</button>";
            }
            if(($_SESSION['userType'] == "Superuser" || $_SESSION['userType']=='Headquarters') && $quote['OrderStatus'] == "finalized"){
                echo "<label for=submitBtn><p>{$buttonMsg}</p> </label>";
                echo "<button type=submit name=submitBtn value='Sanction Quote' id=submitBtn $disableSubmit>Sanction Quote</button>";
            }
            if(($_SESSION['userType'] == "Superuser" ||$_SESSION['userType']=='Headquarters') && $quote['OrderStatus'] == "sanctioned"){
                echo "<label for=submitBtn><p>{$buttonMsg}</p> </label>";
                echo "<button type=submit name=submitBtn value='Order Quote' id=submitBtn $disableSubmit>Order Quote</button>";
            }
            
        echo "</form>";
    }
   

}
catch(PDOexception $e) {
    echo "Connection failed: " . $e->getMessage();
}

//dirty and inefficent
//designed to run after after a quote query
//returns updated quote query
function addToTotal($pdo, $quote, $quoteID, $cost) {
    // $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
    // $quote = $result->fetch(PDO::FETCH_ASSOC);

    $orderTotal = $quote['OrderTotal'] + $cost;

    if($orderTotal < 0)
        $orderTotal = 0;

    $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
    $prepared->execute([$orderTotal]);

    $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
    $quote = $result->fetch(PDO::FETCH_ASSOC);

    return $quote;
}
function editTotal($pdo, $quote, $quoteID, $lineID, $cost) {
    // $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
    // $quote = $result->fetch(PDO::FETCH_ASSOC);

    // $orderTotal = $quote['OrderTotal'] + $cost;

    $result = $pdo->query("SELECT * FROM LineItems WHERE LineID = $lineID AND QuoteID = $quoteID");
    $lineItem = $result->fetch(PDO::FETCH_ASSOC);

    $orderTotal = $quote['OrderTotal'] + $cost - $lineItem['Cost'];

    if($orderTotal < 0)
        $orderTotal = 0;

    $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
    $prepared->execute([$orderTotal]);

    $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
    $quote = $result->fetch(PDO::FETCH_ASSOC);

    return $quote;
}
?>
</body>
</html>
<style>