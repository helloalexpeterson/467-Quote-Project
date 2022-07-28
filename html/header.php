<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php if(isset($pagetitle)){  echo $pagetitle;  } else {  echo "Quotes";  } ?></title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/css/quote.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
<?php 
echo "<b> Logged in as: {$_SESSION['username']} - {$_SESSION['userType']}</b>";
echo "<ul class='navbar-nav me-auto mb-2 mb-lg-0'>";
if($_SESSION['userType'] == 'Sales Associate'){
    echo<<<HTML
                <li class="nav-item"><a class="nav-link" href='quotes.php?type=open'>Open Quotes</a></li>
                <li class="nav-item"><a class="nav-link" href='open.php'>Create New Quote</a></li>
                <li class="nav-item"><a class="nav-link" href='#'>My Commissions</a></li>           
       HTML;
   } 
   if($_SESSION['userType'] == 'Headquarters'){
    echo<<<HTML
                <li class="nav-item"><a class="nav-link" href='quotes.php?type=finalized'>Finalized Quotes</a></li>
                <li class="nav-item"><a class="nav-link" href='quotes.php?type=sanctioned'>Sanctioned Quotes</a></li>
                <li class="nav-item"><a class="nav-link" href='quotes.php?type=ordered'>Ordered Quotes</a></li>  
       HTML;
   } 
   if($_SESSION['userType'] == 'Administrator'){
    echo<<<HTML
                <li class="nav-item"><a class="nav-link" href='admin.php'>Associate Management</a></li>  
                <li class="nav-item"><a class="nav-link" href='#'>Quote Management</a></li>  
         HTML;
   }
?>  
                <li class="nav-item"><a class="nav-link" href='logout.php'>Logout</a></li>
            </ul>
        </div>
    </nav>
    <h1>Plant Repair:</h1>

    