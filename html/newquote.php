<!DOCTYPE html>
<html>
  <head> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Select Customer</title>
    <meta name="description" content="description"/>
    <meta name="author" content="author" />
    <meta name="keywords" content="keywords" />
    <link rel="stylesheet" href="./stylesheet.css" type="text/css" />
    <style type="text/css">.body { width: auto; }</style>
<?php
    include '../lib/db.php';

   $pdo = connectdb();
   
   $legacy = connectlegacy(); echo "<br>";
   $id = $_POST["id"]; echo "<br>";
  
   $sql = "SELECT * FROM customers where id = $id";
   $result = $legacy->query($sql);
   $row = $result->fetchAll(PDO::FETCH_ASSOC);   

   //debug print
   echo "<pre>";  print_r($_POST);  echo "<br>";  echo "</pre>";
   echo "<pre>";  print_r($row);    echo "</pre>";

   echo "<h2> New Quote For: {$row[0]['name']} </h2>";
   foreach($row[0] as $k => $v){
    if($k !== "id" && $k !== "name"){
        echo $v;
        echo "<br>";
        }
   }

   

   
   

?>