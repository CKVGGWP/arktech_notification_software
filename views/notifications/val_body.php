<?php include('controllers/ck_notificationController.php'); ?>
<main>
    <div class="d-flex flex-column justify-content-center p-3">
        <div class="col-md-12 mt-3 px-4">
         <div class="pb-3">
                <a href="../../index.php" class="btn btn-outline-light text-dark"><i class="fas fa-angle-left"></i> Back</a>
            </div>
            <!------------------- DataTables Example ----------------->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                   <?php if (!empty($notificationType)) : ?>
                        <?php if (mysqli_num_rows($notificationType) > 0) : ?>
                            <div class="card-header py-3 text-center">
                                <div class="slider-area">
                                    <div class="container">
                                        <div class="slider-btn-two">
                                            <div class="next-two"><i class="fa-solid fa-angles-right"></i></div>
                                        </div>
                                        <div class="slider-list multiple-slides">
                                            <button type="button" class="btn btn-outline-dark mx-3 filterButtons" value="">All <br> <?php echo $countAllNotification; ?></button>
                                            <?php while ($row = mysqli_fetch_assoc($notificationType)) : ?>
                                                <button type="button" class="btn btn-outline-success mx-3 filterButtons" value="<?php echo $row['listId'] ?>"> <?php echo $row['notificationName']; ?> <br> <?php echo $row['typeCount']; ?> </button>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover text-center" id="userTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Notification ID</th>
                                    <th>Details</th>
                                    <th>Key</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>