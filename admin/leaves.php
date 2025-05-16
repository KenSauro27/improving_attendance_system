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
  </style>
  <title>Hello, world!</title>
</head>

<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-md-8 mt-4" style="font-family: 'Arial';">
        <?php $getAllLeaves = getAllLeaves($pdo); ?>
        <?php foreach ($getAllLeaves as $row) { ?>
          <div class="card shadow mt-4">
            <div class="card-body p-5 leaveContainer">
              <h2 class="fullName"><?php echo $row['last_name'] . ", " . $row['first_name']; ?></h2>
              <h5 class="dateStart">DATE START: <?php echo $row['date_start']; ?></h5>
              <h5 class="dateEnd">DATE END: <?php echo $row['date_end']; ?></h5>
              <p class="leaveDescription mt-4">
                DESCRIPTION: <?php echo $row['description']; ?>
              </p>
              <p class="leaveStatus">
                STATUS:
                <?php
                if ($row['status'] == 'Rejected') {
                  echo "<span style='color:red;'>" . $row['status'] . "</span>";
                }
                if ($row['status'] == 'Accepted') {
                  echo "<span style='color:green;'>" . $row['status'] . "</span>";
                }
                if ($row['status'] == 'Pending') {
                  echo "Pending";
                }
                ?>
              </p>
              <i>DATE ADDED: <?php echo $row['date_added']; ?></i>
            </div>
            <form action="core/handleForms.php" method="POST">
              <input type="hidden" class="leave_id" value="<?php echo $row['leave_id']; ?>">
              <div class="form-group float-right px-5">
                <label for="exampleFormControlSelect1">Update Status</label>
                <select class="form-control leaveStatus">
                  <option value="">Change Status Here</option>
                  <option value="Pending">Pending</option>
                  <option value="Accepted">Accepted</option>
                  <option value="Rejected">Rejected</option>
                </select>
              </div>
            </form>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <script>
    $('.leaveStatus').on('change', function(event) {
      var formData = {
        leave_id: $(this).closest('form').find('.leave_id').val(),
        leave_status: $(this).val(),
        updateLeaveStatus: 1
      }

      if (formData.leave_id != "" && formData.leave_status != "") {
        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function(data) {
            location.reload();
          }
        })
      } else {
        alert("Make sure no fields are empty!");
      }

    })
    // Add this to the existing script section
    $('#notificationDropdown').on('click', function() {
      // Mark notifications as read when dropdown is opened
      $.post('core/handleForms.php', {
        markNotificationsAsRead: true,
        user_id: <?= $_SESSION['user_id'] ?? 0 ?>
      });
    });
  </script>
  <?php include 'includes/footer.php'; ?>
</body>

</html>