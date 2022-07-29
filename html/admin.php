<?php  
session_start(['name' => 'quotes']); 
?>
<!DOCTYPE html>
<html>
<head>
<?php 
include 'header.php';
include '../lib/func.php';
include '../lib/db.php';
?>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Administration</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php 
    $pdo = connectdb();
    $sql = "SELECT EmployeeID, Email, EmpName, Title, CommissionTotal FROM Employees";
    $result = $pdo->query($sql);
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    ?>
        <div class="adminActions">
            <div class="associatePreview">
                <h2>Sales Associates</h2>
              
                <div class="individualAssoc">
                <table border='1' id='assocTable'>
                    <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Title</th>
                    <th>Commission total</th>
                    </tr>
                    
                    <?php foreach($rows as $employee ){
                        //print a table of employees and buttons to edit and delete
                        echo "<tr>";
                        echo "<form method='POST' action='adminAddEmp.php'>";
                        echo "<td> {$employee['EmployeeID'] } </td>" ; 
                        echo "<input type='hidden' name='empID' value='{$employee['EmployeeID'] }'>";
                        echo "<td> {$employee['EmpName'] } </td>" ; 
                        echo "<td> {$employee['Email'] } </td>" ; 
                        echo "<td> {$employee['Title'] } </td>" ;
                        echo "<td> {$employee['CommissionTotal'] } </td>" ;
                        echo "<td><button type='submit' name='editAssociate' value='editAssociate' id='editAssociate'>Edit</button></td>";
                        echo "<td><button type='submit' name='deleteAssociate' id='deleteAssociate'>Delete</button></td>";
                        echo "</form>";
                        echo "</tr>";
                    } 
                    ?>
                 </table>
                </div>
                <br>
                <button id='idBtn' onclick="sortTable('number', 0, 'assocTable')">Sort By Employee ID</button>
                <button id='fNameBtnn' onclick="sortTable('string', 1, 'assocTable')">Sort By First Name</button>
                <button id='lnameBtn' onclick="sortTable('lname', 1, 'assocTable')">Sort By Last Name</button>
                <button id='emailBtn' onclick="sortTable('string', 2, 'assocTable')">Sort By Email</button>
                <button id='titleBtn' onclick="sortTable('string', 3, 'assocTable')">Sort By Title</button>
                <button id='commissionBtn' onclick="sortTable('number', 4, 'assocTable')">Sort By Commission</button>

                <script src="tablesort.js"></script>
              
            </div><br>
            <!--Print a form to add a new associate -->        
            <div class="addAssociate">
                <h3>Add a new user:</h3>
                <form method="POST" action="adminAddEmp.php">
                    <label for="empName">Name:</label>
                    <input type="text" id="empName" name="empName" placeholder="Associate name"><br>
                    <label for="pwd">Password:</label>
                    <input type="password" id="pwd" name="pwd" placeholder="Associate password"><br>
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="Associate E-mail"><br><br>
                  
                    <label for="title">Title:</label>
                    <select id="title" name="title">
                        <option value="Sales Associate">Sales Associate</option>
                        <option value="Headquarters">Headquarters</option>
                        <option value="Administrator">Administrator</option>
                    </select><br><br>
                    <input type="submit" name="submit" value="Add new user">
                </form>
            </div>
        </div>
    </div>

</body>

</html>
