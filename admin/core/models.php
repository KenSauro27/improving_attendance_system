<?php

require_once 'dbConfig.php';

function checkIfUserExists($pdo, $username)
{
	$response = array();
	$sql = "SELECT * FROM attendance_system_users WHERE username = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$username])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result" => true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		} else {
			$response = array(
				"result" => false,
				"status" => "400",
				"message" => "User doesn't exist from the database"
			);
		}
	}

	return $response;
}

function insertNewUser($pdo, $username, $first_name, $last_name, $password)
{
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $username);

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO attendance_system_users (username, first_name, last_name, is_admin, password) 
		VALUES (?,?,?,?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$username, $first_name, $last_name, true, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		} else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	} else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}

function getAllUsers($pdo)
{
	$sql = "SELECT * FROM attendance_system_users";
	$stmt = $pdo->prepare($sql);
	$executeQuery = $stmt->execute();

	if ($executeQuery) {
		return $stmt->fetchAll();
	}
}

function getAllEmployees($pdo)
{
	$sql = "SELECT * FROM attendance_system_users 
			WHERE is_admin = 0";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function getAllDates($pdo)
{
	$sql = "SELECT DISTINCT date_added FROM attendance_records ORDER BY date_added DESC";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function getAllAttendancesByDate($pdo, $date_today)
{
	$sql = "SELECT 
                u.user_id,
                u.first_name, 
                u.last_name,
                MAX(CASE WHEN r.attendance_type = 'time_in' THEN r.timestamp_record_added END) AS time_in,
                MAX(CASE WHEN r.attendance_type = 'time_out' THEN r.timestamp_record_added END) AS time_out
            FROM attendance_system_users u
            LEFT JOIN attendance_records r ON 
                u.user_id = r.user_id AND 
                r.date_added = ?
            WHERE u.is_admin = 0
            GROUP BY u.user_id, u.first_name, u.last_name
            ORDER BY u.last_name";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$date_today]);
	return $stmt->fetchAll();
}

function getUsersWithIncompleteAttendance($pdo, $date_today)
{
	$sql = "SELECT
			  attendance_system_users.user_id AS user_id,
			  attendance_system_users.username AS username,
			  attendance_system_users.first_name AS first_name,
			  attendance_system_users.last_name AS last_name
			FROM attendance_system_users
			LEFT JOIN attendance_records
			  ON attendance_system_users.user_id = attendance_records.user_id
			  AND attendance_records.date_added = ?
			WHERE
			  attendance_records.user_id IS NULL 
			  AND attendance_system_users.is_admin = 0";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$date_today]);
	return $stmt->fetchAll();
}

function getAllLeaves($pdo)
{
	$sql = "SELECT 
				attendance_system_users.first_name AS first_name,
				attendance_system_users.last_name AS last_name,
				leaves.leave_id AS leave_id,
				leaves.description AS description,
				leaves.date_start AS date_start,
				leaves.date_end AS date_end,
				leaves.date_added AS date_added,
				leaves.status AS status
			FROM attendance_system_users
			JOIN leaves ON 
				attendance_system_users.user_id = leaves.user_id
			ORDER BY leaves.date_added DESC
			";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	return $stmt->fetchAll();
}

function updateLeaveStatus($pdo, $status, $leave_id)
{
	$sql = "UPDATE leaves SET status = ? WHERE leave_id = ?";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$status, $leave_id]);
}

// Add to models.php
function createNotification($pdo, $user_id, $message, $sender_id = null)
{
	$sql = "INSERT INTO notifications (user_id, sender_id, message) VALUES (?, ?, ?)";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$user_id, $sender_id, $message]);
}

function getUnreadNotificationsCount($pdo, $user_id)
{
	$sql = "SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND is_read = FALSE";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$user_id]);
	return $stmt->fetch()['count'];
}

function getNotifications($pdo, $user_id)
{
	$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$user_id]);
	return $stmt->fetchAll();
}

function markNotificationsAsRead($pdo, $user_id)
{
	$sql = "UPDATE notifications SET is_read = TRUE WHERE user_id = ? AND is_read = FALSE";
	$stmt = $pdo->prepare($sql);
	return $stmt->execute([$user_id]);
}
