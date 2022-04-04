<?php

require("models/ck_database.php");
require("models/val_notifications.php");

session_start();

$userID = isset($_SESSION['idNumber']) ? $_SESSION['idNumber'] : '';

$leaveId = isset($_GET['leaveFormId']) ? $_GET['leaveFormId'] : '';

$notification = new Notifications();

$notificationType = $notification->getNotificationType($userID);

$notificationCount = $notificationType->num_rows;

$position = $notification->getPosition($userID);

$countAllNotification = $notification->countNotification($position);

