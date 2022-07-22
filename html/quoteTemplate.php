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
        $quote = $result->fetchAll(PDO::FETCH_ASSOC);
    }
    // $customerID = $quote[0]["CustomerID"];
    $customerID = isset($_POST['customerID']) ? $_POST['customerID'] : $quote[0]["CustomerID"];
    if ($customerID) {
        $result = $legacy->query("SELECT * FROM customers where id = $customerID");
        $cust = $result->fetchAll(PDO::FETCH_ASSOC);  
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

    // Legacy DB values
    $email = isset($_POST['Email']) ? $_POST['Email'] : $quote[0]['Email'];
    $CustomerName = isset($cust[0]['name']) ? $cust[0]['name'] : "Invalid customer selected";
    $city = isset($cust[0]['city']) ? $cust[0]['city'] : "No city found";
    $street = isset($cust[0]['street']) ? $cust[0]['street'] : "No street found";
    $contact = isset($cust[0]['contact']) ? $cust[0]['contact'] : "No contact found";
    

    //FORM FUNCTIONS

    if ($formAction == "email") {
        $prepared = $pdo->prepare("UPDATE Quotes SET Email=? WHERE QuoteID = $quoteID");
        $prepared->execute([$email]);
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

        <div id=\"address\">$city<br>$street<br>$contact<br></div>
    html;

    // Email
    echo "<form id=\"email\" action=\"\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"quoteID\" value=\"$quoteID\">";
        echo "<input type=\"hidden\" name=\"formAction\" value=\"email\">";
        echo "Email: ";
        echo "<input type=\"text\" name=\"Email\" value=\"$email\"";
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
        $ServiceDesc = $row["ServiceDesc"];
        $Cost = $row["Cost"];
        echo "<form action='' method='POST'>";
        echo "<input type=\"text\" value=\"$ServiceDesc\"]>";
        echo "<input type=\"text\" value=\"$Cost\"]><br>";
        echo "<input type='submit' name='editline'><br>";
        echo "</form>";
        // echo "<input type=\"text\" value=\"$ServiceDesc\"] disabled=\"disabled\"><br>";
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"Cost\"]\" disabled=\"disabled\">";


  
    }

    if ($action != "process") {
        echo <<< html
            EDITING/SAVE DELETING BUTTONS
            <form action="" method="POST">
                <input type="hidden" name="formAction" value="addLine">
                <input type="text" name="ServiceDesc" placeholder="Service Description">
                <input type="text" name="Cost" placeholder="Service Cost">
                <button type="submit">Add Line Item</button>
            </form>
        html;
    }

    // Secret Notes
    echo "<h1>Secret Notes:</h1>";
    echo "query quote table";
    if ($action != "process") {
        echo <<< html
            EDITING/SAVE DELETING BUTTONS
            <form action="" method="POST">
                <input type="hidden" name="formAction" value="addNote">
                <input type="text" name="Note" placeholder="Note">
                <button type="submit">Add Secret Note</button>
            </form>
        html;
    }

    $result = $pdo->query("SELECT * FROM Notes where QuoteID = $quoteID");
    $secretNotes = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach ($secretNotes as $row) {
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"ServiceDesc\"]\" disabled=\"disabled\">";
        $Note = $row["Note"];
        echo "<input type=\"text\" value=\"$Note\"]>";
        // echo "<input type=\"text\" value=\"$ServiceDesc\"] disabled=\"disabled\"><br>";
        // echo "<input type=\"text\" name=\"\" value=\"$row[\"Cost\"]\" disabled=\"disabled\">";
    }

    // Discount
    echo <<< html
        <form id="discount" action="" method="POST">
            <input type="hidden" name="formAction" value="discount">
            Discount: 
            <input type="text" name="discount" placeholder="%">
            <button type="submit">Apply</button>
            <input type="radio" name="type" value="percent">percent
            <input type="radio" name="type" value="amount">amount
            <br>Amount: \$query table line item
        </form>
    html;

    // Create/Update Button
    echo "<form id=\"update\" action=\"\" method=\"POST\">";
    echo "<input type=\"hidden\" name=\"formAction\" value=\"update\">";
        if($new)
            echo "<button type=\"submit\">Create</button>";
        else
            echo "<button type=\"submit\">Update</button>";
    echo "</form>";

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