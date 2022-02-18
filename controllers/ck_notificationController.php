<?php

require("models/ck_database.php");
require("models/val_notifications.php");

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : '';

$leaveId = isset($_GET['leaveFormId']) ? $_GET['leaveFormId'] : '';

$notification = new Notifications();

$notificationType = $notification->getNotificationType($userID);

$notificationCount = $notificationType->num_rows;

$position = $notification->getPosition($userID);

$countAllNotification = $notification->countNotification($position);
