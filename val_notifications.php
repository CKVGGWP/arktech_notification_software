<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Start of Head -->

    <?php include('views/includes/head/head.php'); ?>
    <?php include('controllers/ck_emptySession.php'); ?>
	<link rel="stylesheet" href="assets/css/slick.css">
    <script src="assets/js/slick.min.js"></script>
	<!-- DISABLE METHOD-->
    <style>
    	div.dataTables_paginate {
			display:none;
		}
    </style>

    <!-- End of Head -->
</head>

<body>
    <!-- Start of index -->

    <?php include('views/notifications/val_body.php'); ?>

    <!-- End of index -->

    <!-- Start of Footer -->

    <?php include('views/includes/footer/footer.php'); ?>
    <script src="assets/js/val_notifications.js"></script>

    <!-- End of Footer -->
</body>

</html>