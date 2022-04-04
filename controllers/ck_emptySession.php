<?php

if (empty($_SESSION['idNumber'])) :
    header("Location: ../../val_login.php");
    exit();
endif;
