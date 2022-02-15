<?php include('views/includes/header/charise_header.php'); ?>
<?php include('controllers/ck_notificationController.php'); ?>
<main class="container">
    <div class="d-flex flex-column justify-content-center p-3">
        <!------------------- DataTables Example ----------------->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <a class="btn btn-outline-light text-dark" href="index.php" role="button"><i class="fa-solid fa-chevron-left"></i></i> Back</a>
         <?php if (!empty($notificationType)) : ?>
                <?php if (mysqli_num_rows($notificationType) > 0) : ?>
                    <div class="card-header py-3 text-center">
                        <div class="slider-area">
                            <div class="container">
                            <div class="slider-btn-two">
       						 <div class="next-two"><i class="fa-solid fa-angles-right"></i></div>
   							 </div>
                                <div class="slider-list multiple-slides">
                                    <?php while ($row = mysqli_fetch_assoc($notificationType)) : ?>
                                        <button class="btn btn-outline-success mx-3"> <?php echo $row['notificationName']; ?> <br> <?php echo $row['typeCount']; ?> </button>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
           </div>
    </div>
</main>