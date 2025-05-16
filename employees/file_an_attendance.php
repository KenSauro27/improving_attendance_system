<?php 
require_once 'core/dbConfig.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_admin'] == 1) {
  header("Location: ../admin/index.php");
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
  <body onload="startTime()">
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card shadow mt-4 p-4">
            <form action="core/handleForms.php" method="POST">
              <div class="card-header">
                <h2>File attendance here</h2>
              </div>
              <div class="card-body">
                <?php  
                  if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

                    if ($_SESSION['status'] == "200") {
                      echo "<h1 style='color: green;'>{$_SESSION['message']}</h1>";
                    }

                    else {
                      echo "<h1 style='color: red;'>{$_SESSION['message']}</h1>"; 
                    }

                  }
                  unset($_SESSION['message']);
                  unset($_SESSION['status']);
                ?>
                <div class="form-group">
                  <label for="exampleInputEmail1">Select attendance type</label>
                  <select class="form-control" name="attendance_type">
                    <option value="time_in">Time In</option>
                    <option value="time_out">Time Out</option>
                  </select>
                  <input type="hidden" name="date_today" value="<?php $date = date('Y-m-d H:i:s'); echo date('Y-m-d', strtotime($date)); ?>">
                  <input type="submit" class="btn btn-primary float-right mt-4" name="insertNewAttendanceBtn">
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php include 'includes/footer.php'; ?>
  </body>
</html>
