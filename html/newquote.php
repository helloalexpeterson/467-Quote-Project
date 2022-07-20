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
   echo "ignore this - debug info";
   echo "<pre>";  print_r($_POST);  echo "<br>";  echo "</pre>";
   echo "<pre>";  print_r($row);    echo "</pre>";

   //Print header
   echo "<h2> New Quote For: {$row[0]['name']} </h2>";

   //Print customer details
   foreach($row[0] as $k => $v){
    if($k !== "id" && $k !== "name"){
        echo $v;
        echo "<br>";
        }
   }
?>
<br>
<form>  
    <label for ="email">Email:</label>  
    <input type="text" id="email" name="email"><br>
</form>

<p> Quote line items: </p><br>
<br>
<p>Secret notes: </p><br>
<br>

<form>  
    <label for ="discount">Discount: </label>  
    <input type="text" id="discount" name="discount"><br> 
    <input type="radio" id="percent" name="discounttype" value="percent">
        <label for="percent">Percent off order</label><br>
    <input type="radio" id="amount" name="discounttype" value="amount">
        <label for="amount">Amount off order</label><br>
</form>