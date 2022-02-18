<?php

class Notifications extends Database
{
    // CK Code Start

    public function getTable($notificationType = '', $userId)
    {
        $query = "SELECT 
                  d.notificationId,
                  d.notificationDetail,
                  d.notificationKey,
                  d.notificationLink,
                  d.notificationType 
                  FROM system_notificationdetails d
                  LEFT JOIN system_notification n ON n.notificationId = d.notificationId";

        if ($notificationType != "") {
            $query .= " WHERE notificationType = '$notificationType' AND notificationTarget = '$userId' AND notificationStatus = '0'";
        } else {
            $query .= " WHERE notificationTarget = '$userId' AND notificationStatus = '0'";
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

                if (file_exists('../../../' . $newFile)) {

                    $button .= '<a href="../..' . $notificationLink . '&title=Notification" class="btn btn-warning">
  						        Redirect
					            </a>';
                } else {
                    $button .= '<a href="error.php" class="btn btn-danger">
  						        Not Found
					            </a>';
                }

                $data[] = [
                    $notificationId,
                    $notificationDetail,
                    $notificationKey,
                    $button,
                ];
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

    public function createLeaveModal($notificationKey)
    {
        $html = '';

        $data = $this->getLeaveForm($notificationKey);

        foreach ($data as $key => $result) :

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
                    </div>
                    <div class="form-group mb-2">
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
                    </div>
                    <div class="form-group mb-2">
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
                                <label class="col-sm-6">Date of Approval</label>
                                <input readonly class="form-control" id="dateOfApproval" name="dateOfApproval" value="' . $newResult['date'] . '"></input>
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
            $sql .= " WHERE notificationTarget = '$HRId'";
        } else {
            $sql .= " WHERE notificationTarget = '" . $_SESSION['userID'] . "'";
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

    public function leaveFormApproval($id, $status, $remarks)
    {
        $sql = '';
        $data = '';
        if ($status == "disapprove") {
            $sql .= "UPDATE system_leaveform SET status = 4, reasonOfSuperior = '$remarks', date = NOW() WHERE listId = '$id'";
        } else if (
            $status == "approve"
        ) {
            $sql .= "UPDATE system_leaveform SET status = 2, reasonOfSuperior = '$remarks', date = NOW() WHERE listId = '$id'";
        }

        $query = $this->connect()->query($sql);

        if ($query) {
            if ($this->updateNotification($id)) {
                if ($status == "approve") {
                    if ($this->insertHRNotification()) {
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

    private function insertHRNotification()
    {
        $leaveId = $this->leaveFormId();

        $link = "/V4/14-13 Notification Software/ck_viewNotification.php?leaveFormId=" . $leaveId;

        $sql = "INSERT INTO system_notificationdetails 
                (notificationDetail, notificationKey, notificationLink, notificationType)
                VALUES('You have a leave application waiting for approval', '$leaveId', '$link', '38')";
        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function leaveFormId()
    {
        $sql = "SELECT 
                listId 
                FROM system_leaveform 
                ORDER BY listId DESC 
                LIMIT 1";
        $query = $this->connect()->query($sql);

        if ($result = $query->fetch_assoc()) {
            $id = $result['listId'];
        }

        return $id;
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
                WHERE p.positionName = 'HR Staff'";
        $query = $this->connect()->query($sql);

        if ($result = $query->fetch_assoc()) {
            $id = $result['idNumber'];
        }

        return $id;
    }

    private function updateNotification($id)
    {
        $sql = "UPDATE system_notification s
                LEFT JOIN system_notificationdetails n ON s.notificationId = n.notificationId
                SET s.notificationStatus = 1 
                WHERE n.notificationKey = '$id'";
        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function insertToHR($id, $leaveType, $from, $to, $remarks, $status, $type, $allowance, $flag)
    {
        $sql = "INSERT INTO hr_leave 
                (employeeId, leaveType, leaveDate, leaveDateUntil, leaveRemarks, status, type, transpoAllowance, quarantineFlag)
                VALUES ('$id', '$leaveType', '$from', '$to', '$remarks', '$status', '$type', '$allowance', '$flag')";
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
                WHERE employeeNumber = '$id'";
        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    private function getLeaveDates()
    {
        $sql = "SELECT 
                leaveFrom, 
                leaveTo,
                purposeOfLeave 
                FROM system_leaveform 
                ORDER BY listId DESC 
                LIMIT 1";
        $query = $this->connect()->query($sql);

        $data = array();

        while ($result = $query->fetch_assoc()) {
            $data[] = $result;
        }

        return $data;
    }

    public function updateHR($decision, $leaveType, $leaveRemarks, $status, $type, $transpoAllowance, $quarantine, $empId)
    {
        $newKey = $this->leaveFormId();

        $data = $this->getLeaveDates();

        $from = $data[0]['leaveFrom'];
        $to = $data[0]['leaveTo'];
        $purpose = $data[0]['purposeOfLeave'];

        if ($this->updateLeaveForm($decision, $empId, $leaveRemarks)) {
            if ($decision == 3) {
                if ($this->insertToHR($empId, $leaveType, $from, $to, $purpose, $status, $type, $transpoAllowance, $quarantine)) {
                    if ($this->updateNotification($newKey)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                if ($this->updateNotification($newKey)) {
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
        $sql = "SELECT 
                t.listId,
                t.notificationName,
                d.notificationType, 
                COUNT(DISTINCT(d.notificationKey)) AS typeCount
                FROM system_notificationdetails d
                LEFT JOIN system_notificationtype t ON t.listId = d.notificationType
                LEFT JOIN system_notification n ON n.notificationId = d.notificationId
                WHERE notificationTarget = '$id' AND n.notificationStatus = 0
                GROUP BY notificationName, d.notificationKey";
        $query = $this->connect()->query($sql);

        return $query;
    }

    // CK Code End 	

}
