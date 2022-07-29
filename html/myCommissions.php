<!DOCTYPE html>
<html lang="en">
<head>
<title>My Commissions</title>
<meta charset="utf-8">
<link rel="stylesheet" href="../public/css/quote.css">
</head>
<body>
<?php
    error_reporting(E_ALL);
    try {
        session_start(['name' => 'quotes']); 
        include '../lib/db.php';
        include 'header.php';
        $pdo = connectdb();
        $result = $pdo->query("SELECT * FROM PurchaseOrders WHERE EmployeeID = {$_SESSION['userID']}");
        $orders = $result->fetchAll(PDO::FETCH_ASSOC);

        echo "<table border='1'>";
        echo "<tr>
            <th>Customer Name</th>
            <th>Order Total</th>
            <th>Commission Rate</th>
            <th>Commission</th>
            <th>Process Date</th>
            <th>Order Time</th>
        <tr>";
        $commissionTotal = (float)0.00;
        foreach ($orders as $order) {
            $commission = (float)$order['OrderTotal'] * 0.01 * $order['CommissionRate'];
            $commissionTotal += $commission;
            $commission = number_format($commission,2);
            echo "<tr>
                <td>{$order['CustomerName']}</td>
                <td>{$order['OrderTotal']}</td>
                <td>{$order['CommissionRate']}%</td>
                <td>\${$commission}</td>
                <td>{$order['ProcessDate']}</td>
                <td>{$order['OrderTime']}</td>
                </tr>";
        }
        echo "</table>";
        echo "Total commissions: $" . number_format($commissionTotal,2);
    }
    catch(PDOexception $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>
</body>
</html>
<style>