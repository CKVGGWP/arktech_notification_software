<?php

class Notifications extends Database
{
    // CK Code Start

    public function getTable($notificationType = '')
    {
        $query = "SELECT 
                  notificationId,
                  notificationDetail,
                  notificationKey  
                  FROM system_notificationdetails";

        if ($notificationType != "") {
            $query .= " WHERE notificationType = '$notificationType'";
        }

        $query .= " ORDER BY notificationId DESC";


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
                    '<button type="button" class="btn btn-warning employees">
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
                t.listId,
                t.notificationName, 
                COUNT(d.notificationId) AS typeCount
                FROM system_notificationdetails d
                LEFT JOIN system_notificationtype t ON t.listId = d.notificationType
                GROUP BY t.notificationName";
        $query = $this->connect()->query($sql);

        return $query;
    }

    // CK Code End 	

}
