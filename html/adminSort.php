<?php  
session_start(['name' => 'quotes']); 
?>
<!DOCTYPE html>
<html>
<?php 
  include 'header.php'; 

  if($_SESSION['userType'] !== 'Administrator'){
   
    echo "You do not have permission to view this page. Please login as the appropriate user.";
    echo <<<HTML
    <form action='login.php' method='POST'> <input type='submit' name='fail' value='Return to login'></form>
    HTML;
    exit();

  }

  ?>
  <h2> <?php echo "Administrative Quote Management"; ?> </h2> 
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

echo "<h3>List of all quotes:</h3>";
$month = date('m');
$day = date('d');
$year = date('Y');
$today = $year . '-' . $month . '-' . $day;
echo $today;

$db = connectdb();
if(isset($_GET['submitdate'])){

  $dbsql = "SELECT * FROM Quotes WHERE StartDate BETWEEN ? AND ? ORDER BY StartDate;";
  $statement = $db->prepare($dbsql);
  $dbresult = $statement->execute([$_GET['start'],$_GET['end']]);            
  $dbrow = $statement->fetchAll(PDO::FETCH_ASSOC);
} 
else if(isset($_GET['emp']) && ($_GET['emp']) > 0){  

  $dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.StartDate, Quotes.OrderTotal, Quotes.OrderStatus, Employees.EmpName 
  FROM Quotes JOIN Employees ON Quotes.EmployeeID = Employees.EmployeeID  WHERE Quotes.EmployeeID = ?;";

  $statement = $db->prepare($dbsql);
  $dbresult = $statement->execute([$_GET['emp']]);            
  $dbrow = $statement->fetchAll(PDO::FETCH_ASSOC);
}
else if(isset($_GET['customer'])){

  $dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.StartDate, Quotes.OrderTotal, Quotes.OrderStatus, Employees.EmpName 
  FROM Quotes JOIN Employees ON Quotes.EmployeeID = Employees.EmployeeID  WHERE Quotes.CustomerID = ?;";

$statement = $db->prepare($dbsql);
$dbresult = $statement->execute([$_GET['customer']]);            
$dbrow = $statement->fetchAll(PDO::FETCH_ASSOC);

}
else if(isset($_GET['status'])){

  $dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.StartDate, Quotes.OrderTotal, Quotes.OrderStatus, Employees.EmpName 
  FROM Quotes JOIN Employees ON Quotes.EmployeeID = Employees.EmployeeID  WHERE Quotes.OrderStatus = ?;";

$statement = $db->prepare($dbsql);
$dbresult = $statement->execute([$_GET['status']]);            
$dbrow = $statement->fetchAll(PDO::FETCH_ASSOC);
}
else{ 
  $dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.StartDate, Quotes.OrderTotal, Quotes.OrderStatus, Employees.EmpName 
  FROM Quotes JOIN Employees ON Quotes.EmployeeID = Employees.EmployeeID";
  $statement = $db->prepare($dbsql);
  $dbresult = $statement->execute();            
  $dbrow = $statement->fetchAll(PDO::FETCH_ASSOC);  
}

//Date sort form
echo <<<HTML
<form method=GET action="adminSort.php">
<label for="start">Start date:</label>
<input type="date" id="start" name="start"
       value=""
       min="2000-01-01" max="2025-12-31">

<label for="end">End date:</label>
<input type="date" id="end" name="end"
       value=""
       min="2000-01-01" max="2025-12-31">
<input type="submit" id="submitdate" name="submitdate" value="Sort by date range">
</form>
HTML;

//Employee Sort Form
$sql = "SELECT EmpName, EmployeeID FROM Employees";
$result = $db->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
echo"<form action='adminSort.php' method='GET'>";
echo "<select id='emp' name='emp'>";
echo "<option value='' selected>Choose one</option>";

    // Iterating through the array of employees
    foreach($row as $emp => $v){
      echo "<option value={$v['EmployeeID']}>{$v['EmployeeID']} - {$v['EmpName']}</option>";
  }
echo "</select> ";  
echo "<button type='submit'>Filter by Employee</button>";  
echo "</form>";

//Customer Sort Form
$sql = "SELECT DISTINCT CustomerName, CustomerID FROM Quotes";
$result = $db->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);
echo"<form action='adminSort.php' method='GET'>";
echo "<select id='customer' name='customer'>";
echo "<option value='' selected>Choose one</option>";

    // Iterating through the array of cust
    foreach($row as $cust => $v){
      echo "<option value={$v['CustomerID']}>{$v['CustomerID']} - {$v['CustomerName']}</option>";
  }
echo "</select> ";  
echo "<button type='submit'>Filter by Customer</button>";  
echo "</form>";


//Status Sort Form
echo"<form action='adminSort.php' method='GET'>";
echo "<select id='status' name='status'>";
echo "<option value='' selected>Choose one</option>";

      echo "<option value=open>Open</option>";
      echo "<option value=finalized>Finalized</option>";
      echo "<option value=sanctioned>Sanctioned</option>";
      echo "<option value=ordered>Ordered</option>";

echo "</select> ";  
echo "<button type='submit'>Filter by Status</button>";  
echo "</form>";



//Display quotes
if($statement->rowCount() > 0){
    echo "<table border='1'>
    <tr>
    <th>QuoteID</th>
    <th>Date</th>
    <th>Name</th>
    <th>Order Total</th>
    <th>Order Status</th>
    <th>Employee</th>
    </tr>";}

    $quoteCount = 0;
    foreach($dbrow as $row){
        $quoteCount++;
    echo "<tr>";
    echo "<td> {$row['QuoteID'] } </td>" ; 
    echo "<td> {$row['StartDate'] } </td>" ; 
    echo "<td> {$row['CustomerName'] } </td>" ; 
    echo "<td> {$row['OrderTotal'] } </td>" ; 
    echo "<td> {$row['OrderStatus'] } </td>" ; 
    echo "<td> {$row['EmpName'] } </td>" ; 
    echo "<td><a href=\"quoteTemplate.php?quoteID={$row['QuoteID']}\" class='btn btn-primary'> Review Quote</a></td> ";
    echo "</tr>";
}
echo "</table>";
echo "<b>$quoteCount quotes found</b>";
?>
  </body>
</html>
