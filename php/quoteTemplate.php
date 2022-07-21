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
    $subAction = isset($_POST['subAction']) ? $_POST['subAction'] : '';
    $new = isset($_POST['new']) ? $_POST['new'] : '';

    // Legacy DB values
    $email = "From DB Table";
    $CustomerName = isset($_POST['CustomerName']) ? $_POST['CustomerName'] : "Query DB for customer name";
    $city = "City from Legacy";
    $street = "Street from Legacy";
    $contact = "Contacy from Legacy";
    
    //Name and Address
    echo <<< html
        // DEBUG
        <h2>SAVE \$action TO php session IF POSSIBLE, ELSE NEED TO DISCUSS</h2>
        The page's action is set to 
        $action
        <br>The page's subAction is set to 
        $subAction
        // DEBUG
    
    
        <h2>$CustomerName</h2>

        <div id=\"address\">$city<br>$street<br>$contact<br></div>
    html;

    // Email
    echo "<form id=\"email\" action=\"\" method=\"POST\">";
        echo "<input type=\"hidden\" name=\"subAction\" value=\"email\">";
        echo "Email: ";
        echo "<input type=\"text\" name=\"email\" value=\"$email\"";
        if ($action != "create") 
            echo "disabled=\"disabled\">";
        else
            echo "><button type=\"submit\">Save email</button>";
    echo "</form>";

    // Line Items
    echo "<h1>Line Items:</h1>";
    echo "query quote table";
    if ($action != "process") {
        echo <<< html
            EDITING/SAVE DELETING BUTTONS
            <form action="" method="POST">
                <input type="hidden" name="subAction" value="addLine">
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
                <input type="hidden" name="subAction" value="addNote">
                <input type="text" name="Note" placeholder="Note">
                <button type="submit">Add Secret Note</button>
            </form>
        html;
    }

    // Discount
    echo <<< html
        <form id="discount" action="" method="POST">
            <input type="hidden" name="subAction" value="discount">
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
    echo "<input type=\"hidden\" name=\"subAction\" value=\"update\">";
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
            echo "<input type=\"hidden\" name=\"subAction\" value=\"foo\">";
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
            echo "<input type=\"hidden\" name=\"subAction\" value=\"foo\">";
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
            echo "<input type=\"hidden\" name=\"subAction\" value=\"foo\">";
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
            echo "<input type=\"hidden\" name=\"subAction\" value=\"foo\">";
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