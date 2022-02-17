<?php

require("../models/ck_database.php");
require("../models/val_notifications.php");

session_start();

$notifications = new Notifications();

$userId = isset($_SESSION['userID']) ? $_SESSION['userID'] : '';

if (isset($_POST['newType'])) {
    $type = $_POST['notificationType'];
    $notificationsData = $notifications->getTable($type, $userId);
} else {
    $notificationsData = $notifications->getTable("", $userId);
}

echo $notificationsData;
