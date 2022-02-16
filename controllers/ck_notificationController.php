<?php

require("models/ck_database.php");
require("models/val_notifications.php");

$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : '';

$notification = new Notifications();

if ($notification->getPosition($userID) == "HR Staff" || $notification->getPosition($userID) == "President") {
    $notificationType = $notification->getNotificationType();
} else {
    $notificationType = "";
}
