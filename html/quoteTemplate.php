<?php session_start(['name' => 'quotes']); ?>

<?php 
include '../lib/db.php';
include '../lib/func.php';

  //************move this to the top
  if(isset($_POST['submitBtn']) && isset($_POST['submitBtn'])){
    echo "***6***";
    advanceQuoteStatus($_POST['quoteID'], $_POST['submitBtn']); //this is broken now
    echo "***7***";
    unset($_POST['submitBtn']);
    //header('location:open.php');
}    
include 'header.php';

//debug print
echo "ignore this - debug info"; 
echo "<br>";
echo "<pre>  'SESSION'";  
print_r($_SESSION);   
echo "</pre>" ;

echo "<br>";
echo "<pre>  'POST'";  
print_r($_POST);   
echo "</pre>" ;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>View Quote</title>
<meta charset="utf-8">
<link rel="stylesheet" href="../public/css/quote.css">
</head>
<body>
<?php
error_reporting(E_ALL);
try {

    $pdo = connectdb();
    $legacy = connectlegacy();
   
    //if "Create New Quote is pushed"
    if(!isset($_POST["quoteID"])){
    
        $id = $_POST['id']; 
        $sql = "SELECT * FROM customers WHERE id = $id";
        $result = $legacy->query($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);   

        echo "Calling openQuote function";
        $quoteID = createQuote($pdo,  $_POST['id'], $row['name'], $row['city'], $row['street'], $row['contact'], $_POST['email']);

        if($quoteID){ 
            echo "<br>";
            echo "Created a quote for {$row['name']}. <br> Quote number: {$quoteID}";
            } else { echo "didn't create quote!";}
        
    }

    // quoteID from form, else use newly created one
    $quoteID = isset($_POST['quoteID']) ? $_POST['quoteID']: $quoteID;
    $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
    $quote = $result->fetch(PDO::FETCH_ASSOC);

    // custID from quote
    $customerID = $quote["CustomerID"];
    $result = $legacy->query("SELECT * FROM customers where id = $customerID");
    $cust = $result->fetch(PDO::FETCH_ASSOC);  


    // POST values
    $formAction = isset($_POST['formAction']) ? $_POST['formAction'] : '';
    $orderTotal = $quote['OrderTotal'];

    // Legacy DB values
    $email = isset($_POST['Email']) ? $_POST['Email'] : $quote['Email'];
    $CustomerName = isset($cust['name']) ? $cust['name'] : "Invalid customer selected";
    $city = isset($cust['city']) ? $cust['city'] : "No city found";
    $street = isset($cust['street']) ? $cust['street'] : "No street found";
    $contact = isset($cust['contact']) ? $cust['contact'] : "No contact found";

    //FORM FUNCTIONS

    switch($formAction){
        case 'email':
            $prepared = $pdo->prepare("UPDATE Quotes SET Email=? WHERE QuoteID = $quoteID");
            $prepared->execute([$email]);
            break;
        case 'editLine':
            if (isset($_POST['lineID']) && isset($_POST['editDesc']) && is_numeric($_POST['editCost'])) {
                $quote = editTotal($pdo, $quote, $quoteID, $_POST['lineID'], $_POST['editCost']);
                $prepared = $pdo->prepare("UPDATE LineItems SET ServiceDesc=?, Cost=? WHERE LineID=? AND quoteID = $quoteID");
                $prepared->execute([$_POST['editDesc'], $_POST['editCost'], $_POST['lineID']]);
            }
            else {
                echo "Error editing line item";
            }
            break;
        case 'deleteLine':
            if (isset($_POST['lineID'])) {
                $prepared = $pdo->prepare("DELETE FROM LineItems WHERE LineID=? AND quoteID=$quoteID");
                $prepared->execute([$_POST['lineID']]);
                $quote = addToTotal($pdo, $quote, $quoteID, (-1 * $_POST['editCost']));
            }
            else {
                echo "Error deleting line";
            }
            break;
        case 'addLine':
            if (isset($_POST['addDesc']) && is_numeric($_POST['addCost'])) {
                $prepared = $pdo->prepare("INSERT INTO LineItems SET ServiceDesc=?, Cost=?, quoteID=$quoteID");
                $prepared->execute([$_POST['addDesc'], $_POST['addCost']]);

                $quote = addToTotal($pdo, $quote, $quoteID, $_POST['addCost']);
            }
            else {
                echo "Error adding line";
            }
            break;
        case 'editNote':
            if (isset($_POST['noteID']) && isset($_POST['editNote'])) {
                $prepared = $pdo->prepare("UPDATE Notes SET Note=? WHERE NoteID=? AND quoteID = $quoteID");
                $prepared->execute([$_POST['editNote'], $_POST['noteID']]);
            }
            else {
                echo "Error editing secret note";
            }
            break;
        case 'deleteNote':
            if (isset($_POST['noteID'])) {
                $prepared = $pdo->prepare("DELETE FROM Notes WHERE NoteID=? AND quoteID=$quoteID");
                $prepared->execute([$_POST['noteID']]);
            }
            else {
                echo "Error deleting secret note";
            }
            break;
        case 'addNote':
            if (isset($_POST['addNote'])) {
                $prepared = $pdo->prepare("INSERT INTO Notes SET Note=?, quoteID=$quoteID");
                $prepared->execute([$_POST['addNote']]);
            }
            else {
                echo "Error adding secret note";
            }
            break;
        case 'discountPercent':
            $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
            $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
            $lineTotal=0;
            foreach($lineItems as $line){
                $lineTotal= $lineTotal + ($line['Cost']); 
            }
            $orderTotal =   $lineTotal  - (0.01 * (float)$_POST['discount'] * $lineTotal);
            $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
            $prepared->execute([$orderTotal]);

            $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
            $quote = $result->fetch(PDO::FETCH_ASSOC);
            break;
        case 'discountAmount':
            $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
            $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
            $lineTotal=0;
            foreach($lineItems as $line){
                $lineTotal= $lineTotal + ($line['Cost']); 
            }
            $discount = (float)$_POST['discount'];
            if ($discount > $lineTotal) {
                // echo "Error: discount too large";
            }
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
            $buttonText = 'Finalize Quote';
            $buttonMsg = "To finalize this quote and submit it to processing in headquarters, click here: ";
            break;
        case 'finalized':
            $disableEmail = 'disabled';
            $disableLines = '';
            $disableNotes = '';
            $disableDiscount = '';
            $buttonText = 'Sanction Quote';
            $buttonMsg = "To sanction this quote and email it to the customer, click here: ";
            break;
        case 'sanctioned':
            $disableEmail = 'disabled';
            $disableLines = 'disabled';
            $disableNotes = 'disabled';
            $disableDiscount = '';
            $buttonText = 'Order Quote';
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
    echo <<< html
        <h2>$CustomerName</h2>

        <div class=\"address\">$city<br>$street<br>$contact<br></div>
    html;

    // Email
    echo "<form class=\"email\" action=\"\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
        echo "<input type=\"hidden\" name=\"formAction\" value=\"email\">";
        echo "Email: ";
        echo "<input type=\"email\" name=\"Email\" value=\"$email\" required";
        echo "$disableEmail><button type=\"submit\" $disableEmail>Save email</button>";
    echo "</form>";

    // Line Items
    echo "<h1>Line Items:</h1>";
    
    $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
    $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);
    $lineTotalB = 0;
    $count = 0;
    foreach ($lineItems as $row) {
        $count++;
        if (isset($_POST['lineID']))
            if ($count == $_POST['lineID'])
                echo "<div id='editedLine'>";
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"ServiceDesc\"]\" disabled=\"disabled\">";
        // $ServiceDesc = $row["ServiceDesc"];
        // $Cost = $row["Cost"];
        echo "<form action='#editedLine' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type=\"text\" name='editDesc' value=\"{$row["ServiceDesc"]}\" required $disableLines>";
            echo "<input type=\"number\" name='editCost' value=\"{$row["Cost"]}\"] min='0' step='0.01' required $disableLines>";
            echo "<input type=\"hidden\" name=\"lineID\" value=\"{$row['LineID']}\"/>";
            echo "<button type=\"submit\" name='formAction' value='editLine' $disableLines>Edit</button>";
            echo "<button type=\"submit\" name='formAction' value='deleteLine' $disableLines>Delete</button><br>";
        echo "</form>";
        // echo "<input type=\"text\" value=\"$ServiceDesc\"] disabled=\"disabled\"><br>";
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"Cost\"]\" disabled=\"disabled\">";  
    }

    if ($count == 0) {
        echo "<div class='noItem'>No line items</div>";
    }

    if (!$disableLines) {
        echo "<form id='addedLine' action='#addedLine' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type='text' name='addDesc' placeholder='Service Description' required $disableLines>";
            echo "<input type='number' name='addCost' placeholder='Service Cost' min='0' step='0.01' required $disableLines>";
            echo "<input type='hidden' name='formAction' value='addLine'>";
            echo "<button type='submit'$disableLines>Add Line Item</button >";
        echo "</form >";
    }

    // Secret Notes
    echo "<h1>Secret Notes:</h1>";
    // echo "query quote table";

    $result = $pdo->query("SELECT * FROM Notes where QuoteID = $quoteID");
    $secretNotes = $result->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;
    foreach ($secretNotes as $row) {
        $count++;
        if (isset($_POST['noteID']))
            if ($count == $_POST['noteID'])
                echo "<div id='editedNote'>";
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"ServiceDesc\"]\" disabled=\"disabled\">";
        echo "<form action='#editedNote' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type=\"text\" name='editNote' value=\"{$row['Note']}\" required $disableNotes>";
            echo "<input type=\"hidden\" name=\"noteID\" value=\"{$row['NoteID']}\"/>";
            echo "<button type=\"submit\" name=\"formAction\" value=\"editNote\" $disableNotes>Edit</button>";
            echo "<button type=\"submit\" name=\"formAction\" value=\"deleteNote\"$disableNotes>Delete</button><br>";
         echo "</form>";
        // echo "<input type=\"text\" value=\"$ServiceDesc\"] disabled=\"disabled\"><br>";
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"Cost\"]\" disabled=\"disabled\">";
    }

    if ($count == 0) {
        echo "<div class='noItem'>No secret notes</div>";
    }

    if (!$disableNotes) {
        echo "<form id='addedNote' action='#addedNote' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type='text' name='addNote' placeholder='Note' required $disableNotes>";
            echo "<input type='hidden' name='formAction' value='addNote' $disableNotes>";
            echo "<button type='submit'$disableNotes>Add Secret Note</button>";
        echo "</form>";
    }

    // Discount
        echo "<form id='discounted' class='discountPercent' action='#discounted' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            // echo "<input type='hidden' name='formAction' value='discount'>";
            echo "<label>Discount %: </label>";
            echo "<input type='number' name='discount' placeholder='' min='0' max='100' required $disableDiscount >";
            echo "<button type='submit' name='formAction' value='discountPercent' required $disableDiscount >Apply</button><br>";
        echo "</form>";
        echo "<form class='discountAmount' action='#discounted' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<label>Discount Amount: </label>";
            echo "<input type='number' name='discount' placeholder='' min='0' max='{$quote['OrderTotal']}' step='0.01' required  $disableDiscount >";
            echo "<button type='submit' name='formAction' value='discountAmount' $disableDiscount >Apply</button><br>";
            // echo "<input type='radio' name='formAction' value='discountPercent' checked>percent";
            // echo "<input type='radio' name='formAction' value='discountAmount'>amount";
            //$orderTotal = number_format($quote['OrderTotal'],2);
            if(isset($lineTotal)){
            $discountTotal = $lineTotal - $orderTotal;
            $discountTotal = round($discountTotal, $precision = 2);
            echo "<label>Current discount: &dollar;{$discountTotal}</label><br>";
            }
            echo "current quote total " . $quote['OrderTotal'] . "<br>";
            $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
            $quote = $result->fetch(PDO::FETCH_ASSOC);
            echo "query quote total " . $quote['OrderTotal'] . "<br>";
            echo "<label>Amount: &dollar;{$orderTotal}</label>";
        echo "</form>";

    //Finalize quote/Sanction Quote/Order quote button
    if($quote['OrderStatus'] !== 'ordered'){ 
    echo "<form action=\"\" method=\"POST\">";
      
        $quoteID = isset($_POST['quoteID']) ? $_POST['quoteID']: $quoteID;

        echo "<input type=hidden name='quoteID' value={$quoteID}>";
        echo "<label for=submitBtn><p>{$buttonMsg}</p> </label>";
        echo "<button type=submit name=submitBtn value='{$buttonText}' id=submitBtn>$buttonText</button>";
        //echo "<script type='text/javascript'>alert('Username'".$username.");</script>";
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