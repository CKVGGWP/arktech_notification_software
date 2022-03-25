<?php

class Notifications extends Database
{
    // CK Code Start

    public function getTable($notificationType = '', $userId)
    {

        $sql = "SELECT departmentId, sectionId FROM hr_employee WHERE idNumber LIKE '" . $userId . "'";
        $queryRose = $this->connect()->query($sql);
        if ($queryRose and $queryRose->num_rows > 0) {
            $resultRose = $queryRose->fetch_assoc();
            $rose_departmentId = $resultRose['departmentId'];
            $rose_sectionId = $resultRose['sectionId'];
        }

        $query = "SELECT 
                  d.notificationId,
                  d.notificationDetail,
                  d.notificationKey,
                  d.notificationLink,
                  d.notificationType 
                  FROM system_notificationdetails d
                  LEFT JOIN system_notification n ON n.notificationId = d.notificationId";

        // if ($notificationType != "") {
        // $query .= " WHERE notificationType = '$notificationType' AND notificationTarget = '$userId' AND notificationStatus = '0'";
        // } else {
        // $query .= " WHERE notificationTarget = '$userId' AND notificationStatus = '0'";
        // }
        if ($notificationType != "") {
            $query .= " WHERE notificationType = '$notificationType' AND ((notificationTarget = '$userId' AND targetType=2) OR (notificationTarget = '$rose_sectionId' AND targetType=1) OR (notificationTarget = '$rose_departmentId' AND targetType=0)) AND notificationStatus = '0'";
        } else {
            $query .= " WHERE ((notificationTarget = '$userId' AND targetType=2) OR (notificationTarget = '$rose_sectionId' AND targetType=1) OR (notificationTarget = '$rose_departmentId' AND targetType=0)) AND notificationStatus = '0'";
        }

        $query .= " ORDER BY notificationId DESC";

        $sql = $this->connect()->query($query);
        $data = [];
        $totalData = 0;
        if ($sql->num_rows > 0) {

            while ($result = $sql->fetch_assoc()) {
                extract($result);
                $button = '';

                $getFile = explode("?", $notificationLink);
                $newFile = $getFile[0];

                $B4addLink = "";
                $addLink = "";

                if ($notificationType == 12) //CPAR;
                {
                    $B4addLink = "/V4/";
                }

                if (file_exists('../../../' . $B4addLink . $newFile)) {
                    if ($notificationType == 12) //CPAR;
                    {
                        $addLink = "&cparId=" . $notificationKey . "";
                    }
                    if ($notificationType == 14) //OB;
                    {
                        $addLink = "&obKey=" . $notificationKey . "";
                    }
                    if ($notificationType == 17 or $notificationType == 21 or $notificationType == 24) {
                        $addLink = "&inventoryId=" . $notificationKey . "";
                    }
                    if ($notificationType == 20) {
                        $addLink = "&deniedPurchaseOrder=1";
                    }
                    if ($notificationType == 33) {
                        $addLink = "&projectListId=" . $notificationKey;
                    }
                    if ($notificationType == 34) {
                        $addLink = "&shareListId=" . $notificationKey;
                    }
                    if ($notificationType == 36 or $notificationType == 1) {
                        $addLink = "&batchNumber=" . $notificationKey . "";
                    }


                    // $button .= '<a href="../..' .$notificationLink . '&title=Notification" class="btn btn-warning">Redirect</a>';
                    if (stristr($notificationLink, "?")) {
                        $button .= '<a href="../..' . $B4addLink . $notificationLink . '&title=Notification&notificationId=' . $notificationId . '' . $addLink . '" class="btn btn-warning">
  						        Redirect
					            </a>';
                    } else {
                        $button .= '<a href="../..' . $B4addLink . $notificationLink . '?title=Notification&notificationId=' . $notificationId . '' . $addLink . '" class="btn btn-warning">
  						        Redirect
					            </a>';
                    }
                } else {
                    $button .= '<a href="../..' . $notificationLink . '" class="btn btn-danger">
  						        Not Found
					            </a>';
                }
                if ($_SESSION['idNumber'] == '-0940') {
                    $data[] = [
                        $notificationId,
                        $notificationDetail . $query,
                        $notificationKey,
                        $button,
                    ];
                } else {
                    $data[] = [
                        $notificationId,
                        $notificationDetail,
                        $notificationKey,
                        $button,
                    ];
                }
                $totalData++;
            }
        }
        $json_data = array(
            "draw"            => 1,   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval($totalData),  // total number of records
            "recordsFiltered" => intval($totalData), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data   // total data array
        );

        echo json_encode($json_data);  // send data as json format
    }

    private function getLeaveForm($key)
    {
        $sql = "SELECT
                listId,
                dateIssued,
                employeeNumber,
                employeeName,
                designation,
                department,
                purposeOfLeave,
                leaveFrom,
                leaveTo,
                documents,
                dateApproveDenyByLeader,
                reasonOfLeader,
                reasonOfSuperior,
                date
                FROM system_leaveform
                WHERE listId = '$key'";
        $query = $this->connect()->query($sql);

        $data = array();

        while ($result = $query->fetch_assoc()) {
            $data[] = $result;
        }

        return $data;
    }

    public function createLeaderModal($notificationKey)
    {
        $html = '';

        $data = $this->getLeaveForm($notificationKey);

        foreach ($data as $key => $result2) :
            if ($result2['documents'] != "") {
                $html .= '<div class="col-sm-12 mb-sm-0 mb-5">
                        <div class="row">
                            <div class="btn-group mb-4">
                               <a class="btn btn-warning" target="_blank"href="' . $result2['documents'] . '">View attached document</a>
                            </div>
                        </div>
                    </div>';
            }
            $html .= '<div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Employee No.</label>
                            <input readonly class="form-control" id="employeeNumber" name="employeeNumber" value="' . $result2['employeeNumber'] . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Employee Name</label>
                            <input readonly class="form-control" id="employeeName" name="employeeName" value="' . $result2['employeeName'] . '"></input>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-sm-0 mb-1" hidden>
                        <div class="row">
                            <label class="col-sm-5">List ID</label>
                            <input readonly class="form-control" id="listId" name="listId" value="' . $result2['listId'] . '"></input>
                        </div>
                    </div>
                    <div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Designation</label>
                            <input readonly class="form-control" id="designation" name="designation" value="' . $result2['designation'] . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Department</label>
                            <input readonly class="form-control" id="department" name="department" value="' . $result2['department'] . '"></input>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-5">Purpose of Leave</label>
                                <textarea readonly class="form-control" id="purposeofLeave" name="purposeofLeave" value="' . $result2['purposeOfLeave'] . '">' . $result2['purposeOfLeave'] . '</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Leave From:</label>
                            <input readonly class="form-control" id="leaveFrom" name="leaveFrom" value="' . date("F j, Y", strtotime($result2['leaveFrom'])) . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Leave To:</label>
                            <input readonly class="form-control" id="leaveTo" name="leaveTo" value="' . date("F j, Y", strtotime($result2['leaveTo'])) . '"></input>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-5">Approve Leave?</label>
                                <select class="form-control" id="decisionOfLeader" name="decisionOfLeader">
                                    <option>Select Decision</option>
                                    <option value="approve">Yes</option>
                                    <option value="disapprove">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="approvalLeader" class="d-none">
                        <div class="form-group mb-2">
                            <div class="col-md-12 mb-sm-0 mb-1">
                                <div>
                                    <label class="col-sm-5">Reason for Approval</label>
                                    <textarea class="form-control remarkLeaderClass" id="approvalLeaderRemarks" name="approvalLeaderRemarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="disapprovalLeader" class="d-none">
                        <div class="form-group mb-2">
                            <div class="col-md-12 mb-sm-0 mb-1">
                                <div>
                                    <label class="col-sm-5">Reason for Disapproval</label>
                                    <textarea class="form-control remarkLeaderClass" id="disapprovalLeaderRemarks" name="disapprovalLeaderRemarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';

        endforeach;

        $html .= '<div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="submitLeaderApproval">Submit</button>
                </div>
            </div>';


        return json_encode($html);
    }

    public function createLeaveModal($notificationKey)
    {
        $html = '';

        $data = $this->getLeaveForm($notificationKey);


        foreach ($data as $key => $result) :

            if ($result['documents'] != "") {
                $html .= '<div class="col-sm-12 mb-sm-0 mb-5">
                        <div class="row">
                            <div class="btn-group mb-4">
                               <a class="btn btn-warning" target="_blank"href="' . $result['documents'] . '">View attached document</a>
                            </div>
                        </div>
                    </div>';
            }
            $html .= '<div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Employee No.</label>
                            <input readonly class="form-control" id="employeeNumber" name="employeeNumber" value="' . $result['employeeNumber'] . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Employee Name</label>
                            <input readonly class="form-control" id="employeeName" name="employeeName" value="' . $result['employeeName'] . '"></input>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-sm-0 mb-1" hidden>
                        <div class="row">
                            <label class="col-sm-5">List ID</label>
                            <input readonly class="form-control" id="listId" name="listId" value="' . $result['listId'] . '"></input>
                        </div>
                    </div>
                    <div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Designation</label>
                            <input readonly class="form-control" id="designation" name="designation" value="' . $result['designation'] . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Department</label>
                            <input readonly class="form-control" id="department" name="department" value="' . $result['department'] . '"></input>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-5">Purpose of Leave</label>
                                <textarea readonly class="form-control" id="purposeofLeave" name="purposeofLeave" value="' . $result['purposeOfLeave'] . '">' . $result['purposeOfLeave'] . '</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Leave From:</label>
                            <input readonly class="form-control" id="leaveFrom" name="leaveFrom" value="' . date("F j, Y", strtotime($result['leaveFrom'])) . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Leave To:</label>
                            <input readonly class="form-control" id="leaveTo" name="leaveTo" value="' . date("F j, Y", strtotime($result['leaveTo'])) . '"></input>
                        </div>
                    </div>';

            if ($result['reasonOfLeader'] != "") :
                $html .= '<div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-6">Reason of Leader</label>
                                <textarea readonly class="form-control" id="reasonOfLeaderApproval" name="reasonOfLeaderApproval" rows="3" value="' . $result['reasonOfLeader'] . '">' . $result['reasonOfLeader'] . '</textarea>
                            </div>
                        </div>
                    </div>';
            endif;
            $html .= '<div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-5">Approve Leave?</label>
                                <select class="form-control" id="decisionOfHead" name="decisionOfHead">
                                    <option>Select Decision</option>
                                    <option value="approve">Yes</option>
                                    <option value="disapprove">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="approvalHead" class="d-none">
                        <div class="form-group mb-2">
                            <div class="col-md-12 mb-sm-0 mb-1">
                                <div>
                                    <label class="col-sm-5">Reason for Approval</label>
                                    <textarea class="form-control remarkHeadClass" id="approvalHeadRemarks" name="approvalHeadRemarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="disapprovalHead" class="d-none">
                        <div class="form-group mb-2">
                            <div class="col-md-12 mb-sm-0 mb-1">
                                <div>
                                    <label class="col-sm-5">Reason for Disapproval</label>
                                    <textarea class="form-control remarkHeadClass" id="disapprovalHeadRemarks" name="disapprovalHeadRemarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';

        endforeach;

        $html .= '<div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="submitApproval">Submit</button>
                </div>
            </div>';

        return json_encode($html);
    }

    public function createHRModal($notificationKey)
    {
        $html = '';

        $data = $this->getLeaveForm($notificationKey);

        foreach ($data as $key => $newResult) :
            if ($newResult['documents'] != "") {
                $html .= '<div class="col-sm-12 mb-sm-0 mb-5">
                        <div class="row">
                            <div class="btn-group mb-4">
                               <a class="btn btn-warning" target="_blank"href="' . $newResult['documents'] . '">View attached document</a>
                            </div>
                        </div>
                    </div>';
            }
            $html .= '<div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Employee No.</label>
                            <input readonly class="form-control" id="empNum" name="empNum" value="' . $newResult['employeeNumber'] . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Employee Name</label>
                            <input readonly class="form-control" id="empName" name="empName" value="' . $newResult['employeeName'] . '"></input>
                        </div>
                    </div>
                    <div class="col-sm-12 mb-sm-0 mb-1" hidden>
                        <div class="row">
                            <label class="col-sm-5">List ID</label>
                            <input readonly class="form-control" id="list" name="list" value="' . $newResult['listId'] . '"></input>
                        </div>
                    </div>
                    <div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Designation</label>
                            <input readonly class="form-control" id="des" name="des" value="' . $newResult['designation'] . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Department</label>
                            <input readonly class="form-control" id="dept" name="dept" value="' . $newResult['department'] . '"></input>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-5">Purpose of Leave</label>
                                <textarea readonly class="form-control" id="purpose" name="purpose" value="' . $newResult['purposeOfLeave'] . '">' . $newResult['purposeOfLeave'] . '</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2 row">
                        <div class="col-md-6">
                            <label class="col-sm-5">Leave From:</label>
                            <input readonly class="form-control" id="from" name="from" value="' . date("F j, Y", strtotime($newResult['leaveFrom'])) . '"></input>
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-5">Leave To:</label>
                            <input readonly class="form-control" id="to" name="to" value="' . date("F j, Y", strtotime($newResult['leaveTo'])) . '"></input>
                        </div>
                    </div>';

            if ($newResult['reasonOfLeader'] != "") :
                $html .= '<div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-6">Reason of Leader</label>
                                <textarea readonly class="form-control" id="reasonOfLeaderApproval" name="reasonOfLeaderApproval" rows="3" value="' . $newResult['reasonOfLeader'] . '">' . $newResult['reasonOfLeader'] . '</textarea>
                            </div>
                        </div>
                    </div>';
            endif;
            $html .= '<div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-6">Reason of Superior</label>
                                <textarea readonly class="form-control" id="reasonOfApproval" name="reasonOfApproval" rows="3" value="' . $newResult['reasonOfSuperior'] . '">' . $newResult['reasonOfSuperior'] . '</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-6">Date of Superior Approval</label>
                                <input readonly class="form-control" id="dateOfApproval" name="dateOfApproval" value="' . date("F j, Y", strtotime($newResult['date'])) . '"></input>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <div class="col-md-12 mb-sm-0 mb-1">
                            <div>
                                <label class="col-sm-6">Approve Leave?</label>
                                <select class="form-control" id="decision" name="decision">
                                    <option>Choose Decision</option>
                                    <option value="3">Yes</option>
                                    <option value="4">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="disapprovalHR" class="d-none">
                        <div class="form-group mb-2">
                            <div class="col-md-12 mb-sm-0 mb-1">
                                <div>
                                    <label class="col-sm-5">Reason for Disapproval</label>
                                    <textarea class="form-control remarkClass" id="disapprovalRemarks" name="disapprovalRemarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="approvalHR" class="d-none">
                        <div class="form-group mb-2">
                            <div class="col-md-12 mb-sm-0 mb-1">
                                <div>
                                    <label class="col-sm-5">Leave Type</label>
                                    <select name="leaveType" id="leaveType" class="form-control">
                                        <option value="">Whole Day</option>
                                        <option value="0.5">Half Day</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <div class="col-md-12 mb-sm-0 mb-1">
                                <div>
                                    <label class="col-sm-5">Leave Remarks</label>
                                    <textarea class="form-control remarkClass" id="leaveRemarks" name="leaveRemarks"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-2 row">
                            <div class="col-md-6">
                                <label class="col-sm-5">With Payment</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="0">Without Pay</option>
                                    <option value="1">With Pay</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="col-sm-5">Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="0">Sick Leave</option>
                                    <option value="1">Vacation Leave</option>
                                    <option value="2">Bereavement Leave</option>
                                    <option value="3">Maternity Leave</option>
                                    <option value="4">Emergency Leave</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-2 row">
                            <div class="col-md-6">
                                <label class="col-sm-12">Trasportation Allowance (if any)</label>
                                <select name="transpoAllowance" id="transpoAllowance" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="col-sm-5">Quarantine Flag</label>
                                <select name="quarantine" id="quarantine" class="form-control">
                                    <option value="0">Default</option>
                                    <option value="1">Due to Covid-19</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>';

        endforeach;

        $html .= '<div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="setStatusBTN">Set Status</button>
                </div>
            </div>';

        return json_encode($html);
    }

    public function getPosition($idNumber)
    {
        $sql = "SELECT p.positionName FROM hr_employee e 
                LEFT JOIN hr_positions p ON e.position = p.positionId 
                WHERE e.idNumber = '$idNumber'";
        $query = $this->connect()->query($sql);

        $result = $query->fetch_assoc();
        $pos = $result['positionName'];

        return $pos;
    }

    public function countNotification($position = '')
    {
        $HRId = $this->getHRId();
        $sql = "SELECT 
                COUNT(listId) AS notifCount 
                FROM system_notification";

        if ($position == 'HR Staff') {
            $sql_Rose = "SELECT departmentId, sectionId FROM hr_employee WHERE idNumber LIKE '" . $HRId . "'";
            $queryRose = $this->connect()->query($sql_Rose);
            if ($queryRose and $queryRose->num_rows > 0) {
                $resultRose = $queryRose->fetch_assoc();
                $rose_departmentId = $resultRose['departmentId'];
                $rose_sectionId = $resultRose['sectionId'];
            }
            // $sql .= " WHERE notificationTarget = '$HRId'";
            $roseString = " WHERE ((notificationTarget = '$HRId' AND targetType=2) OR (notificationTarget = '$rose_sectionId' AND targetType=1) OR (notificationTarget = '$rose_departmentId' AND targetType=0))";
            $sql .= "" . $roseString;
        } else {
            $sql_Rose = "SELECT departmentId, sectionId FROM hr_employee WHERE idNumber LIKE '" . $_SESSION['idNumber'] . "'";
            $queryRose = $this->connect()->query($sql_Rose);
            if ($queryRose and $queryRose->num_rows > 0) {
                $resultRose = $queryRose->fetch_assoc();
                $rose_departmentId = $resultRose['departmentId'];
                $rose_sectionId = $resultRose['sectionId'];
            }
            // $sql .= " WHERE notificationTarget = '" . $_SESSION['idNumber'] . "'";
            $roseString = " WHERE ((notificationTarget = '" . $_SESSION['idNumber'] . "' AND targetType=2) OR (notificationTarget = '$rose_sectionId' AND targetType=1) OR (notificationTarget = '$rose_departmentId' AND targetType=0))";
            $sql .= "" . $roseString;
        }

        $sql .= " AND notificationStatus = 0";

        $query = $this->connect()->query($sql);

        $data = '';

        if ($query->num_rows > 0) {
            while ($result = $query->fetch_assoc()) {
                $data = $result['notifCount'];
            }
        } else {
            $data = 0;
        }

        return $data;
    }

    private function getDepartment($dept)
    {
        $sql = "SELECT departmentId FROM hr_department WHERE departmentName = '$dept'";
        $query = $this->connect()->query($sql);

        $result = $query->fetch_assoc();
        $deptId = $result['departmentId'];

        return $deptId;
    }

    private function notif($dept)
    {
        $data = [];
        $sql = "SELECT 
                e.idNumber, 
                e.firstName, 
                p.positionName 
                FROM hr_employee e 
                LEFT JOIN hr_positions p ON e.position = p.positionId 
                WHERE (p.positionName LIKE '%supervisor%' OR p.positionName LIKE '%manager%')
                AND NOT p.positionName LIKE '%factory%'
                AND e.departmentId = '$dept'";
        $query = $this->connect()->query($sql);

        while ($result = $query->fetch_assoc()) {
            $data[] = $result;
        }

        return $data;
    }

    private function insertNotification($dept, $keys)
    {
        $notify = $this->notif($dept);

        foreach ($notify as $key => $value) {
            $sql = "INSERT INTO system_notification
                    (notificationId, notificationTarget, targetType) 
                    VALUES ('$keys', '" . $value['idNumber'] . "', '2')";
            $query = $this->connect()->query($sql);
        }

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function leaderFormApproval($id, $status, $remarks)
    {
        $sql = '';
        $data = '';
        if ($status == "disapprove") {
            $sql .= "UPDATE system_leaveform 
                     SET status = 2, reasonOfLeader = '$remarks', dateApproveDenyByLeader = NOW() 
                     WHERE listId = '$id'";
        } else if ($status == "approve") {
            $sql .= "UPDATE system_leaveform 
                     SET status = 1, reasonOfLeader = '$remarks', dateApproveDenyByLeader = NOW() 
                     WHERE listId = '$id'";
        }

        $query = $this->connect()->query($sql);

        if ($query) {
            if ($this->updateNotification($id)) {
                if ($status == "approve") {
                    $leaveForm = $this->getLeaveForm($id);
                    foreach ($leaveForm as $row) {
                        $dept = $row['department'];
                    }
                    $deptId = $this->getDepartment($dept);
                    if ($this->insertHRNotification($id)) {
                        $keys = $this->lastNotificationId();
                        if ($this->insertNotification($deptId, $keys)) {
                            $data = '1';
                        } else {
                            $data = '2';
                        }
                    } else {
                        $data = "2";
                    }
                } else {
                    $data = "1";
                }
            } else {
                $data = "2";
            }
        } else {
            $data = "2";
        }

        return $data;
    }

    public function leaveFormApproval($id, $status, $remarks)
    {
        $sql = '';
        $data = '';
        if ($status == "disapprove") {
            $sql .= "UPDATE system_leaveform 
                     SET status = 4, reasonOfSuperior = '$remarks', date = NOW() 
                     WHERE listId = '$id'";
        } else if ($status == "approve") {
            $sql .= "UPDATE system_leaveform 
                     SET status = 2, reasonOfSuperior = '$remarks', date = NOW() 
                     WHERE listId = '$id'";
        }

        $query = $this->connect()->query($sql);

        if ($query) {
            if ($this->updateNotification($id)) {
                if ($status == "approve") {
                    if ($this->insertHRNotification($id)) {
                        $keys = $this->lastNotificationId();
                        if ($this->insertSystemNotificationHR($keys)) {
                            $data = "1";
                        } else {
                            $data = "2";
                        }
                    } else {
                        $data = "2";
                    }
                } else {
                    $data = "1";
                }
            } else {
                $data = "2";
            }
        } else {
            $data = "2";
        }

        return $data;
    }

    private function insertHRNotification($id)
    {
        $link = "/V4/14-13 Notification Software/ck_viewNotification.php?leaveFormId=" . $id;

        $sql = "INSERT INTO system_notificationdetails 
                (notificationDetail, notificationKey, notificationLink, notificationType)
                VALUES('You have a leave application waiting for approval', '$id', '$link', '38')";
        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function insertSystemNotificationHR($key)
    {
        $targetId = $this->getHRId();

        $sql = "INSERT INTO system_notification
                (notificationId, notificationTarget, targetType)
                VALUES('$key', '$targetId', '2')";
        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function lastNotificationId()
    {
        $sql = "SELECT 
                notificationId 
                FROM system_notificationdetails 
                ORDER BY notificationId DESC 
                LIMIT 1";
        $query = $this->connect()->query($sql);

        if ($result = $query->fetch_assoc()) {
            $id = $result['notificationId'];
        }

        return $id;
    }

    private function getHRId()
    {
        $sql = "SELECT 
                e.idNumber 
                FROM hr_employee e
                LEFT JOIN hr_positions p ON e.position = p.positionId
                WHERE p.positionName = 'HR Staff' AND e.status = 1";
        $query = $this->connect()->query($sql);

        if ($result = $query->fetch_assoc()) {
            $id = $result['idNumber'];
        }

        return $id;
    }

    private function updateNotification($id)
    {
        $sql = '';

        $sql .= "UPDATE system_notification n
                 LEFT JOIN system_notificationdetails s ON s.notificationId = n.notificationId
                 SET n.notificationStatus = 1
                 WHERE s.notificationKey = '$id' AND n.notificationStatus = 0";

        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function updateLeaveForm($decision, $id, $leaveRemarks)
    {
        $sql = "UPDATE system_leaveform
                SET status = '$decision',
                hrRemarks = '$leaveRemarks'
                WHERE listId = '$id'";
        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function getLeaveDates($listId)
    {
        $sql = "SELECT 
                leaveFrom, 
                leaveTo,
                purposeOfLeave 
                FROM system_leaveform 
                WHERE listId = '$listId'";
        $query = $this->connect()->query($sql);

        $data = array();

        while ($result = $query->fetch_assoc()) {
            $data[] = $result;
        }

        return $data;
    }

    private function insertToHR($weekHoliday, $listId, $id, $leaveType, $from, $to, $remarks, $status, $type, $allowance, $flag)
    {
        $startingDateQuery = $from->format("Y-m-d");
        $incremental = 0;
        $weekHoliday = array_reverse($weekHoliday);
        $n = key(array_slice($weekHoliday, -1, 1, true));


        while ($n >= 0) {
            $n--;
            while ($from <= $to) {
                $fromDates = $from->format("Y-m-d");

                if ($fromDates == $weekHoliday[$n] || $from == $to) {

                    if ($from != $to) {
                        $from->modify('-1 day');
                    }
                    $endingDateQuery = $from->format("Y-m-d");
                    $from->modify('+1 day');

                    while ($fromDates == $weekHoliday[$n]) {

                        $from->modify('+1 day');
                        $fromDates = $from->format("Y-m-d");
                        $n--;
                    }


                    if ($endingDateQuery != $weekHoliday[$n - 1]) {
                        $sql = "INSERT INTO hr_leave 
                            (employeeId, listId, leaveType, leaveDate, leaveDateUntil, leaveRemarks, status, type, transpoAllowance, quarantineFlag)
                            VALUES ('$id', '$listId', '$leaveType', '$startingDateQuery', '$endingDateQuery', '$remarks', '$status', '$type', '$allowance', '$flag')";
                        $query = $this->connect()->query($sql);
                        // echo $sql;
                    }
                    $startingDateQuery = $from->format("Y-m-d");
                } else {
                    $from->modify('+1 day');
                }
            }
        }


        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function getHolidayDay($monthNum, $year, $to, $toYear, $startDate, $endDate)
    {
        $sql = "SELECT holidayDate AS holidayDay 
			    FROM hr_holiday 
			    WHERE MONTH(holidayDate) IN ('$monthNum', '$to') AND YEAR(holidayDate) IN ('$year', '$toYear')
			    ORDER BY holidayDay";
        $query = $this->connect()->query($sql);

        $data = array();

        while ($result = $query->fetch_assoc()) {
            $data[] = $result['holidayDay'];
        }

        $startDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);

        while ($startDate <= $endDate) {
            if ($startDate->format("D") == "Sun") {
                array_push($data, $startDate->format("Y-m-d"));
            }
            $startDate->modify('+1 day');
        }

        $data = array_unique($data);
        sort($data);

        return $data;
    }

    public function updateHR($decision, $leaveType, $leaveRemarks, $status, $type, $transpoAllowance, $quarantine, $empId, $listId)
    {
        $data = $this->getLeaveDates($listId);

        $from = $data[0]['leaveFrom'];
        $to = $data[0]['leaveTo'];
        $purpose = $data[0]['purposeOfLeave'];
        $fromDay = date('d', strtotime($from));
        $fromMonthNum = date("m", strtotime($from));
        $fromYear = date("Y", strtotime($from));
        $toDay = date('d', strtotime($to));
        $toMonthNum = date("m", strtotime($to));
        $toYear = date("Y", strtotime($to));

        $startDate = new DateTime($fromYear . '-' . $fromMonthNum . '-' . $fromDay);
        $endDate = new DateTime($toYear . '-' . $toMonthNum . '-' . $toDay);

        $holidayDate = $this->getHolidayDay($fromMonthNum, $fromYear, $toMonthNum, $toYear, $from, $to);

        //             	$test = $this->insertToHR($holidayDate, $empId, $leaveType, $startDate, $endDate, $purpose, $status, $type, $transpoAllowance, $quarantine);

        //             	print_r($test);

        if ($this->updateLeaveForm($decision, $listId, $leaveRemarks)) {
            if ($decision == 3) {
                if ($this->insertToHR($holidayDate, $listId, $empId, $leaveType, $startDate, $endDate, $purpose, $status, $type, $transpoAllowance, $quarantine)) {
                    if ($this->updateNotification($listId)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                if ($this->updateNotification($listId)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function getNotificationType($id)
    {
        $sql = "SELECT departmentId, sectionId FROM hr_employee WHERE idNumber LIKE '" . $id . "'";
        $queryRose = $this->connect()->query($sql);
        if ($queryRose and $queryRose->num_rows > 0) {
            $resultRose = $queryRose->fetch_assoc();
            $rose_departmentId = $resultRose['departmentId'];
            $rose_sectionId = $resultRose['sectionId'];
        }
        // $sql = "SELECT 
        // t.listId,
        // t.notificationName,
        // d.notificationType, 
        // COUNT(d.notificationKey) AS typeCount
        // FROM system_notificationdetails d
        // LEFT JOIN system_notificationtype t ON t.listId = d.notificationType
        // LEFT JOIN system_notification n ON n.notificationId = d.notificationId
        // WHERE notificationTarget = '$id' AND n.notificationStatus = 0
        // GROUP BY notificationName";
        // $query = $this->connect()->query($sql);
        $sql = "SELECT 
                t.listId,
                t.notificationName,
                d.notificationType, 
                COUNT(d.notificationKey) AS typeCount
                FROM system_notificationdetails d
                LEFT JOIN system_notificationtype t ON t.listId = d.notificationType
                LEFT JOIN system_notification n ON n.notificationId = d.notificationId
                WHERE ((notificationTarget = '$id' AND targetType=2) OR (notificationTarget = '$rose_sectionId' AND targetType=1) OR (notificationTarget = '$rose_departmentId' AND targetType=0)) AND n.notificationStatus = 0
                GROUP BY notificationName";
        $query = $this->connect()->query($sql);

        return $query;
    }
    // CK Code End 	
}
