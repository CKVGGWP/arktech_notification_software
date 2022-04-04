<?php

session_start();
unset($_SESSION['idNumber']);
session_destroy();

header("Location: ../val_login.php");
