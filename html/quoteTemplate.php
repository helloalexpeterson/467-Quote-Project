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
    include '../lib/db.php';
    include '../lib/func.php';

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

    // GET QUOTE ID FROM FORM
    $quoteID = isset($_POST['quoteID']) ? $_POST['quoteID']: $quoteID;
    if ($quoteID) {
        $result = $pdo->query("SELECT * FROM Quotes where QuoteID = $quoteID");
        // $result = $pdo->query("SELECT * FROM Quotes where QuoteID = 1");
        $quote = $result->fetch(PDO::FETCH_ASSOC);
    }
    // $customerID = $quote["CustomerID"];
    $customerID = isset($_POST['customerID']) ? $_POST['customerID'] : $quote["CustomerID"];
    if ($customerID) {
        $result = $legacy->query("SELECT * FROM customers where id = $customerID");
        $cust = $result->fetch(PDO::FETCH_ASSOC);  
    }

    //IMPORTANT
    //IMPORTANT
    //INDIVIDUAL FORMS FOR EACH SECTION, HTML ALONES HAS NO NESTED FORMS
    //php is not reactive
    //THERE IS NO MEMORY
    //DISCUSS HOW TO WORK AROUND
    //POSSIBLE TEMP DB???????

    // POST values
    // THESE WILL CHANGE THE RENDERING AND AVAILABLE FUNCTIONS
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $formAction = isset($_POST['formAction']) ? $_POST['formAction'] : '';
    $new = isset($_POST['new']) ? $_POST['new'] : '';
    $orderTotal = $quote['OrderTotal'];

    // Legacy DB values
    $email = isset($_POST['Email']) ? $_POST['Email'] : $quote['Email'];
    $CustomerName = isset($cust['name']) ? $cust['name'] : "Invalid customer selected";
    $city = isset($cust['city']) ? $cust['city'] : "No city found";
    $street = isset($cust['street']) ? $cust['street'] : "No street found";
    $contact = isset($cust['contact']) ? $cust['contact'] : "No contact found";

    //FORM FUNCTIONS

    if ($formAction == "email") {
        $prepared = $pdo->prepare("UPDATE Quotes SET Email=? WHERE QuoteID = $quoteID");
        $prepared->execute([$email]);
    }
    else if ($formAction == 'editLine') {
        if (isset($_POST['lineID']) && isset($_POST['editDesc']) && is_numeric($_POST['editCost'])) {
            // echo $_POST['editDesc'] . $_POST['editCost'] . $_POST['lineID'];
            // $prepared = $pdo->prepare("UPDATE LineItems SET Cost=? WHERE LineID=?");
            // $prepared->execute([11.2, 1]);
            $prepared = $pdo->prepare("UPDATE LineItems SET ServiceDesc=?, Cost=? WHERE LineID=? AND quoteID = $quoteID");
            // $prepared->execute(['new desc', 10.1, 1]);
            $prepared->execute([$_POST['editDesc'], $_POST['editCost'], $_POST['lineID']]);
        }
        else {
            echo "Error editing line item";
        }
    }
    else if ($formAction == 'deleteLine') {
        if (isset($_POST['lineID'])) {
            $prepared = $pdo->prepare("DELETE FROM LineItems WHERE LineID=? AND quoteID=$quoteID");
            $prepared->execute([$_POST['lineID']]);
        }
        else {
            echo "Error deleting line";
        }
    }
    else if ($formAction == 'addLine') {
        if (isset($_POST['addDesc']) && is_numeric($_POST['addCost'])) {
            $prepared = $pdo->prepare("INSERT INTO LineItems SET ServiceDesc=?, Cost=?, quoteID=$quoteID");
            $prepared->execute([$_POST['addDesc'], $_POST['addCost']]);
        }
        else {
            echo "Error adding line";
        }
    }
    else if ($formAction == 'editNote') {
        if (isset($_POST['noteID']) && isset($_POST['editNote'])) {
            $prepared = $pdo->prepare("UPDATE Notes SET Note=? WHERE NoteID=? AND quoteID = $quoteID");
            $prepared->execute([$_POST['editNote'], $_POST['noteID']]);
        }
        else {
            echo "Error editing secret note";
        }
    }
    else if ($formAction == 'deleteNote') {
        if (isset($_POST['noteID'])) {
            $prepared = $pdo->prepare("DELETE FROM Notes WHERE NoteID=? AND quoteID=$quoteID");
            $prepared->execute([$_POST['noteID']]);
        }
        else {
            echo "Error deleting secret note";
        }
    }
    else if ($formAction == 'addNote') {
        if (isset($_POST['addNote'])) {
            $prepared = $pdo->prepare("INSERT INTO Notes SET Note=?, quoteID=$quoteID");
            $prepared->execute([$_POST['addNote']]);
        }
        else {
            echo "Error adding secret note";
        }
    }
    else if ($formAction == 'discountPercent') {
        $orderTotal = $quote['OrderTotal'] - (0.01 * (float)$_POST['discount'] * $quote['OrderTotal']);
        // echo $newTotal;
        $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
        $prepared->execute([$orderTotal]);
    }
    else if ($formAction == 'discountAmount') {
        $discount = (float)$_POST['discount'];
        // echo $discount;
        if ($discount > $quote['OrderTotal']) {
            // echo "Error: discount too large";
        }
        else {
            $orderTotal = $quote['OrderTotal'] - $discount;
            // echo $newTotal;
            $prepared = $pdo->prepare("UPDATE Quotes SET OrderTotal=? WHERE QuoteID = $quoteID");
            $prepared->execute([$orderTotal]);
        }
    }

    //END OF FORM FUNC

    //Name and Address
    echo <<< html
        // DEBUG
        <h2>SAVE \$action TO php session IF POSSIBLE, ELSE NEED TO DISCUSS</h2>
        The page's action is set to 
        $action
        <br>The page's formAction is set to 
        $formAction
        // DEBUG
    
    
        <h2>$CustomerName</h2>

        <div class=\"address\">$city<br>$street<br>$contact<br></div>
    html;

    // Email
    echo "<form class=\"email\" action=\"\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
        echo "<input type=\"hidden\" name=\"formAction\" value=\"email\">";
        echo "Email: ";
        echo "<input type=\"email\" name=\"Email\" value=\"$email\"";
        // if ($action != "create") 
        //     echo "disabled=\"disabled\">";
        // else
            echo "><button type=\"submit\">Save email</button>";
    echo "</form>";

    // Line Items
    echo "<h1>Line Items:</h1>";
    
    
    $result = $pdo->query("SELECT * FROM LineItems where QuoteID = $quoteID");
    $lineItems = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lineItems as $row) {
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"ServiceDesc\"]\" disabled=\"disabled\">";
        // $ServiceDesc = $row["ServiceDesc"];
        // $Cost = $row["Cost"];
        echo "<form action='' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type=\"text\" name='editDesc' value=\"{$row["ServiceDesc"]}\" required>";
            echo "<input type=\"number\" name='editCost' value=\"{$row["Cost"]}\"] min='0'>";
            echo "<input type=\"hidden\" name=\"lineID\" value=\"{$row['LineID']}\"/>";
            echo "<button type=\"submit\" name='formAction' value='editLine'>Edit</button>";
            echo "<button type=\"submit\" name='formAction' value='deleteLine'>Delete</button><br>";
        echo "</form>";
        // echo "<input type=\"text\" value=\"$ServiceDesc\"] disabled=\"disabled\"><br>";
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"Cost\"]\" disabled=\"disabled\">";


  
    }

    if ($action != "process") {
        echo "<form action='' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type='text' name='addDesc' placeholder='Service Description' required>";
            echo "<input type='number' name='addCost' placeholder='Service Cost' min='0'>";
            echo "<input type='hidden' name='formAction' value='addLine'>";
            echo "<button type='submit'>Add Line Item</button>";
        echo "</form>";
    }

    // Secret Notes
    echo "<h1>Secret Notes:</h1>";
    // echo "query quote table";

    $result = $pdo->query("SELECT * FROM Notes where QuoteID = $quoteID");
    $secretNotes = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($secretNotes as $row) {
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"ServiceDesc\"]\" disabled=\"disabled\">";
        echo "<form action='' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type=\"text\" name='editNote' value=\"{$row['Note']}\" required>";
            echo "<input type=\"hidden\" name=\"noteID\" value=\"{$row['NoteID']}\"/>";
            echo "<button type=\"submit\" name=\"formAction\" value=\"editNote\">Edit</button>";
            echo "<button type=\"submit\" name=\"formAction\" value=\"deleteNote\">Delete</button><br>";
         echo "</form>";
        // echo "<input type=\"text\" value=\"$ServiceDesc\"] disabled=\"disabled\"><br>";
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"Cost\"]\" disabled=\"disabled\">";
    }

    if ($action != "process") {
        echo "<form action='' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<input type='text' name='addNote' placeholder='Note' required>";
            echo "<input type='hidden' name='formAction' value='addNote'>";
            echo "<button type='submit'>Add Secret Note</button>";
        echo "</form>";
    }

    // Discount
        echo "<form class='discountPercent' action='' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            // echo "<input type='hidden' name='formAction' value='discount'>";
            echo "<label>Discount %: </label>";
            echo "<input type='number' name='discount' placeholder='' min='0' max='100'>";
            echo "<button type='submit' name='formAction' value='discountPercent'>Apply</button><br>";
        echo "</form>";
        echo "<form class='discountAmount' action='' method='POST'>";
            echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
            echo "<label>Discount Amount: </label>";
            echo "<input type='number' name='discount' placeholder='' min='0' step='0.01'>";
            echo "<button type='submit' name='formAction' value='discountAmount'>Apply</button>";
            // echo "<input type='radio' name='formAction' value='discountPercent' checked>percent";
            // echo "<input type='radio' name='formAction' value='discountAmount'>amount";
            echo "<br>Amount: {$orderTotal} table line item";
        echo "</form>";

    // Create/Update Button
    // echo "<form class=\"update\" action=\"\" method=\"POST\">";
    // echo "<input type=\"hidden\" name=\"formAction\" value=\"update\">";
    //     if($new)
    //         echo "<button type=\"submit\">Create</button>";
    //     else
    //         echo "<button type=\"submit\">Update</button>";
    // echo "</form>";

    // View completion button
    echo "<form action=\"\" method=\"POST\">";
        if($action == "create") {
            echo "To finalize this quote and submit it to processing in headquarters, click here: ";
            //
            // TEMP TEST
            //
            echo "<input type=\"hidden\" name=\"action\" value=\"sanction\">";
            //
            //
            //
            echo "<input type=\"hidden\" name=\"formAction\" value=\"foo\">";
            echo "<button type=\"submit\">Finalize Quote [TEMP: action=sanction][REQUIRE EMAIL]</button>";
        }
        else if($action == "sanction") {
            echo "To sanction this quote and email it to the customer, click here: ";
            //
            // TEMP TEST
            //
            echo "<input type=\"hidden\" name=\"action\" value=\"process\">";
            //
            //
            //
            echo "<input type=\"hidden\" name=\"formAction\" value=\"foo\">";
            echo "<button type=\"submit\">Sanction Quote [TEMP: action=process]</button>";
        }
        else if($action == "process") {
            echo "To convert this quote into an order and process it, click here: ";
            //
            // TEMP TEST
            //
            echo "<input type=\"hidden\" name=\"action\" value=\"done\">";
            //
            //
            //
            echo "<input type=\"hidden\" name=\"formAction\" value=\"foo\">";
            echo "<button type=\"submit\">Process PO [TEMP: action=done]</button>";
        }
        else {
            //
            // TEMP TEST
            //
            echo "<input type=\"hidden\" name=\"action\" value=\"create\">";
            echo "<input type=\"hidden\" name=\"new\" value=\"1\">";
            echo "<input type=\"hidden\" name=\"CustomerName\" value=\"????Get Customer Name from POST value??????\">";
            //
            //
            //
            echo "<input type=\"hidden\" name=\"formAction\" value=\"foo\">";
            echo "<button type=\"submit\">Unknown Action [TEMP: action=create]</button>";
        }
    echo "</form>";
}
catch(PDOexception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
</body>
</html>
<style>