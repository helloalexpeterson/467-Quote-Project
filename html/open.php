<?php  
session_start(['name' => 'quotes']); 
$pagetitle = "Create a new quote";
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

// if(isset($_SESSION['userType']) && $_SESSION['userType'] === 'Sales Associate') 
//{echo "<h2></h2>"; }

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
 

//connect to our db and ege db
$pdo = connectlegacy();
$mydb = connectdb();
?>

  </head>
  <body>
  
<?php 
  
  if(isset($_SESSION['userType']) && $_SESSION['userType'] == 'Sales Associate' || isset($_POST['menuType']) && $_POST['menuType'] == 'Open Quotes' )
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
  </body>
</html>
