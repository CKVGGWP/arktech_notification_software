<?php

require("../models/ck_database.php");
require("../models/val_notifications.php");

session_start();

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : '';
$notifications = new Notifications();

if ($notifications->getPosition($userID) == "HR Staff") {
    $notificationsData = $notifications->getHRTable();
} else {
    $notificationsData = $notifications->getTable($userID);
}

echo $notificationsData;