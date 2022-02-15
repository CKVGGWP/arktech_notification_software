<?php

if (empty($_SESSION['userID'])) :
    header("Location: val_login.php");
    exit();
endif;
