<?php

class Login extends Database
{
    private $username;
    private $password;

    function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function login()
    {
        if ($this->checkUser() == false) {
            echo "2";
            exit();
        }

        if ($this->checkPass() == false) {
            echo "3";
            exit();
        }

        if ($this->getSessionID()) {
            echo "1";
        }

        return true;
    }

    private function checkUser()
    {
        $sql = "SELECT * FROM hr_employee WHERE userName = '$this->username'";
        $result = $this->connect()->query($sql);

        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function checkPass()
    {
        $sql = "SELECT * FROM hr_employee WHERE userName = '$this->username' AND userPassword = '$this->password'";
        $result = $this->connect()->query($sql);

        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function getSessionID()
    {
        $sql = "SELECT * FROM hr_employee WHERE userName = '$this->username' AND userPassword = '$this->password'";
        $result = $this->connect()->query($sql);

        if ($row = $result->fetch_assoc()) {
            $id = $row['idNumber'];
        }

        return $id;
    }

    public function setSessionID()
    {
        $id = $this->getSessionID();
        $_SESSION['userID'] = $id;

        return $_SESSION['userID'];
    }
}
