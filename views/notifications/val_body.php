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
                    <table class="table table-bordered text-center" id="userTable" width="100%" cellspacing="0">
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

    <!-- Modal -->

    <?php if ($notification->getPosition($_SESSION['userID']) != "HR Staff") : ?>

        <!-- View Modal For Leave Form -->

        <div class="modal fade" id="viewLeaveModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" id="viewLeaveDetails">

            </div>
        </div>

    <?php else : ?>

        <!-- View HR Modal -->
        <div class="modal fade" id="viewHRModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" id="viewHRDetails">

            </div>
        </div>

    <?php endif; ?>
</main>