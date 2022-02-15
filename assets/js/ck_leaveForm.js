$(document).ready(function () {
  checkHolidays();

  function checkHolidays() {
    $.ajax({
      url: "controllers/ck_holidayController.php",
      type: "GET",
      data: { calendar: 1 },
      success: function (data) {
        let parsed = JSON.parse(data);

        $(".calendar").flatpickr({
          altInput: true,
          altFormat: "F j, Y",
          dateFormat: "Y-m-d",
          minDate: new Date().fp_incr(1),
          disable: parsed,
          locale: {
            firstDayOfWeek: 1, // start week on Monday
          },
        });
      },
    });
  }

//   $("#sig").signature();

//   let sig = $("#sig").signature({
//     syncField: "#signature",
//     syncFormat: "PNG",
//   });

//   $("#clearSig").on("click", function (e) {
//     e.preventDefault();
//     sig.signature("clear");
//     $("#signature").val("");
//   });

  $("#leaveForm").on("submit", function (e) {
    e.preventDefault();

    let employee_active = $("#employee_active").val();
    let leaveFrom = $("#leaveFrom").val();
    let leaveTo = $("#leaveTo").val();
    let purpose = $("#purpose").val();
    // let signature = $("#sig").signature("toJSON");

    if (leaveFrom == "") {
      Swal.fire({
        icon: "error",
        title: "Leave From is Empty!",
        text: "Please select a starting date!",
      });
    } else if (leaveTo == "") {
      Swal.fire({
        icon: "error",
        title: "Leave To is Empty!",
        text: "Please select an ending date!",
      });
    } else if (leaveFrom > leaveTo) {
      Swal.fire({
        icon: "info",
        title: "Invalid Date!",
        text: "Leave To cannot be before Leave From!",
      });
    } else if (purpose == "") {
      Swal.fire({
        icon: "error",
        title: "Purpose of Leave is Empty!",
        text: "Please enter a purpose of leave!",
      });
    } else {
      $.ajax({
        url: "controllers/ck_holidayController.php",
        type: "POST",
        data: {
          leave: 1,
          employee_active: employee_active,
          leaveFrom: leaveFrom,
          leaveTo: leaveTo,
          purpose: purpose,
          // signature: signature,
        },
        beforeSend: function () {
          $("#blur").addClass("blur-active");
          $(".preloader").show();
        },
        complete: function () {
          $("#blur").removeClass("blur-active");
          $(".preloader").hide();
        },
        success: function (response) {
          if (response == "1") {
            Swal.fire({
              icon: "success",
              title: "Leave Form Submitted!",
              text: "Please wait for the status of your leave request.",
            });
            $("#leaveForm")[0].reset();
          } else if (response == "2") {
            Swal.fire({
              icon: "error",
              title: "Something went wrong!",
              text: "There is a problem with the server! Please try again later!",
            });
          }
        },
      });
    }
  });
});
