<?php  
session_start(['name' => 'quotes']); 
if(!(isset($_SESSION['userType']))){
  header("Location: login.php", 303);
}
?>
<!DOCTYPE html>
<html>
<?php 
include '../lib/func.php';
include '../lib/db.php';
$pagetitle = "View Quotes";
include 'header.php'; 

  switch($_SESSION['userType']){
    case 'Sales Associate':
      $querytype="open";
      $buttonText = "Edit Quote";
      $headermsg =  "View Open Quotes"; 
      break;

      case 'Headquarters':
      $querytype="finalized";
      $buttonText = "Sanction Quote";
      $headermsg =  "Sanction finalized quotes"; 
      if(isset($_GET['type']) && $_GET['type'] === 'sanctioned'){
        $buttonText = "Order Quote";
        $querytype="sanctioned";
        $headermsg =  "Order sanctioned quotes";
        break;
      }
      if(isset($_GET['type']) && $_GET['type'] === 'ordered'){
        $buttonText = "Review Quote";
        $querytype="ordered";
        $headermsg =  "Review quotes submitted for purchase";
        break;
      }
      break;
          // probably shoulda used if statements or a different variable to deal with superusers priveleges 
    case 'Superuser':
      $querytype="";
      if (isset($_GET['type']) && $_GET['type'] == 'Open Quotes'){
        $querytype="open";
        $buttonText = "Edit Quote";
        $headermsg =  "Create new quote for customer"; 
      }
      else if (isset($_GET['type']) && $_GET['type'] == 'sanctioned'){
        $querytype="finalized";
        $buttonText = "Sanction Quote";
        $headermsg =  "Sanction finalized quotes"; 
      }
      else if (isset($_GET['type']) && $_GET['type'] == 'Sanctioned Quotes'){
        $buttonText = "Order Quote";
        $querytype="sanctioned";
        $headermsg =  "Order sanctioned quotes";
      }
      else if (isset($_GET['type']) && $_GET['type'] == 'ordered'){
        $buttonText = "Review Quote";
        $querytype="ordered";
        $headermsg =  "Review quotes submitted for purchase";
      }
      else {
        $querytype="open";
        $buttonText = "Edit Quote";
        $headermsg =  "Create new quote for customer"; 
      }
      break;

      default:
      echo "You do not have permission to view this page. Please login as the appropriate user.";
       
     echo <<<HTML
    <form action='login.php' method='POST'> <input type='submit' name='fail' value='Return to login'></form>
    HTML;
      exit();

  }
  ?>

  <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-3">
                    <div class="card-header">    
                        <h4 class="mb-3"><?php echo "$headermsg"; ?></h4>
                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                        <button class="btn btn-secondary btn-sm m-1" id='idBtn' onclick="sortTable('number', 0, 'quoteTable')">Sort By ID</button>
                        <button class="btn btn-secondary btn-sm m-1" id='custBtn' onclick="sortTable('string', 1, 'quoteTable')">Sort Alphabetically</button>
                        <button class="btn btn-secondary btn-sm m-1" id='costBtn' onclick="sortTable('number', 2, 'quoteTable')">Sort By Order Total</button>
                        <button class="btn btn-secondary btn-sm m-1" id='dateBtn' onclick="sortTable('string', 3, 'quoteTable')">Sort By Date Opened</button>
                        <script src="tablesort.js"></script>
                        </div>
                    </div>

<?php
//echo "<h5>List of {$_GET['type']} quotes:</h5>";
$db = connectdb();
$dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.OrderTotal, Quotes.StartDate FROM Quotes WHERE OrderStatus = ?;";
$statement = $db->prepare($dbsql);
$dbresult = $statement->execute([$_GET['type']]);            
$dbrow = $statement->fetchAll(PDO::FETCH_ASSOC);
if($statement->rowCount() > 0){
    echo "<table class='table table-striped' border='1' id='quoteTable'>
    <thead>
      <tr>
        <th scope='col'>QuoteID</th>
        <th scope='col'>Name</th>
        <th scope='col'>Order Total</th>
        <th scope='col'>Date Opened</th>
        <th scope='col'></th>
      </tr>
    </thead>"
    ;}
    echo "<tbody>";    
    $quoteCount = 0;
    foreach($dbrow as $row){
        $quoteCount++;
      echo "<tr>";
        echo "<td> {$row['QuoteID'] } </td>" ; 
        echo "<td> {$row['CustomerName'] } </td>" ; 
        echo "<td> {$row['OrderTotal'] } </td>" ; 
        echo "<td> {$row['StartDate'] } </td>";
        echo "<td><a href=\"quoteTemplate.php?quoteID={$row['QuoteID']}\" class='btn btn-primary'> $buttonText</a></td> ";
      echo "</tr>";
}
echo "<tbody>";    
echo "</table>";
echo "<b>$quoteCount quotes found</b>";

?>
                </div>
            </div>
         </div>
   </div>
  </body>
</html>
