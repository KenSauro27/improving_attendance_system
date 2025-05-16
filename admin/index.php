<?php
require_once 'core/dbConfig.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_admin'] == 0) {
  header("Location: ../employees/index.php");
}
?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <style>
    body {
      font-family: "Arial";
    }
  </style>
  <title>Hello, world!</title>
</head>

<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container-fluid">
    <div class="col-md-12">
      <h1 class="p-5">Welcome to the Admin Center!, <?php echo $_SESSION['username']; ?>!</h1>
    </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</body>

</html>