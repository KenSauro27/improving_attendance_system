<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../login.php");
			} else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}
		} else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}
	} else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];
		$isAdminStatusFromDB = $loginQuery['userInfoArray']['is_admin'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
			$_SESSION['is_admin'] = $isAdminStatusFromDB;
			header("Location: ../index.php");
		} else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	} else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
	}
}

if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['username']);
	header("Location: ../login.php");
}

if (isset($_POST['insertNewAttendanceBtn'])) {
	$user_id = $_SESSION['user_id'];
	$attendance_type = $_POST['attendance_type'];
	$date_today = $_POST['date_today'];

	if (!empty($user_id) && !empty($attendance_type) && !empty($date_today)) {

		if (!checkIfTimeInOrOutAlready($pdo, $user_id, $attendance_type, $date_today)) {
			if (insertNewAttendance($pdo, $user_id, $attendance_type, $date_today)) {
				$_SESSION['message'] = $attendance_type . " successfully added!";
				$_SESSION['status'] = '200';
			} else {
				$_SESSION['message'] = "An error occured with the query!";
				$_SESSION['status'] = '400';
			}
		} else {
			$_SESSION['message'] = "You already submitted your " . $attendance_type . " for today!";
			$_SESSION['status'] = '400';
		}
	} else {
		$_SESSION['message'] = "Make sure no input fields are empty!";
		$_SESSION['status'] = '400';
	}
	header("Location: ../file_an_attendance.php");
}

//Modify the insertNewLeaveBtn section
if (isset($_POST['insertNewLeaveBtn'])) {
	$description = $_POST['description'];
	$date_start = $_POST['date_start'];
	$date_end = $_POST['date_end'];
	$user_id = $_SESSION['user_id'];

	if (insertNewLeave($pdo, $description, $user_id, $date_start, $date_end)) {
		// Get the newly created leave ID
		$leave_id = $pdo->lastInsertId();

		// Notify all admins
		$admins = getAdminUsers($pdo);
		foreach ($admins as $admin) {
			$message = "New leave request #$leave_id filed by {$_SESSION['username']}";
			createNotification($pdo, $admin['user_id'], $message, $user_id);
		}

		$_SESSION['message'] = "Leave successfully saved!";
		$_SESSION['status'] = '200';
		header("Location: ../file_a_leave.php");
	}
}

if (isset($_POST['editLeaveBtn'])) {
	$description = $_POST['description'];
	$date_start = $_POST['date_start'];
	$date_end = $_POST['date_end'];
	$leave_id = $_POST['leave_id'];
	updateLeaveDescription($pdo, $description, $date_start, $date_end, $leave_id);
}

// Add these at the bottom of handleForms.php
if (isset($_GET['markNotificationsAsRead'])) {
	markNotificationsAsRead($pdo, $_GET['user_id']);
	exit;
}

if (isset($_GET['getUnreadCount'])) {
	$count = getUnreadNotificationsCount($pdo, $_GET['user_id']);
	echo json_encode(['count' => $count]);
	exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'mark_notifications_read' && isset($_POST['user_id'])) {
	require_once 'models.php';
	markNotificationsAsRead($pdo, $_POST['user_id']);
	exit;
}

if (isset($_GET['get_unread_count'])) {
	require_once 'models.php';
	$count = getUnreadNotificationsCount($pdo, $_GET['user_id']);
	echo json_encode(['count' => $count]);
	exit;
}
