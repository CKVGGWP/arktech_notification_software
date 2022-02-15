<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Start of Head -->

    <?php include('views/includes/head/head.php'); ?>
    <?php include('controllers/ck_emptySession.php'); ?>

    <!-- End of Head -->
</head>

<body>

    <!-- Start of Header -->

    <?php include('views/includes/header/charise_header.php'); ?>

    <!-- End of Header -->

 <!-- Start of Sidebar -->

    <div class="container-fluid">
        <div class="row">
            <?php include('views/includes/sidebar/charise_sideBar.php'); ?>

            <!-- End of Sidebar -->

    <!-- Start of index -->

    <?php include('views/index/val_body.php'); ?>

    <!-- End of index -->

    <!-- Start of Footer -->

    <?php include('views/includes/footer/footer.php'); ?>

    <!-- End of Footer -->
</body>

</html>