// Server-side processing with object sourced data
$(document).ready(function () {
  getDataTable();

  function getDataTable() {
    let dataTable = $("#userTable").DataTable({
      lengthChange: false,
      searching: false,
      processing: true,
      ordering: false,
      serverSide: true,
      bInfo: false,
      ajax: {
        url: "controllers/val_notificationsController.php", // json datasource
        type: "POST", // method  , by default get
        /*
	  success: function (row, data, index) {
	  console.log(row);
	  },
      */

        error: function () {
          // error handling
        },
      },
      createdRow: function (row, data, index) {},
      columnDefs: [],
      fixedColumns: false,
      deferRender: true,
      scrollY: 500,
      scrollX: false,
      scroller: {
        loadingIndicator: true,
      },
      stateSave: false,
    });
  }

  $(".filterButtons").on("click", function () {
    let notificationType = $(this).val();

    $("#userTable").DataTable().clear().destroy();
    $("#userTable").DataTable().destroy();

    let dataTable = $("#userTable").DataTable({
      lengthChange: false,
      searching: false,
      processing: true,
      ordering: false,
      serverSide: true,
      bInfo: false,
      // Get the data from the controller
      ajax: {
        url: "controllers/val_notificationsController.php", // json datasource
        type: "POST", // method , by default get
        data: { newType: 1, notificationType: notificationType },
        error: function () {
          // error handling
        },
      },
      createdRow: function (row, data, index) {},
      columnDefs: [],
      fixedColumns: false,
      deferRender: true,
      scrollY: 500,
      scrollX: false,
      scroller: {
        loadingIndicator: true,
      },
      stateSave: false,
    });
  });
});

// CK Start of Code

$(document).ready(function () {
  let hiddenId = $("#hiddenId").val();
  if ($("#viewLeaveDetails").length == 0) {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: { modal2: 1, hiddenId: hiddenId },
      dataType: "json",
      success: function (data) {
        $("#viewHRDetails").html(data);
      },
    });
  } else if ($("#viewHRDetails").length == 0) {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: { modal: 1, hiddenId: hiddenId },
      dataType: "json",
      success: function (data) {
        $("#viewLeaveDetails").html(data);
      },
    });
  } else {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: { modal3: 1, hiddenId: hiddenId },
      dataType: "json",
      success: function (data) {
        $("#viewLeaderDetails").html(data);
      },
    });
  }
});

$(document).ready(function () {
  $("#submitApproval").prop("disabled", true);

  $("#setStatusBTN").prop("disabled", true);

  $("#submitLeaderApproval").prop("disabled", true);
});

$(document).on("change", "#decisionOfLeader", function () {
  let des = $(this).val();

  if (des == "approve") {
    $("#approvalLeader").removeClass("d-none");
    $("#disapprovalLeader").addClass("d-none");
    $("#submitLeaderApproval").prop("disabled", false);
  } else if (des == "disapprove") {
    $("#approvalLeader").addClass("d-none");
    $("#disapprovalLeader").removeClass("d-none");
    $("#submitLeaderApproval").prop("disabled", false);
  } else {
    $("#approvalLeader").addClass("d-none");
    $("#disapprovalLeader").addClass("d-none");
    $("#submitLeaderApproval").prop("disabled", true);
  }
});

$(document).on("click", "#submitLeaderApproval", function () {
  let listId = $("#listId").val();
  let decisionOfLeader = $("#decisionOfLeader").val();
  let leaderRemark = "";

  if (decisionOfLeader == "approve") {
    let approvalLeaderRemarks = $("#approvalLeaderRemarks").val();
    leaderRemark = approvalLeaderRemarks;
  } else if (decisionOfLeader == "disapprove") {
    let disapprovalLeaderRemarks = $("#disapprovalLeaderRemarks").val();
    leaderRemark = disapprovalLeaderRemarks;
  }

  if (leaderRemark == "") {
    $("#remarkLeaderClass").addClass("is-invalid");
    $("#remarkLeaderClass").focus();
    return false;
  } else {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: {
        approveLeader: 1,
        listId: listId,
        decisionOfLeader: decisionOfLeader,
        leaderRemark: leaderRemark,
      },
      success: function (response) {
        Swal.fire({
          title: "Success",
          text: "Leave Has Been Set!",
          icon: "success",
        }).then((result) => {
          window.location.href = "val_notifications.php?title=Notification";
        });
      },
    });
  }
});

$(document).on("change", "#decisionOfHead", function () {
  let des = $(this).val();

  if (des == "approve") {
    $("#approvalHead").removeClass("d-none");
    $("#disapprovalHead").addClass("d-none");
    $("#submitApproval").prop("disabled", false);
  } else if (des == "disapprove") {
    $("#approvalHead").addClass("d-none");
    $("#disapprovalHead").removeClass("d-none");
    $("#submitApproval").prop("disabled", false);
  } else {
    $("#approvalHead").addClass("d-none");
    $("#disapprovalHead").addClass("d-none");
    $("#submitApproval").prop("disabled", true);
  }
});

$(document).on("click", "#submitApproval", function () {
  let listId = $("#listId").val();
  let decisionOfHead = $("#decisionOfHead").val();
  let headRemark = "";

  if (decisionOfHead == "approve") {
    let approvalHeadRemarks = $("#approvalHeadRemarks").val();
    headRemark = approvalHeadRemarks;
  } else if (decisionOfHead == "disapprove") {
    let disapprovalHeadRemarks = $("#disapprovalHeadRemarks").val();
    headRemark = disapprovalHeadRemarks;
  }

  if (headRemark == "") {
    $("#remarkHeadClass").addClass("is-invalid");
    $("#remarkHeadClass").focus();
    return false;
  } else {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: {
        approve: 1,
        listId: listId,
        decisionOfHead: decisionOfHead,
        headRemark: headRemark,
      },
      success: function (response) {
        Swal.fire({
          title: "Success",
          text: "Leave Has Been Set!",
          icon: "success",
        }).then((result) => {
          window.location.href = "val_notifications.php?title=Notification";
        });
      },
    });
  }
});

$(document).on("change", "#decision", function () {
  let decision = $(this).val();

  if (decision == 3) {
    $("#approvalHR").removeClass("d-none");
    $("#disapprovalHR").addClass("d-none");
    $("#setStatusBTN").prop("disabled", false);
  } else if (decision == 4) {
    $("#approvalHR").addClass("d-none");
    $("#disapprovalHR").removeClass("d-none");
    $("#setStatusBTN").prop("disabled", false);
  } else {
    $("#approvalHR").addClass("d-none");
    $("#disapprovalHR").addClass("d-none");
    $("#setStatusBTN").prop("disabled", true);
  }
});

$(document).on("click", "#setStatusBTN", function () {
  let leaveType = $("#leaveType").val() ? $("#leaveType").val() : "";
  let status = $("#status").val() ? $("#status").val() : "";
  let type = $("#type").val() ? $("#type").val() : "";
  let transpoAllowance = $("#transpoAllowance").val()
    ? $("#transpoAllowance").val()
    : "";
  let quarantine = $("#quarantine").val() ? $("#quarantine").val() : "";
  let newEmpNum = $("#empNum").val();
  let decision = $("#decision").val();
  let list = $("#list").val();

  let remarks = "";

  if (decision == 3) {
    let leaveRemarks = $("#leaveRemarks").val();
    remarks = leaveRemarks;
  } else if (decision == 4) {
    let disapprovalRemarks = $("#disapprovalRemarks").val();
    remarks = disapprovalRemarks;
  } else {
    $(this).prop("disabled", true);
    return false;
  }

  if (remarks == "") {
    $(".remarkClass").addClass("is-invalid");
    $(".remarkClass").focus();
    return false;
  } else {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: {
        setStatus: 1,
        decision: decision,
        leaveType: leaveType,
        remarks: remarks,
        status: status,
        type: type,
        transpoAllowance: transpoAllowance,
        quarantine: quarantine,
        newEmpNum: newEmpNum,
        list: list,
      },
      beforeSend: function () {
        $(this).prop("disabled", true);
      },
      complete: function () {
        $(this).prop("disabled", false);
      },
      success: function (response) {
        Swal.fire({
          title: "Success",
          text: "Leave Status has been set!",
          icon: "success",
        }).then((result) => {
          window.location.href = "val_notifications.php?title=Notification";
        });
      },
    });
  }
});

// CK End of Code
