<?php  
session_start(['name' => 'quotes']); 
?>
<!DOCTYPE html>
<html>
  <head>
<?php 
  include 'header.php'; 
  
  switch($_SESSION['userType']){
    case 'Sales Associate':
      $querytype="open";
      $buttonText = "Edit Quote";
      $headermsg =  "Create new quote for customer"; 
      echo "<br> Query type is: $querytype<br>";
      break;

      case 'Headquarters':
      $querytype="finalized";
      $buttonText = "Sanction Quote";
      $headermsg =  "Sanction finalized quotes"; 
      if(isset($_POST['menuType']) && $_POST['menuType'] === 'Sanctioned Quotes'){
        $buttonText = "Order Quote";
        $querytype="sanctioned";
        $headermsg =  "Order sanctioned quotes";
        echo "<br>Query type is: $querytype<br>";
        break;
      }
      if(isset($_POST['menuType']) && $_POST['menuType'] === 'Ordered Quotes'){
        $buttonText = "Review Quote";
        $querytype="ordered";
        $headermsg =  "Review quotes submitted for purchase";
        echo "<br>Query type is: $querytype<br>";
        break;
      }
      echo "<br> Query type is: $querytype<br>";
      break;
      // probably shoulda used if statements or a different variable to deal with superusers priveleges 
      case 'Superuser':
        if ($_POST['menuType'] == 'Open Quotes'){
          $querytype="open";
          $buttonText = "Edit Quote";
          $headermsg =  "Create new quote for customer"; 
        }
        else if ($_POST['menuType'] == 'Finalized Quotes'){
          $querytype="finalized";
          $buttonText = "Sanction Quote";
          $headermsg =  "Sanction finalized quotes"; 
        }
        else if ($_POST['menuType'] == 'Sanctioned Quotes'){
          $buttonText = "Order Quote";
          $querytype="sanctioned";
          $headermsg =  "Order sanctioned quotes";
        }
        else if ($_POST['menuType'] == 'Ordered Quotes'){
          $buttonText = "Review Quote";
          $querytype="ordered";
          $headermsg =  "Review quotes submitted for purchase";
        }
        echo "<br> Query type is: $querytype<br>";
        break;
      default:
      echo "You do not have permission to view this page. Please login as the appropriate user.";
       
     echo <<<HTML
    <form action='login.php' method='POST'> <input type='submit' name='fail' value='Return to login'></form>
    HTML;
      exit();

  }
  
  if(isset($_SESSION['userType']) && $_SESSION['userType'] === 'Sales Associate') 
  {echo "<h2></h2>"; }
  
  ?>
  <h2> <?php echo "$headermsg"; ?> </h2> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Select Customer</title>
    <meta name="description" content="description"/>
    <meta name="author" content="author" />
    <meta name="keywords" content="keywords" />
    <link rel="stylesheet" href="./stylesheet.css" type="text/css" />
    <style type="text/css">.body { width: auto; }</style>
    
<?php
include '../lib/func.php';
include '../lib/db.php';

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

   echo "<pre> 'GET'";  
   print_r($_GET);  
   echo "<br>";  
   echo "</pre> <br>";

//connect to our db and ege db
$pdo = connectlegacy();
$mydb = connectdb();
?>

  </head>
  <body>
  
<?php 
  

  //if an associate is logged in link
  if(isset($_SESSION['userType']) && $_SESSION['userType'] == 'Sales Associate' || $_SESSION['userType'] == 'Superuser' ) 
  { 

    $sql = "SELECT id, name FROM customers";
    
    $result = $pdo->query($sql);
    $row = $result->fetchAll(PDO::FETCH_ASSOC);

    echo"<form action='quoteTemplate.php' method='POST'>";
    echo "<label for='id'>First, select a customer:</label><br>";
    echo "<select id='id' name='id' required>";
    echo "<option value='' disabled selected>Choose one</option>";

    $custCount = 0;
        // Iterating through the array of customers
        foreach($row as $customer => $index){
          $custCount++;
          echo "<option value={$index['id']}>{$index['name']}</option>";
      }

    echo "</select> ";  
    echo "<b>$custCount total customers</b>";

    echo "<br>";
    echo "<label for='email'>Input the customer's email to begin quote:</label><br>  ";
    echo "<input type='email' name='email' required>"; 
    echo "<input type='submit' name='newquote' value='Create New Quote'> <p>This will direct to a new page</p>";
    echo "</form>";
  }
  
?>

<?php 
    echo "<h3>List of $querytype quotes:</h3>";
    $db = connectdb();
    $dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.OrderTotal FROM Quotes WHERE OrderStatus = '$querytype';";

    /* enable this feature if we want to only show quotes for logged in associate
    if($_SESSION['userType'] === 'Sales Associate')
    {
      $userID = $_SESSION['userID'];
      $dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.OrderTotal FROM Quotes WHERE OrderStatus = '$querytype' AND Quotes.EmployeeID = '$userID';";
    } */

    $dresult = $db->query($dbsql);
    $dbrow = $dresult->fetchAll(PDO::FETCH_ASSOC);

    // echo "<pre>"; echo "rows queried"; echo "<br>"; print_r($dbrow);    echo "</pre>";

        echo "<table border='1'>
        <tr>
        <th>QuoteID</th>
        <th>Name</th>
        <th>Order Total</th>
        </tr>";
    
        $quoteCount = 0;
        foreach($dbrow as $row){
          $quoteCount++;
        echo "<tr>";
        echo "<td> {$row['QuoteID'] } </td>" ; 
        echo "<td> {$row['CustomerName'] } </td>" ; 
        echo "<td> {$row['OrderTotal'] } </td>" ; 
        echo "<td><form action=\"quoteTemplate.php\" method=\"POST\">";
             echo "<input type=\"hidden\" name=\"quoteID\" value=\"{$row['QuoteID']}\"/>";
             echo "<button type=\"submit\">$buttonText</button> ";
         echo "</form></td>";

        echo "</tr>";
    }
    echo "</table>";
    echo "<b>$quoteCount quotes found</b>";
    ?>
  </body>
</html>