<?php include('controllers/ck_notificationController.php'); ?>
<main class="container">
    <div class="d-flex flex-column justify-content-center p-3">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <a class="btn btn-outline-light text-dark" href="val_notifications.php?title=Notification" role="button"><i class="fa-solid fa-chevron-left"></i></i> Back</a>
            </div>
            <input type="text" id="hiddenId" value="<?php echo $leaveId; ?>">
            <div class="card-body">
                <?php if ($position == "HR Staff") : ?>
                    <div class="card-body" id="viewHRDetails">

                    </div>
                <?php else : ?>
                    <div class="card-body" id="viewLeaveDetails">

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>