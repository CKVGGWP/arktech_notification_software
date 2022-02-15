// Server-side processing with object sourced data
$(document).ready(function () {
  var dataTable = $("#userTable").DataTable({
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
});

$(document).on("click", ".employees", function () {
  let employeeNumber = $(this).data("employee").employeeNumber;
  let employeeName = $(this).data("employee").employeeName;
  let designation = $(this).data("employee").designation;
  let department = $(this).data("employee").department;
  let purposeOfLeave = $(this).data("employee").purposeOfLeave;
  let leaveFrom = $(this).data("employee").leaveFrom;
  let leaveTo = $(this).data("employee").leaveTo;
  let listId = $(this).data("employee").listId;

  $("#employeeNumber").val(employeeNumber);
  $("#employeeName").val(employeeName);
  $("#designation").val(designation);
  $("#department").val(department);
  $("#purposeofLeave").val(purposeOfLeave);
  $("#leaveFrom").val(leaveFrom);
  $("#leaveTo").val(leaveTo);
  $("#listId").val(listId);
});

// CK Start of Code

$(document).on("click", "#approve", function () {
  let listId = $("#listId").val();
  let reasonForApproval = $("#reasonForApproval").val();
  let leaveFrom = $("#leaveFrom").val();
  let leaveTo = $("#leaveTo").val();

  if (reasonForApproval == "") {
    $("#reasonForApproval").addClass("is-invalid");
    $("#reasonForApproval").focus();
    return false;
  } else {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: {
        approve: 1,
        listId: listId,
        reasonForApproval: reasonForApproval,
        leaveFrom: leaveFrom,
        leaveTo: leaveTo,
      },
      success: function (response) {
        Swal.fire({
          title: "Success",
          text: "Leave Approved!",
          icon: "success",
        }).then((result) => {
          location.reload();
        });
      },
    });
  }
});

$(document).on("click", "#disapprove", function () {
  let listId = $("#listId").val();
  let reasonForDisapproval = $("#reasonForDisapproval").val();

  if (reasonForDisapproval == "") {
    $("#reasonForDisapproval").addClass("is-invalid");
    $("#reasonForDisapproval").focus();
    return false;
  } else {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: {
        disapprove: 1,
        listId: listId,
        reasonForDisapproval: reasonForDisapproval,
      },
      success: function (response) {
        Swal.fire({
          title: "Success",
          text: "Leave Disapproved!",
          icon: "success",
        }).then((result) => {
          location.reload();
        });
      },
    });
  }
});

$(document).on("click", ".hr", function () {
  let empNum = $(this).data("employee").employeeNumber;
  let empName = $(this).data("employee").employeeName;
  let des = $(this).data("employee").designation;
  let dept = $(this).data("employee").department;
  let purpose = $(this).data("employee").purposeOfLeave;
  let from = $(this).data("employee").leaveFrom;
  let to = $(this).data("employee").leaveTo;
  let list = $(this).data("employee").listId;
  let reason = $(this).data("employee").reasonOfSuperior;
  let date = $(this).data("employee").date;

  $("#empNum").val(empNum);
  $("#empName").val(empName);
  $("#des").val(des);
  $("#dept").val(dept);
  $("#purpose").val(purpose);
  $("#from").val(from);
  $("#to").val(to);
  $("#list").val(list);
  $("#reasonOfApproval").val(reason);
  $("#dateOfApproval").val(date);

  $(".titleName").text(empName + " Leave Details");
});

$(document).on("click", "#setStatusBTN", function () {
  let leaveType = $("#leaveType").val();
  let leaveRemarks = $("#leaveRemarks").val();
  let status = $("#status").val();
  let type = $("#type").val();
  let transpoAllowance = $("#transpoAllowance").val();
  let quarantine = $("#quarantine").val();
  let newEmpNum = $("#empNum").val();

  if (leaveRemarks == "") {
    $("#leaveRemarks").addClass("is-invalid");
    $("#leaveRemarks").focus();
    return false;
  } else {
    $.ajax({
      url: "controllers/ck_newNotificationController.php",
      type: "POST",
      data: {
        setStatus: 1,
        leaveType: leaveType,
        leaveRemarks: leaveRemarks,
        status: status,
        type: type,
        transpoAllowance: transpoAllowance,
        quarantine: quarantine,
        newEmpNum: newEmpNum,
      },
      success: function (response) {
        Swal.fire({
          title: "Success",
          text: "Leave Status has been set!",
          icon: "success",
        }).then((result) => {
          location.reload();
        });
      },
    });
  }
});

// CK End of Code
