
<?php
    include '../config/secrets.php';
    //Connect to mariadb function
    function connectdb() {
        global $dbname;
        global $dbpassword;
        global $dbusername;
        try {
            $dsn = "mysql:host=courses;dbname=$dbname";
            $pdo = new PDO($dsn, $dbusername, $dbpassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        } 
        catch(PDOException $error){  
            die('    <p> Connection to database failed: ' . $error->getMessage() . "</p>\n </body></html>"); 
        }

        return $pdo;
    }
    function connectlegacy() {
    //Connect to legacydb function
    $servername = "blitz.cs.niu.edu";
    $username = "student";
    $password = "student";
    $dbname = "csci467";
    // Create connection
    try {
      $dsn = "mysql:dbname=$dbname;host=$servername";
      $leg = new PDO($dsn, $username, $password);
      $leg->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    } 
    catch(PDOException $error){  
        die('    <p> Connection to database failed: ' . $error->getMessage() . "</p>\n </body></html>"); 
        }
        return $leg;
    }
   
    ?>