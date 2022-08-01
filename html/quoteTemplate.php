<?php 
session_start(['name' => 'quotes']); 

include '../lib/db.php';
include '../lib/func.php';

// form action for advancing quote status
// show a success or an error message
$msg = ''; 
if(isset($_POST['submitBtn']) && isset($_POST['submitBtn'])){

    $msg = advanceQuoteStatus($_GET['quoteID'], $_POST['submitBtn'] ); 
    unset($_POST['submitBtn']);
}    
$pagetitle="View Quote";
include 'header.php';

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
            
            // requery quote
            $orderTotal = $lineTotal - $discount;
            $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
            $prepared->execute([$orderTotal]);

            $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
            $quote = $result->fetch(PDO::FETCH_ASSOC);
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
            // get order info
            $sql = "SELECT * FROM PurchaseOrders where QuoteID = ?";
            try{
                $statement = $pdo->prepare($sql);
                if($statement->execute([$quoteID])){
                    if($statement->rowCount()){ 
                        $order = $statement->fetch(PDO::FETCH_ASSOC);
                    } else { echo "<p>No rows</p>"; }
                } else { echo "<p>The querey failed</p>"; }
            }
            catch(PDOExecption $e){ echo "<p>There was an error with the database: {$e->getMessage()}</p>"; }
            break;
    }

    // allow sales associate and admin to see quote in a readonly state
    if($_SESSION['userType'] == "Sales Associate" && $quote['OrderStatus'] !== "open" || $_SESSION['userType'] == "Administrator" ){
        $disableEmail = 'disabled';
        $disableLines = 'disabled';
        $disableNotes = 'disabled';
        $disableDiscount = 'disabled';
        $buttonText = '';
    }   
 
    echo <<<HTML
            <div class="container row justify-content-center ">
                <div class="col-md-10">
                    <div class="card mt-3">
                        <div class="card-header">    
                            <h2 class="mb-3">Quote $quoteID - Status: {$quote['OrderStatus']} </h2>
                       
        <div class='errorMsg'>$errorMsg</div>
        <!--- Print message confirming order -->  
        <p>$msg</p>  
        <h2>$CustomerName</h2>
        <div class=\"address\">$city<br>$street<br>$contact<br> </div> </div>
    HTML;

    // if has order, show info
    if (isset($order)) {
        echo "<div>";
        echo "<br>Fullfilled on: " . $order['ProcessDate'];
        echo "<br>Commission Rate: " . $order['CommissionRate'] . "%";
        echo "<br>Commission: $" . number_format((0.01 * $order['CommissionRate'] * $order['OrderTotal']), 2);
        echo "</div>";
    }
    echo "</div>";

    // Email
    echo "<h4 class='mb-3'>Email:</h4>";
    echo "<form class=\"email\" action=\"\" method=\"POST\">";  
    echo " <div class='row mb-3 '>"; 
    echo " <div class='col'>"; 
        echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
        echo "<input type=\"email\" id='email' class='form-control' name=\"Email\" value=\"$email\" required $disableEmail>";
        echo "</div>";
        echo " <div class='col'>"; 
        echo "<button class='btn btn-secondary' type=\"submit\" name=\"formAction\" value=\"email\" $disableEmail>Save email</button>";
    echo "</form>";
    echo "</div>";    echo "</div>"; 


    // Line Items
    
    echo "<h4 class='mb-3'>Line Items:</h4>";
    $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
    $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
    $lineCount = 0;
    foreach ($lineItems as $row) {
        $lineCount++;
        // jump to element after action
        if (isset($_POST['lineID']))
            if ($lineCount == $_POST['lineID']) 
                echo "<div id='editedLine'> </div>";
        echo "<form action='#editedLine' method='POST'>";
        echo " <div class='row mb-1'>"; 
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo " <div class='col'>"; 
            echo "<input class='form-control' type=\"text\" name='editDesc' value=\"{$row["ServiceDesc"]}\" required $disableLines>"; 
            echo "</div>";
            echo " <div class='col'>"; 
            echo "<input class='form-control' type=\"number\"  name='editCost' value=\"{$row["Cost"]}\"] min='0' step='0.01' required $disableLines>";
            echo "<input type=\"hidden\" name=\"lineID\" value=\"{$row['LineID']}\"/>";
            echo "</div>";
            echo " <div class='col'>"; 
            echo "<button class='btn btn-secondary me-1' type=\"submit\" name='formAction' value='editLine' $disableLines>Edit</button>";
            echo "<button class='btn btn-secondary' type=\"submit\" name='formAction' value='deleteLine' $disableLines>Delete</button><br>";
            echo "</div>";
            echo "</div>";
        echo "</form>";
    } 

    if ($lineCount == 0) { echo "<div class='noItem'>No line items</div>"; }

    // add lines
    if (!$disableLines) {
        echo "<form class='' id='addedLine' action='#addedLine' method='POST'>";
        echo " <div class='row mb-3 '>"; 
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo " <div class='col'>"; 
            echo "<input class='form-control' type='text' name='addDesc' placeholder='Service Description' required $disableLines>";
            echo "</div>";
            echo " <div class='col'>";
            echo "<input class='form-control' type='number' name='addCost' placeholder='Service Cost' min='0' step='0.01' required $disableLines>";
            echo "</div>";
            echo " <div class='col'>";
            echo "<button class='btn btn-secondary' type='submit'name='formAction' value='addLine' $disableLines>Add Line Item</button >";
            echo "</div>";
        echo "</form >";
        echo "</div>";
    }
    
    // Secret Notes
    echo "<h4 class='mb-3'>Secret Notes:</h4>";
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
        echo " <div class='row mb-1'>"; 
        echo " <div class='col'>"; 
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input class='form-control' type=\"text\" name='editNote' value=\"{$row['Note']}\" required $disableNotes>";
            echo "<input type=\"hidden\" name=\"noteID\" value=\"{$row['NoteID']}\"/>";
            echo "</div>";
            echo " <div class='col'>"; 
            echo "<button class='btn btn-secondary me-1' type=\"submit\" name=\"formAction\" value=\"editNote\" $disableNotes>Edit</button>";
            echo "<button class='btn btn-secondary' type=\"submit\" name=\"formAction\" value=\"deleteNote\"$disableNotes>Delete</button><br>";
            echo "</div>";
         echo "</form>";
         echo "</div>";
    }

    if ($count == 0) { echo "<div class='noItem'>No secret notes</div>"; }

    // add secret notes
    if (!$disableNotes) {
        echo "<form id='addedNote' action='#addedNote' method='POST'>";
        echo " <div class='row mb-1'>"; 
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo " <div class='col'>"; 
            echo "<input class='form-control' type='text' name='addNote' placeholder='Note' required $disableNotes>";
            echo "</div>";
            echo " <div class='col'>";
            echo "<button class='btn btn-secondary' type='submit' name='formAction' value='addNote' $disableNotes>Add Secret Note</button>";
            echo "</div>";
        echo "</form>";
    }

    // Discount
    $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
    $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
    $lineTotal=0;
    foreach($lineItems as $line){
        $lineTotal= $lineTotal + ($line['Cost']); 
    }

       // show current discounted amount
       $discountTotal = $lineTotal - $orderTotal;
       $discountTotal = round($discountTotal, $precision = 2);
       echo "<h4 class='mt-3 mb-2'>Current discount: &dollar;{$discountTotal}</h4>";

    echo "<form id='discounted' class='discountPercent' action='#discounted' method='POST'>";
    echo " <div class='row mb-1'>"; 
        echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
        echo " <div class='col'>";
        echo "<input class='form-control' type='number' name='discount' placeholder='Discount %:' min='0' max='100' required $disableDiscount >";
        echo " </div>";
        echo " <div class='col'>";
        echo "<button class='btn btn-secondary' type='submit' name='formAction' value='discountPercent' required $disableDiscount >Apply</button><br>";
        echo " </div>";
        echo " </div>";
    echo "</form>";
    echo "<form class='discountAmount' action='#discounted' method='POST'>";
    echo " <div class='row'>"; 
        echo " <div class='col'>";
        echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
        echo "<input class='form-control' type='number' name='discount' placeholder='Discount Sum:' min='0' max='{$lineTotal}' step='0.01' required  $disableDiscount >";
        echo "</div>";
        echo "<div class='col'>";
        echo "<button class='btn btn-secondary' type='submit' name='formAction' value='discountAmount' $disableDiscount >Apply</button><br>";
        echo "</div>";
        echo " </div>";
    echo "</form>";
    echo "<small class='text-muted'>To remove discount, enter zero and click apply.</small>";


    //Display order total minus discount
    $orderTotal = round($orderTotal, $precision = 2);
    echo "<h4 class='mt-3 mb-2'>Subtotal: &dollar;{$orderTotal}</h4>";

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
            echo "<input type=hidden name='quoteID' value={$quoteID}>";
            echo "<div class='row'>";
            if(($_SESSION['userType'] == "Superuser" || $_SESSION['userType']=='Sales Associate') && $quote['OrderStatus'] == "open"){
               // echo "<label for=submitBtn><p>{$buttonMsg}</p> </label>"; echo "&nbsp";
                echo "<p class='mt-3 mb-2'>{$buttonMsg}</p>";
                echo "<button button class='btn  btn-success' type=submit name=submitBtn value='Finalize Quote' id=submitBtn $disableSubmit>Finalize Quote</button>";
            }
            if(($_SESSION['userType'] == "Superuser" || $_SESSION['userType']=='Headquarters') && $quote['OrderStatus'] == "finalized"){
                //echo "<label for=submitBtn><p>{$buttonMsg}</p> </label>"; echo "&nbsp";
                echo "<p class='mt-3 mb-2'>{$buttonMsg}</p>";

                echo "<button button class='btn  btn-success' type=submit name=submitBtn value='Sanction Quote' id=submitBtn $disableSubmit>Sanction Quote</button>";
            }
            if(($_SESSION['userType'] == "Superuser" ||$_SESSION['userType']=='Headquarters') && $quote['OrderStatus'] == "sanctioned"){
                //echo "<label for=submitBtn><p>{$buttonMsg}</p> </label>"; echo "&nbsp";
                echo "<p class='mt-3 mb-2'>{$buttonMsg}</p>";

                echo "<button button class='btn  btn-success' type=submit name=submitBtn value='Order Quote' id=submitBtn $disableSubmit>Order Quote</button>";
            }
        echo "</form>";
        echo "</div>";
    }
}
catch(PDOexception $e) {
    echo "Connection failed: " . $e->getMessage();
}

// addToTotal
// input: internal database pdo, quote info, quoteID, cost to add
// returns updated quote query
//
// adds new line item cost to overall total
// dirty and inefficent
// designed to run after after a quote query
function addToTotal($pdo, $quote, $quoteID, $cost) {
    $orderTotal = $quote['OrderTotal'] + $cost;

    if($orderTotal < 0)
        $orderTotal = 0;

    $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
    $prepared->execute([$orderTotal]);

    $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
    $quote = $result->fetch(PDO::FETCH_ASSOC);

    return $quote;
}

// editTotal
// input: internal database pdo, quote info, quoteID, line id of edited line, cost to add
// returns updated quote query
//
// makes overal total reflects line item cost changes
function editTotal($pdo, $quote, $quoteID, $lineID, $cost) {
    $result = $pdo->query("SELECT * FROM LineItems WHERE LineID = $lineID AND QuoteID = $quoteID");
    $lineItem = $result->fetch(PDO::FETCH_ASSOC);

    $orderTotal = $quote['OrderTotal'] + $cost - $lineItem['Cost'];

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