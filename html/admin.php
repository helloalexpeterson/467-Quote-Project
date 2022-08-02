<?php  
session_start(['name' => 'quotes']); 
$pagetitle = "Administration";
include 'header.php';
include '../lib/func.php';
include '../lib/db.php';
$pdo = connectdb();
$sql = "SELECT EmployeeID, Email, EmpName, Title, CommissionTotal, Street FROM Employees";
$result = $pdo->query($sql);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>   
        <div class="container ">
            <div class="container row justify-content-center ">
                <div class="col-md-10">
                    <div class="card mt-3">
                        <div class="card-header">    
                            <h4 class="mb-3">Users</h4>
                <table class='table table-striped' border='1' id='assocTable'>
                <thead>
                <tr>
                <th scope='col'><a href="javascript:sortTable('number', 0, 'assocTable')"> ID </a> </th>
                <th scope='col'><a href="javascript:sortTable('string', 1, 'assocTable')"> Username </a> </th>
                <th scope='col'><a href="javascript:sortTable('string', 2, 'assocTable')"> Email </a> </th>
                <th scope='col'><a href="javascript:sortTable('string', 3, 'assocTable')"> Address </a> </th>
                <th scope='col'><a href="javascript:sortTable('string', 4, 'assocTable')"> Title </a> </th>
                <th scope='col'><a href="javascript:sortTable('number', 5, 'assocTable')"> Commission total </a> </th>
                <th scope='col'></th>
                <th scope='col'></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $employee ){
                        //print a table of employees and buttons to edit and delete
                        echo "<tr>";
                        echo "<form method='POST' action='adminAddEmp.php'>";
                        echo "<td>{$employee['EmployeeID'] }</td>" ; 
                        echo "<input type='hidden' name='empID' value='{$employee['EmployeeID'] }'>";

                        echo "<td> {$employee['EmpName'] }</td>" ; 
                        echo "<td> {$employee['Email'] }</td>" ; 
                        echo "<td> {$employee['Street'] }</td>" ; 
                        echo "<td> {$employee['Title'] }</td>" ;
                        echo "<td> {$employee['CommissionTotal'] }</td>" ;

                        echo "<td><button type='submit' class='btn btn-primary btn-sm' name='editAssociate' value='editAssociate' id='editAssociate'>Edit</button></td>";
                        echo "<td><button type='submit' class='btn btn-primary btn-sm' name='deleteAssociate' id='deleteAssociate'>Delete</button></td>";
                        echo "</form>";
                        echo "</tr>";
                    } 
                    ?>
                    </tbody>
                 </table>
                </div>
                <script src="tablesort.js"></script>
            </div><br>
            <!--Print a form to add a new associate -->        

            <div class="container row justify-content-center">
            <div class="col-md-10">
            <h4 class="mb-3">Add a new user:</h4>
            <form class="form-group" method="POST" action="adminAddEmp.php">
            <div class="form-group">
                <label for="empName">Name:</label>
                <input type="text" class="form-control" id="empName" name="empName" placeholder="Associate name" required>
            </div>
            <div class="form-group">
                <label for="pwd">Password:</label>
                <input type="password" class="form-control" id="pwd" name="pwd" placeholder="Associate password" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Associate E-mail" required>
            </div>
            <div class="form-group">
                <label for="street">Address:</label>
                <input type="text" class="form-control"   id="street" name="street" placeholder="Associate Address" required>
            </div>
            <div class="form-group mb-3">
                <label for="title">Title:</label>
                <select id="title" name="title" class="form-control" placeholder="Title">
                        <option value="Sales Associate">Sales Associate</option>
                        <option value="Headquarters">Headquarters</option>
                        <option value="Administrator">Administrator</option>
                </select>
            </div>
            </div>
            <button class="btn btn-primary w-25  mb-5" type="submit" name="submit" value="Add new user" >Add new user</button>
            </form>
                <!----my code--->
            </div>
        </div>
    </div>
</div>

</body>

</html>
