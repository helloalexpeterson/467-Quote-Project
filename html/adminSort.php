<?php  
//This page shows all quotes for the admin to view
$pagetitle = "Administration";
session_start(['name' => 'quotes']); 
include 'header.php'; 
include '../lib/func.php';
include '../lib/db.php';
?>
<!DOCTYPE html>
<html>
<?php 
  if($_SESSION['userType'] !== 'Administrator'){
   
    echo "You do not have permission to view this page. Please login as the appropriate user.";
    echo <<<HTML
    <form action='login.php' method='POST'> <input type='submit' name='fail' value='Return to login'></form>
    HTML;
    exit();
  }

$month = date('m');
$day = date('d');
$year = date('Y');
$today = $year . '-' . $month . '-' . $day;

$db = connectdb();
if(isset($_GET['submitdate'])){
  $dbsql = "SELECT Quotes.QuoteID, Quotes.CustomerName, Quotes.StartDate, Quotes.OrderTotal, Quotes.OrderStatus, Employees.EmpName 
  FROM Quotes JOIN Employees ON Quotes.EmployeeID = Employees.EmployeeID  WHERE StartDate BETWEEN ? AND ? ORDER BY StartDate;";
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
<div class="container">
            <div class="container row justify-content-center ">
                <div class="col">
                    <h4 class="mb-3">Administrative Quote Management</h4>

<form class="row" method=GET action="adminSort.php">
<div class="col">
<label for="start">Start date:</label>
<input type="date" id="start" name="start"
       value=""
       min="2000-01-01" max="2025-12-31" required>
<label for="end">End date:</label>
<input type="date" id="end" name="end"
       value=""
       min="2000-01-01" max="2025-12-31" required>
<input class="btn btn-primary btn-sm w-10" type="submit" id="submitdate" name="submitdate" value="Sort by date range">
</div>
</form>
</div>
HTML;

//Employee Sort Form
$sql = "SELECT EmpName, EmployeeID FROM Employees";
$result = $db->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

echo "<div class='container row justify-content-center'>";
echo "<form class='col p-3' action='adminSort.php' method='GET'>";
echo "<select id='emp' name='emp'>";
echo "<option value='' selected>Filter by Employee</option>";
    // Iterating through the array of employees
    foreach($row as $emp => $v){
      echo "<option value={$v['EmployeeID']}>{$v['EmployeeID']} - {$v['EmpName']}</option>";
  }
echo "</select> ";  
echo "<input class='btn btn-primary btn-sm' type='submit'>";  
echo "</form>";

//Customer Sort Form
$sql = "SELECT DISTINCT CustomerName, CustomerID FROM Quotes";
$result = $db->query($sql);
$row = $result->fetchAll(PDO::FETCH_ASSOC);

echo"<form class='col p-3' action='adminSort.php' method='GET'>";
echo "<select id='customer' name='customer' required>";
echo "<option value='' selected>Filter by Customer</option>";

    // Iterating through the array of cust
    foreach($row as $cust => $v){
      echo "<option value={$v['CustomerID']}>{$v['CustomerID']} - {$v['CustomerName']}</option>";
  }
echo "</select> ";  
echo "<input class='btn btn-primary btn-sm' type='submit'>";  
echo "</form>";


//Status Sort Form

echo"<form class='col p-3' action='adminSort.php' method='GET'>";
echo "<select id='status' name='status' required>";
echo "<option value='' selected>Filter by Status</option>";

      echo "<option value=open>Open</option>";
      echo "<option value=finalized>Finalized</option>";
      echo "<option value=sanctioned>Sanctioned</option>";
      echo "<option value=ordered>Ordered</option>";

echo "</select> ";  
echo "<input class='btn btn-primary btn-sm' type='submit'>";  
echo "</form>";
echo "</div>";


//Display quotes
if($statement->rowCount() > 0){
    echo "<table class='table table-striped' border='1' id='adqTable'>
    <thead>
    <tr>
    <th scope='col'><a href=\"javascript:sortTable('number', 0, 'adqTable')\"> QuoteID </a></th>
    <th scope='col'><a href=\"javascript:sortTable('string', 1, 'adqTable')\"> Date </a></th>
    <th scope='col'><a href=\"javascript:sortTable('string', 2, 'adqTable')\"> Customer Name </a></th>
    <th scope='col'><a href=\"javascript:sortTable('number', 3, 'adqTable')\"> Order Total </a></th>
    <th scope='col'><a href=\"javascript:sortTable('string', 4, 'adqTable')\"> Order Status </a></th>
    <th scope='col'><a href=\"javascript:sortTable('string', 5, 'adqTable')\"> Employee </a></th>
    </tr>
    </thead>
    </tbody>";
  }
 
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
    echo "<td><a href=\"quoteTemplate.php?quoteID={$row['QuoteID']}\" class='btn btn-primary btn-sm'> Review Quote</a></td> ";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "<b>$quoteCount quotes found</b>";
?>
    <script src="tablesort.js"></script>
  </body>
</html>
