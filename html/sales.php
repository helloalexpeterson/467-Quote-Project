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
    //Connect to mariadb function
    $servername = "blitz.cs.niu.edu";
    $username = "student";
    $password = "student";
    $dbname = "csci467";
    // Create connection
    try {
      $dsn = "mysql:dbname=$dbname;host=$servername";
      $pdo = new PDO($dsn, $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    } 
    catch(PDOException $error){  
        die('    <p> Connection to database failed: ' . $error->getMessage() . "</p>\n </body></html>"); 
    }
    
    $sql = "SELECT id, name FROM customers";
    
    $result = $pdo->query($sql);
    $row = $result->fetchAll(PDO::FETCH_ASSOC);
    ?>

  </head>
  <body>
    
    <form action="newquote.php">
    <label for="fname">Select customer:</label><br>

    <form action="newquote.php" method="POST">
      <label for="customer"></label>
      <select>
        <option selected="selected">Choose one</option>
        <?php
      
        // Iterating through the array of customers
        foreach($row as $customer => $index){
          echo "<option value={$index['id']}>{$index['name']}</option>";
      }
        
        ?>
    </select>
    <input type="submit" value="Create New Quote"> <p>This will direct to a new page</p>

  </body>
</html>