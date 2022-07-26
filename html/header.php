<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<?php 
echo "<th> Logged in as: {$_SESSION['username']} - {$_SESSION['userType']}</th>";
echo "<form action='login.php' method='POST'> <input type='submit' name='logout' value='Logout'> </form>";

if($_SESSION['userType'] == 'Sales Associate'){
    echo<<<HTML
       <div class="wrapper">
       <div class="nav">
           <table>
               <tr>
                   <th>Plant Repair:</th>
                   <th>Associate View - Create Quote</th>
                   <th><form action='open.php' method='POST'> <input type='submit' name='menuType' value='Open Quotes'> </form></th>
                   <th><form action='' method='POST'> <input type='submit' name='' value='My Account Information'> </form></th>
               </tr>
           </table>
       </div>
       HTML;
   } 
   if($_SESSION['userType'] == 'Headquarters'){
    echo<<<HTML
       <div class="wrapper">
       <div class="nav">
           <table>
               <tr>
                   <th>Plant Repair:</th>
                   <th>HQ View - Sanction and Order Quote</th>
                   <th><form action='open.php' method='POST'> <input type='submit' name='menuType' value='Finalized Quotes'> </form></th>
                   <th><form action='open.php' method='POST'> <input type='submit' name='menuType' value='Sanctioned Quotes'> </form></th>
                   <th><form action='open.php' method='POST'> <input type='submit' name='menuType' value='Ordered Quotes'> </form></th>
               </tr>
           </table>
       </div>
       HTML;
   } 
   if($_SESSION['userType'] == 'Administrator'){
    echo<<<HTML
       <div class="wrapper">
       <div class="nav">
           <table>
               <tr>
                   <th>Plant Repair:</th>
                   <th>Admin View - Manage Associates and Quote History</th>
                   <th><form action='' method='POST'> <input type='submit' name='menuType' value='Quote Management'> </form></th>
                   <th><form action='admin.php' method='POST'> <input type='submit' name='menuType' value='Associate Management'> </form></th>
               </tr>
           </table>
       </div>
       HTML;
   } 
   if($_SESSION['userType'] == 'Superuser'){
    echo<<<HTML
       <div class="wrapper">
       <div class="nav">
           <table>
               <tr>
                   <th>Plant Repair:</th>
                   <th>Superuser View</th>
                   <th><form action='open.php' method='POST'> <input type='submit' name='menuType' value='Open Quotes'> </form></th>
                   <th><form action='' method='POST'> <input type='submit' name='' value='My Account Information'> </form></th>
                   <th><form action='open.php' method='POST'> <input type='submit' name='menuType' value='Finalized Quotes'> </form></th>
                   <th><form action='open.php' method='POST'> <input type='submit' name='menuType' value='Sanctioned Quotes'> </form></th>
                   <th><form action='' method='POST'> <input type='submit' name='' value='Quote Management'> </form></th>
                   <th><form action='admin.php' method='POST'> <input type='submit' name='menuType' value='Associate Management'> </form></th>
               </tr>
           </table>
       </div>
       HTML;
   } 

?>