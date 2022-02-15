$(document).ready(function () {
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();

    let username = $("#userName").val();
    let password = $("#userPassWord").val();

    if (username == "" || password == "") {
      $("#loginError").html("Please enter your username and password");
    } else {
      $.ajax({
        url: "controllers/ck_loginController.php",
        type: "POST",
        data: { login: true, username: username, password: password },
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
            window.location.href = "index.php?title=Dashboard";
          } else if (response == "2") {
            $("#loginError").html("Username is incorrect");
          } else if (response == "3") {
            $("#loginError").html("Password is incorrect");
          }
        },
      });
    }
  });
});
