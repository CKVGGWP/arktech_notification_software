<?php

require("../models/ck_database.php");
require("../models/val_notifications.php");

$notifications = new Notifications();

if (isset($_POST['newType'])) {
    $type = $_POST['notificationType'];
    $notificationsData = $notifications->getTable($type);
} else {
    $notificationsData = $notifications->getTable("");
}

echo $notificationsData;
