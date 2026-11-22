document.addEventListener("DOMContentLoaded", function () {
  var sidebar = document.getElementById("sidebar");
  var dropdown = document.getElementById("notifDrop");
  var menuBtn = document.getElementById("menuBtn");
  var notifBtn = document.getElementById("notifBtn");

  // mobile menu
  if (menuBtn && sidebar) {
    menuBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      sidebar.classList.toggle("open");
    });
  }

  // notification dropdown
  if (notifBtn && dropdown) {
    notifBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });
  }

  // click anywhere else closes them
  document.addEventListener("click", function () {
    if (sidebar) sidebar.classList.remove("open");
    if (dropdown) dropdown.classList.remove("show");
  });

    // month drop down sends the form
    var selects = document.querySelectorAll('select[data-autosubmit]');
    for (var i = 0; i < selects.length; i++) {
        selects[i].addEventListener('change', function () {
            this.form.submit();
        });
    }

  // hide messages after 4 seconds
  setTimeout(function () {
    var alerts = document.querySelectorAll(".alert");
    for (var i = 0; i < alerts.length; i++) {
      alerts[i].style.display = "none";
    }
  }, 4000);
});

// notification dropdown
document.getElementById('notifBtn').addEventListener('click', function (e) {
    e.stopPropagation();
    dropdown.classList.toggle('show');
});
