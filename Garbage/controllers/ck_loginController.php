<?php

require("../models/ck_database.php");
require("../models/ckval_login.php");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = new Login($username, $password);

    if ($login->login()) {
        session_start();
        $login->setSessionID();
    }
}
