<?php
    $view="admin";
    $querytype="ordered";
    //include "sales.php";
?>




<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Administration</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="wrapper">
        <div class="nav">
            <table>
                <tr>
                    <th>Plant Repair:</th>
                    <th>Admin</th>
                    <th><a href="" id="associatesLink">Associates</a></th>
                    <th><a href="" id="quotesLink">Quotes</a></th>
                </tr>
            </table>
        </div>

        <div class="adminActions">
            <div class="associatePreview">
                <h2>Sales Associates</h2>
                <hr>
                <div class="individualAssoc">
                    <p id="associate">ID - Name - commission</p>
                    <button type="text" id="editAssociate">Edit</button>
                    <button type="text" id="deleteAssociate">Delete</button>
                </div>
                <hr>
            </div><br>

            <div class="addAssociate">
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
                    <input type="submit" name="submit" value="Add new associate">
                </form>
            </div>
        </div>
    </div>

</body>

</html>
