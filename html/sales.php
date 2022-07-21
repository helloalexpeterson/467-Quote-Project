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
    include '../lib/db.php';
    $pdo = connectlegacy();
    $sql = "SELECT id, name FROM customers";
    
    $result = $pdo->query($sql);
    $row = $result->fetchAll(PDO::FETCH_ASSOC);
  ?>

  </head>
  <body>
  
  <?php 
  if($view=="associate") 
  { 
    echo"<form action='newquote.php' method='POST'>";
    echo "<label for='id'>Select customer:</label><br>";
    echo "<select id='id' name='id'>";
      echo "<option value='selected'>Choose one</option>";

        // Iterating through the array of customers
        foreach($row as $customer => $index){
          echo "<option value={$index['id']}>{$index['name']}</option>";
      }

    echo "</select>";  
     
    echo "<input type='submit' value='Create New Quote'> <p>This will direct to a new page</p>";
  }
  
  ?>


if($view=="admin") 
  { 
   echo "This would be the master quote lookup";
  }
  ?>


    <?php echo "<h3>List of $querytype quotes:</h3>"?>
    <?php 
    $db = connectdb();
    $dbsql = "SELECT Quotes.CustomerID, Quotes.CustomerName, Quotes.OrderTotal FROM Quotes WHERE OrderStatus = '$querytype';";
    $dresult = $db->query($dbsql);
    $dbrow = $dresult->fetchAll(PDO::FETCH_ASSOC);

   
     echo "<table border='1'>
     <tr>
     <th>ID</th>
     <th>Name</th>
     <th>Order Total</th>
     </tr>";
     echo "<td> {$dbrow[0]["CustomerID"] } </td>" ; 
     echo "<td> {$dbrow[0]["CustomerName"] } </td>" ; 
     echo "<td> {$dbrow[0]["OrderTotal"] } </td>" ; 

    
    ?>
  </body>
</html>
