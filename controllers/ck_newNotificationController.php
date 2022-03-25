<?php

require("../models/ck_database.php");
require("../models/val_notifications.php");

session_start();

$notifications = new Notifications();

$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : '';

if (isset($_POST['modal'])) {
    $notificationKey = $_POST['hiddenId'];

    echo $notifications->createLeaveModal($notificationKey);
}

if (isset($_POST['modal2'])) {
    $notificationKey = $_POST['hiddenId'];

    echo $notifications->createHRModal($notificationKey);
}

if (isset($_POST['modal3'])) {
    $notificationKey = $_POST['hiddenId'];

    echo $notifications->createLeaderModal($notificationKey);
}

if (isset($_POST['approveLeader'])) {
    $listId = $_POST['listId'];
    $approval = $_POST['leaderRemark'];
    $status = $_POST['decisionOfLeader'];

    $notifications->leaderFormApproval($listId, $status, $approval);
}

if (isset($_POST['approve'])) {
    $listId = $_POST['listId'];
    $approval = $_POST['headRemark'];
    $status = $_POST['decisionOfHead'];

    $notifications->leaveFormApproval($listId, $status, $approval);
}

if (isset($_POST['setStatus'])) {
    $leaveType = $_POST['leaveType'];
    $remarks = $_POST['remarks'];
    $status = $_POST['status'];
    $type = $_POST['type'];
    $transpoAllowance = $_POST['transpoAllowance'];
    $quarantine = $_POST['quarantine'];
    $newEmpNum = $_POST['newEmpNum'];
    $decision = $_POST['decision'];
    $listId = $_POST['list'];

    $notifications->updateHR($decision, $leaveType, $remarks, $status, $type, $transpoAllowance, $quarantine, $newEmpNum, $listId);
}
