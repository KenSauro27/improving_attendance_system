<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

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
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/smoothness/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <style>
    body {
      font-family: "Arial";
    }

    /* Table styling */
    .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th {
      background-color: #008080;
      color: white;
      padding: 12px;
      text-align: left;
    }

    .table td {
      padding: 10px 12px;
      border-bottom: 1px solid #e0e0e0;
    }

    .table tr:hover {
      background-color: #f5f5f5;
    }

    .no-attendance {
      color: #dc3545;
      font-style: italic;
    }

    .card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #e0e0e0;
      font-weight: 600;
      padding: 15px 20px;
    }
  </style>
  <title>Attendance Records</title>
</head>

<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container-fluid">
    <div class="col-md-12">
      <?php $getAllDates = getAllDates($pdo); ?>
      <?php foreach ($getAllDates as $row) { ?>
        <div class="card shadow mt-4">
          <div class="card-header">
            <h2><?php echo $row['date_added']; ?></h2>
          </div>
          <div class="card-body">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Status</th>
                  <th scope="col">First Name</th>
                  <th scope="col">Last Name</th>
                  <th scope="col">Time In</th>
                  <th scope="col">Time Out</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $getAllAttendancesByDate = getAllAttendancesByDate($pdo, $row['date_added']);
                $getUsersWithIncompleteAttendance = getUsersWithIncompleteAttendance($pdo, $row['date_added']);

                // Display users with attendance
                foreach ($getAllAttendancesByDate as $innerRow) {
                  $time_in = isset($innerRow['time_in']) ? $innerRow['time_in'] : null;
                  $time_out = isset($innerRow['time_out']) ? $innerRow['time_out'] : null;

                  if ($time_in || $time_out) {
                ?>
                    <tr>
                      <td>
                        <?php
                        if ($time_in && $time_out) {
                          echo '<span>Complete</span>';
                        } else {
                          echo '<span>Incomplete</span>';
                        }
                        ?>
                      </td>
                      <td><?php echo $innerRow['first_name']; ?></td>
                      <td><?php echo $innerRow['last_name']; ?></td>
                      <td class="time-cell"><?php echo $time_in ?? '<span class="no-attendance">No time in</span>'; ?></td>
                      <td class="time-cell"><?php echo $time_out ?? '<span class="no-attendance">No time out</span>'; ?></td>
                    </tr>
                  <?php
                  }
                }

                // Display users with no attendance records at all
                foreach ($getUsersWithIncompleteAttendance as $innerRow) {
                  ?>
                  <tr>
                    <td><span class="badge badge-danger">Missing</span></td>
                    <td><?php echo $innerRow['first_name']; ?></td>
                    <td><?php echo $innerRow['last_name']; ?></td>
                    <td colspan="2" class="text-danger">No attendance recorded</td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
    </div>
  <?php } ?>
  </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</body>

</html>