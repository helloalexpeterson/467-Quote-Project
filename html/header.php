<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/docs/5.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <title><?php if(isset($pagetitle)){  echo $pagetitle;  } else {  echo "Quotes";  } ?></title>
    <meta charset="utf-8">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
     <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
    </style>

</head>
<body class="bg-light">
<main>
<header class="p-3 text-bg-dark bg-dark">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <ul class="nav me-lg-auto">
<?php if(!(isset($_SESSION['userType']))){

  header("Location: login.php", 303);

} ?>     


<?php if($_SESSION['userType'] == 'Sales Associate' || $_SESSION['userType'] == 'Superuser'): ?>     
          <li class='px-2'><a href="quotes.php?type=open" class="nav-link text-white btn btn-primary ">Open Quotes</a></li>
          <li class='px-2'><a href="open.php" class="nav-link text-white btn btn-primary">Create New Quote</a></li>
          <li class='px-2'><a href="myCommissions.php" class="nav-link text-white btn btn-primary">My Commissions</a></li>
<?php endif; ?>

<?php if($_SESSION['userType'] == 'Headquarters' || $_SESSION['userType'] == 'Superuser'): ?>     
          <li class='px-2'><a href="quotes.php?type=finalized" class="nav-link text-white btn btn-primary ">Finalized Quotes</a></li>
          <li class='px-2'><a href="quotes.php?type=sanctioned" class="nav-link text-white btn btn-primary">Sanctioned Quotes</a></li>
          <li class='px-2'><a href="quotes.php?type=ordered" class="nav-link text-white btn btn-primary">Ordered Quotes</a></li>
<?php endif; ?>

<?php if($_SESSION['userType'] == 'Administrator' || $_SESSION['userType'] == 'Superuser'): ?>     
          <li class='px-2'><a href="admin.php" class="nav-link text-white btn btn-primary ">Associate Management</a></li>
          <li class='px-2'><a href="adminSort.php" class="nav-link text-white btn btn-primary">Quote Management</a></li>
<?php endif; ?>
        </ul>
        <div class="text-end">
        <span class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
        Logged in as: <?php echo $_SESSION['username'], ' - ', $_SESSION['userType']; ?> 
        <ul class="nav me-lg-auto">
        <li class='px-2'><a href="logout.php" class="nav-link text-white btn btn-primary">Logout</a></li>
        </ul>
        </span>
        </div>
      </div>
    </div>
  </header>
</main>    