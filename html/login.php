<?php  
session_start(['name' => 'quotes']); 
?>
<!DOCTYPE html>
<?php
include '../lib/func.php';
include '../lib/db.php';
?>
<html>
  <head>
  <center> <h1> Login </h1></center> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <meta name="description" content="description"/>
    <meta name="author" content="author" />
    <meta name="keywords" content="keywords" />
    <link rel="stylesheet" href="./stylesheet.css" type="text/css" />
    <style type="text/css">.body { width: auto; }</style>
  </head>
  <body>
<?php 
if(isset($_POST["logout"]) && isset($_SESSION['username']))
{   echo "User: {$_SESSION['username']} logged out.<br>";
    unset($_SESSION['quotes']);
}
  //print_r($_POST);
if(isset($_POST['action'])){
  if($_POST['action']=='login'){
 
    login($_POST['user'],$_POST['pass'] );

  }}

?>
    <center> 
    <form action="login.php" method="POST">
    <label >Username:</label><br>
    <input type="text" id="user" name="user" value=""><br>
    <label >Password:</label><br>
    <input type="text" id="pass" name="pass" value=""><br><br>
    <input type="submit" value="login" name="action">
    </form> 
    </center> 


  </body>
</html>