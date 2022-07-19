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
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = "SELECT * FROM customers";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        echo " " . $row["id"]. "&nbsp" . $row["name"]."<br>";
      }
    } else {
      echo "0 results";
    }
    $conn->close();
    ?>

  </head>
  <body>
    
    <form action=" ">
    <label for="fname">Select customer:</label><br>

    <form action=" ">
      <label for="customer"></label>
      <select>
        <option selected="selected">Choose one</option>
        <?php
        // A sample product array
        $products = array("Mobile", "Laptop", "Tablet", "Camera");
        
        // Iterating through the product array
        while($row = $result->fetch_assoc()){
          foreach($row["name"] as $name){
            echo "<option value=$item>$item</option>";
          }
        }
        
        ?>
    </select>
    <input type="submit" value="Submit">
</form>


    <input type="text" id="fname" name="fname" value=""><br>
    <label for="lname">Password:</label><br>
    <input type="text" id="lname" name="lname" value=""><br><br>
    <input type="submit" value="Submit">
    </form> 
    
  </body>
</html>