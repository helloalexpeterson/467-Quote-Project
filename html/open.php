<?php
    $view="associate";
    $querytype="open";
    $buttontext = "edit quote";
    $pagename ="open.php";
?>

<!DOCTYPE html>
<html>
  <head>
  <h2> Create new quote for customer </h2> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Select Customer</title>
    <meta name="description" content="description"/>
    <meta name="author" content="author" />
    <meta name="keywords" content="keywords" />
    <link rel="stylesheet" href="./stylesheet.css" type="text/css" />
    <style type="text/css">.body { width: auto; }</style>
    
<?php
    //include '../lib/db.php';
    include '../lib/func.php';
   //debug print
   echo "ignore this - debug info"; 

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
  if($view=="associate") 
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
  //if an admin clicks the link

if($view=="admin") 
  { 
   echo "This would be the master quote lookup";
  }

  ?>
    <?php echo "<h3>List of $querytype quotes:</h3>"?>
    <?php 
    $db = connectdb();
    $dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.OrderTotal FROM Quotes WHERE OrderStatus = '$querytype';";
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
             echo "<button type=\"submit\">Edit Quote</button> ";
         echo "</form></td>";

        echo "</tr>";


    }

    
    ?>
  </body>
</html>