<?php  
session_start(['name' => 'quotes']); 
?>
<!DOCTYPE html>
<html>
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
      if(isset($_GET['type']) && $_GET['type'] === 'sanctioned'){
        $buttonText = "Order Quote";
        $querytype="sanctioned";
        $headermsg =  "Order sanctioned quotes";
        echo "<br>Query type is: $querytype<br>";
        break;
      }
      if(isset($_GET['type']) && $_GET['type'] === 'ordered'){
        $buttonText = "Review Quote";
        $querytype="ordered";
        $headermsg =  "Review quotes submitted for purchase";
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

    echo "<pre> 'GET'";  
    print_r($_GET);  
    echo "<br>";  
    echo "</pre> <br>";
}

echo "<h3>List of {$_GET['type']} quotes:</h3>";
$db = connectdb();
$dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.OrderTotal FROM Quotes WHERE OrderStatus = ?;";
$statement = $db->prepare($dbsql);
$dbresult = $statement->execute([$_GET['type']]);            
$dbrow = $statement->fetchAll(PDO::FETCH_ASSOC);
if($statement->rowCount() > 0){
    echo "<table border='1'>
    <tr>
    <th>QuoteID</th>
    <th>Name</th>
    <th>Order Total</th>
    </tr>";}

    $quoteCount = 0;
    foreach($dbrow as $row){
        $quoteCount++;
    echo "<tr>";
    echo "<td> {$row['QuoteID'] } </td>" ; 
    echo "<td> {$row['CustomerName'] } </td>" ; 
    echo "<td> {$row['OrderTotal'] } </td>" ; 
    echo "<td><a href=\"quoteTemplate.php?quoteID={$row['QuoteID']}\" class='btn btn-primary'> $buttonText</a></td> ";
    echo "</tr>";
}
echo "</table>";
echo "<b>$quoteCount quotes found</b>";
?>
  </body>
</html>
