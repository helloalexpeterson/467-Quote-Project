<?php  
session_start(['name' => 'quotes']); 

  
?>
<!DOCTYPE html>
<html>
  <head>
  <?php include 'header.php'; 
  
  switch($_SESSION['userType']){
    case 'Sales Associate':
      $querytype="open";
      $buttonText = "Edit Quote";
      echo "<br> Query type is: $querytype<br>";
      break;

      case 'Headquarters':
      $querytype="finalized";
      $buttonText = "Sanction Quote";
      if(isset($_POST['menuType']) && $_POST['menuType'] === 'Sanctioned Quotes'){
        $buttonText = "Order Quote";
        $querytype="sanctioned";
        echo "<br>Query type is: $querytype<br>";
        break;
      }
      if(isset($_POST['menuType']) && $_POST['menuType'] === 'Ordered Quotes'){
        $buttonText = "Review Quote";
        $querytype="ordered";
        echo "<br>Query type is: $querytype<br>";
        break;
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
  
  
  ?>
  <h2> Create new quote for customer </h2> 
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

  //if an associate clicks the link
  if(isset($_SESSION['userType']) && $_SESSION['userType'] === 'Sales Associate') 
  { 

    $sql = "SELECT id, name FROM customers";
    
    $result = $pdo->query($sql);
    $row = $result->fetchAll(PDO::FETCH_ASSOC);

    echo"<form action='quoteTemplate.php' method='POST'>";
    echo "<label for='id'>First, select a customer:</label><br>";
    echo "<select id='id' name='id'>";
      echo "<option value='selected'>Choose one</option>";

        // Iterating through the array of customers
        foreach($row as $customer => $index){
          echo "<option value={$index['id']}>{$index['name']}</option>";
      }

    echo "</select> ";  

    echo "<br>";
    echo "<label for='email'>Input the customer's email to begin quote:</label><br>  ";
    echo "<input type='text' name='email'>"; 
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
    
        foreach($dbrow as $row){
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
    ?>
  </body>
</html>