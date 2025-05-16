<?php
if (!isset($pdo)) {
  require_once __DIR__ . '/../core/dbConfig.php';
  require_once __DIR__ . '/../core/models.php';
}
?>

<style>
  /* Notification Dropdown Styling */
  #notificationDropdown {
    transition: all 0.3s ease;
  }

  .dropdown-menu.notification-dropdown {
    width: 350px;
    padding: 0;
    border: none;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    border-radius: 8px;
    overflow: hidden;
    transform: translateY(10px);
    animation: fadeIn 0.3s ease forwards;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(10px);
    }
  }

  .dropdown-menu.notification-dropdown .dropdown-header,
  .dropdown-menu.notification-dropdown .dropdown-footer {
    padding: 12px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
    font-weight: 600;
  }

  .dropdown-menu.notification-dropdown .dropdown-item {
    padding: 12px 20px;
    border-bottom: 1px solid #f5f5f5;
    transition: all 0.2s ease;
  }

  .dropdown-menu.notification-dropdown .dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
  }

  .dropdown-menu.notification-dropdown .unread {
    background-color: #f0f7ff;
    border-left: 3px solid #008080;
  }

  .dropdown-menu.notification-dropdown .dropdown-item small {
    font-size: 0.8em;
    opacity: 0.8;
  }

  /* Add this to your existing .font-weight-bold selector */
  .font-weight-bold.text-primary {
    background-color: #f0f7ff !important;
  }
</style>

<nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: #008080;">
  <a class="navbar-brand" href="#">Admin Panel</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="all_attendances.php">All Attendances</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="leaves.php">Leaves</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="all_users.php">All Users</a>
      </li>
      <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Notifications
            <?php
            if (function_exists('getUnreadNotificationsCount')) {
              $unreadCount = getUnreadNotificationsCount($pdo, $_SESSION['user_id']);
              if ($unreadCount > 0): ?>
                <span class="badge badge-danger badge-pill position-absolute" style="top: 0; right: 0;" id="unreadCountBadge"><?= $unreadCount ?></span>
            <?php endif;
            } ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notificationDropdown">
            <div class="dropdown-header">Notifications</div>
            <?php
            if (function_exists('getNotifications')) {
              $notifications = getNotifications($pdo, $_SESSION['user_id']);
              if (empty($notifications)): ?>
                <span class="dropdown-item">No notifications</span>
              <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                  <a class="dropdown-item <?= $notification['is_read'] ? 'text-muted' : 'font-weight-bold text-primary' ?>" href="#">
                    <?= $notification['message'] ?>
                    <small class="d-block"><?= date('M j, Y g:i a', strtotime($notification['created_at'])) ?></small>
                  </a>
                <?php endforeach; ?>
              <?php endif;
            } else { ?>
              <span class="dropdown-item">Notifications not available</span>
            <?php } ?>
          </div>
        </li>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link" href="core/handleForms.php?logoutUserBtn=1">Logout</a>
      </li>
    </ul>
  </div>
  <script>
    $(document).ready(function() {
      // Mark notifications as read when dropdown is opened
      $('#notificationDropdown').on('click', function() {
        $.ajax({
          url: 'core/handleForms.php',
          type: 'POST',
          data: {
            action: 'mark_notifications_read',
            user_id: <?= $_SESSION['user_id'] ?? 0 ?>
          },
          success: function() {
            // Hide the unread count badge
            $('#unreadCountBadge').hide();
            // Update all notification items to appear as read
            $('.dropdown-item').removeClass('font-weight-bold text-primary').addClass('text-muted');
          }
        });
      });

      setInterval(function() {
        if (typeof getUnreadNotificationsCount !== 'undefined') {
          $.get('core/handleForms.php?get_unread_count=1&user_id=<?= $_SESSION['user_id'] ?? 0 ?>', function(data) {
            if (data.count > 0) {
              $('#unreadCountBadge').text(data.count).show();
            } else {
              $('#unreadCountBadge').hide();
            }
          });
        }
      }, 30000);
    });
  </script>
</nav>