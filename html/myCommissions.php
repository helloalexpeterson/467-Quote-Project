<?php
session_start(['name' => 'quotes']);
//This page displays the commissions for a given employee
include 'header.php';
include '../lib/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>My Commissions</title>
<meta charset="utf-8">
</head>
<body>

<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card mt-3">
                    <div class="card-header">    
                        <h4 class="mb-3">My commission info</h4>
<?php

    error_reporting(E_ALL);
    try {
     
        $pdo = connectdb();
        $result = $pdo->query("SELECT * FROM Employees WHERE EmployeeID = {$_SESSION['userID']}");
        $currEmp = $result->fetch(PDO::FETCH_ASSOC);

        $result = $pdo->query("SELECT * FROM PurchaseOrders WHERE EmployeeID = {$_SESSION['userID']}");
        $orders = $result->fetchAll(PDO::FETCH_ASSOC);

        echo "<table class='table table-striped' border='1'>";
        echo "<thead>";
        echo "<tr>
            <th>Customer Name</th>
            <th>Order Total</th>
            <th>Commission Rate</th>
            <th>Commission</th>
            <th>Process Date</th>
            <th>Order Time</th>
        <tr>
        </thead>
        <tbody>";    
        $commissionTotal = (float)0.00;
        foreach ($orders as $order) {
            $commission = (float)$order['OrderTotal'] * 0.01 * $order['CommissionRate'];
            $commissionTotal += $commission;
            $commission = number_format($commission,2);
            echo "<tr>
                <td>{$order['CustomerName']}</td>
                <td>\${$order['OrderTotal']}</td>
                <td>{$order['CommissionRate']}%</td>
                <td>\${$commission}</td>
                <td>{$order['ProcessDate']}</td>
                <td>{$order['OrderTime']}</td>
                </tr>";
        }
        echo "</tbody>";    
        echo "</table>";
        if ($commissionTotal == $currEmp['CommissionTotal']) { }
        else {
            $adminAdjustment = $currEmp['CommissionTotal'] - $commissionTotal;
            echo "Admin Adjustment: $" . number_format($adminAdjustment,2) . "<br>";
            $commissionTotal = $currEmp['CommissionTotal'];
        }
        echo "Total commissions: $" . number_format($commissionTotal,2);
    }
    catch(PDOexception $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>
</body>
</html>
<style>