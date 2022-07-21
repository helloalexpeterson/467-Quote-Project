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
    
    <form action="newquote.php" method="POST">
    <label for="id">Select customer:</label><br>
      <select id="id" name="id">
        <option value="selected">Choose one</option>
        <?php
      
        // Iterating through the array of customers
        foreach($row as $customer => $index){
          echo "<option value={$index['id']}>{$index['name']}</option>";
      }
        
        ?>
    </select>
    <input type="submit" value="Create New Quote"> <p>This will direct to a new page</p>

    <h3>List of open quotes:</h3>
    <?php 
    $db = connectdb();
    $dbsql = "SELECT CustomerID,   , name FROM customers";
    $dbresult = 

    
    ?>
  </body>
</html>