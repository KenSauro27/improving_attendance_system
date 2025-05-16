<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_admin'] == 1) {
  header("Location: ../admin/index.php");
}

// Add this near the top after session checks
markNotificationsAsRead($pdo, $_SESSION['user_id']);
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
    <div class="row justify-content-center">
      <div class="display-4">All Leaves. Double click to edit</div>
      <div class="col-md-10">
        <div class="card shadow mt-4">
          <?php $getLeavesByUserId = getLeavesByUserId($pdo, $_SESSION['user_id']); ?>
          <?php foreach ($getLeavesByUserId as $row) { ?>
            <div class="card-body p-5 leaveContainer">
              <h5 class="dateStart">DATE START: <?php echo $row['date_start']; ?></h5>
              <h5 class="dateEnd">DATE END: <?php echo $row['date_end']; ?></h5>
              <p class="leaveDescription mt-4">
                DESCRIPTION: <?php echo $row['description']; ?>
              </p>
              <p class="leaveStatus">STATUS:
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
              <form action="core/handleForms.php" class="mt-5 d-none" id="editLeaveForm">
                <h2>Edit your leave</h2>
                <div class="form-group">
                  <label for="exampleFormControlTextarea1">Description</label>
                  <input type="hidden" id="leave_id" value="<?php echo $row['leave_id']; ?>">
                  <textarea class="form-control" id="description" rows="3" name="description"><?php echo $row['description']; ?></textarea>
                </div>
                <div class="form-group">
                  <label for="exampleFormControlTextarea1">Date Start</label>
                  <input type="date" class="form-control" name="date_start" value="<?php echo $row['date_start']; ?>" id="date_start">
                </div>
                <div class="form-group">
                  <label for="exampleFormControlTextarea1">Date End</label>
                  <input type="date" class="form-control" name="date_end" value="<?php echo $row['date_end']; ?>" id="date_end">
                  <input type="submit" class="mt-4 btn btn-primary float-right" name="editLeaveBtn">
                </div>
              </form>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <script>
    $('.leaveContainer').on('dblclick', function(event) {
      $(this).find('#editLeaveForm').toggleClass('d-none');
    })

    $('#editLeaveForm').on('submit', function(event) {
      event.preventDefault();

      var formData = {
        description: $(this).find('#description').val(),
        date_start: $(this).find('#date_start').val(),
        date_end: $(this).find('#date_end').val(),
        leave_id: $(this).find('#leave_id').val(),
        editLeaveBtn: 1
      }

      if (formData.description != "" && formData.date_start != "" && formData.date_end != "") {
        $.ajax({
          type: "POST",
          url: "core/handleForms.php",
          data: formData,
          success: function(data) {
            location.reload();
          }
        })
      } else {
        alert("Make sure there are no empty input fields!")
      }
    })
  </script>
  <?php include 'includes/footer.php'; ?>
</body>

</html>