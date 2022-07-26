<!DOCTYPE html>
<?php
session_start();
include '../lib/func.php';
include '../lib/db.php';
//include '../lib/db.php';
//session code here
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
  print_r($_POST);
  if($_POST['action']=='login'){
    echo "LOGIN";
    login($_POST['user'],$_POST['pass'] );

  }

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