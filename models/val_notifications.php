<?php

class Notifications extends Database
{
   public function getTable($userID)
    {
        $query = "SELECT 
                n.notificationId, 
                d.notificationDetail, 
                d.notificationKey, 
                l.dateIssued, 
                l.employeeNumber, 
                l.employeeName, 
                l.designation,
                l.department, 
                l.purposeOfLeave, 
                l.leaveFrom, 
                l.leaveTo, 
                l.listId
                FROM `system_notification` n 
                JOIN system_notificationdetails d ON n.notificationId = d.notificationId
                JOIN system_leaveform l ON d.notificationKey = l.listId
                WHERE n.notificationTarget = '$userID' AND l.status = 0";

        $sql = $this->connect()->query($query);
        $data = [];
        $totalData = 0;
        if ($sql->num_rows > 0) {
            while ($result = $sql->fetch_assoc()) {
                extract($result);

                $data[] = [
                    $notificationId,
                    $notificationDetail,
                    $notificationKey,
                    '<button type="button" class="btn btn-warning employees" data-bs-toggle="modal" data-bs-target="#viewModal" id="' . $listId . '"
                    data-employee=\'{"employeeNumber":"' . $employeeNumber . '","employeeName":"' . $employeeName . '","designation":"' . $designation . '",
                    "department":"' . $department . '","purposeOfLeave":"' . $purposeOfLeave . '","leaveFrom":"' . date("F j, Y", strtotime($leaveFrom)) . '",
                    "leaveTo":"' . date("F j, Y", strtotime($leaveTo)) . '", "listId":"' . $listId . '"}\'>
  						View
					</button>',
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

	// CK Code Start

    public function getHRTable()
    {
        $query = "SELECT 
                n.notificationId, 
                d.notificationDetail, 
                d.notificationKey, 
                l.dateIssued, 
                l.employeeNumber, 
                l.employeeName, 
                l.designation,
                l.department, 
                l.purposeOfLeave, 
                l.leaveFrom, 
                l.leaveTo, 
                l.listId, 
                l.reasonOfSuperior, 
                l.date, 
                h.leaveType
                FROM `system_notification` n 
                JOIN system_notificationdetails d ON n.notificationId = d.notificationId
                JOIN system_leaveform l ON d.notificationKey = l.listId
                JOIN hr_leave h ON h.employeeId = l.employeeNumber
                WHERE l.status = 2 AND h.leaveRemarks = '' 
                GROUP BY l.employeeNumber";

        $sql = $this->connect()->query($query);
        $data = [];
        $totalData = 0;
        if ($sql->num_rows > 0) {
            while ($result = $sql->fetch_assoc()) {
                extract($result);

                $data[] = [
                    $notificationId,
                    $notificationDetail,
                    $notificationKey,
                    '<button type="button" class="btn btn-warning hr" data-bs-toggle="modal" data-bs-target="#viewHRModal" id="' . $listId . '"
                    data-employee=\'{"employeeNumber":"' . $employeeNumber . '","employeeName":"' . $employeeName . '","designation":"' . $designation . '",
                    "department":"' . $department . '","purposeOfLeave":"' . $purposeOfLeave . '","leaveFrom":"' . date("F j, Y", strtotime($leaveFrom)) . '",
                    "leaveTo":"' . date("F j, Y", strtotime($leaveTo)) . '", "listId":"' . $listId . '", "reasonOfSuperior":"' . $reasonOfSuperior . '",
                    "date":"' . date("F j, Y", strtotime($date)) . '"}\'>
  						View
					</button>',
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
        $sql = '';

        if ($position == 'HR') {
            $sql .= "SELECT COUNT(leaveId) AS notifCount 
                    FROM hr_leave 
                    WHERE leaveRemarks = ''";
        } else {
            $sql .= "SELECT COUNT(l.listId) AS notifCount 
                    FROM system_leaveform l 
                    LEFT JOIN system_notificationdetails n ON n.notificationKey = l.listId 
                    LEFT JOIN system_notification s ON s.notificationId = n.notificationId 
                    WHERE s.notificationTarget = '" . $_SESSION['userID'] . "' AND l.status = '0' 
                    GROUP BY l.department";
        }

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

    public function leaveFormApproval($id, $status, $remarks, $from = '', $to = '')
    {
        $sql = '';
        $data = '';
        if ($status == "disapprove") {
            $sql .= "UPDATE system_leaveform SET status = 4, reasonOfSuperior = '$remarks', date = NOW() WHERE listId = '$id'";
        } else if ($status == "approve") {
            $sql .= "UPDATE system_leaveform SET status = 2, reasonOfSuperior = '$remarks', date = NOW() WHERE listId = '$id'";
        }

        $query = $this->connect()->query($sql);

        $selectId = "SELECT employeeNumber FROM system_leaveform WHERE listId = '$id'";
        $queryId = $this->connect()->query($selectId);

        if ($queryId->num_rows > 0) {
            while ($result = $queryId->fetch_assoc()) {
                $employeeNumber = $result['employeeNumber'];
            }
        }

        if ($query) {
            if ($this->updateNotification($id)) {
                if ($status == "approve") {
                    if ($this->insertToHR($employeeNumber, $from, $to)) {
                        $data = "1";
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

    private function insertToHR($id, $from, $to)
    {
        $sql = "INSERT INTO hr_leave (employeeId, leaveDate, leaveDateUntil)
                VALUES ('$id', '$from', '$to')";
        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

	public function updateHR($leaveType, $leaveRemarks, $status, $type, $transpoAllowance, $quarantine, $empId)
    {
        $sql = "UPDATE hr_leave h
                LEFT JOIN system_leaveform s ON s.employeeNumber = h.employeeId
                SET h.leaveType = '$leaveType', h.leaveRemarks = '$leaveRemarks', h.status = '$status', 
                h.type = '$type', h.transpoAllowance = '$transpoAllowance', h.quarantineFlag = '$quarantine',
                s.status = '3'
                WHERE h.employeeId = '$empId' AND h.leaveRemarks = ''";
        $query = $this->connect()->query($sql);

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

	public function getNotificationType()
    {
        $sql = "SELECT 
        notificationName, 
        COUNT(notificationId) AS typeCount
        FROM system_notificationdetails
        LEFT JOIN system_notificationtype ON listId = notificationType
        GROUP BY notificationName";
        $query = $this->connect()->query($sql);

        return $query;
    }

	// CK Code End 	

}
