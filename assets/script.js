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


// popup forms and delete confirmation
document.addEventListener('DOMContentLoaded', function () {

    // buttons with data-modal open a popup form
    // data-fill   = JSON of a saved row (edit mode)
    // data-preset = default values only (add mode)
    var buttons = document.querySelectorAll('[data-modal]');
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener('click', openModal);
    }

    function openModal() {
        var modal = document.getElementById(this.dataset.modal);
        var form = modal.querySelector('form');
        form.reset();

        var data = {};
        var editing = false;

        if (this.dataset.fill) {
            data = JSON.parse(this.dataset.fill);
            editing = true;
        } else if (this.dataset.preset) {
            data = JSON.parse(this.dataset.preset);
        }

        for (var key in data) {
            if (form.elements[key] && data[key] !== null) {
                form.elements[key].value = data[key];
            }

            var spans = modal.querySelectorAll('[data-bind=' + key + ']');
            for (var j = 0; j < spans.length; j++) {
                spans[j].textContent = data[key];
            }
        }

        if (editing) {
            modal.classList.add('editing');
        } else {
            modal.classList.remove('editing');
        }

        if (modal.dataset.title) {
            var prefix = editing ? 'Edit ' : 'Add ';
            modal.querySelector('.modal-title').textContent = prefix + modal.dataset.title;
        }

        modal.classList.add('show');
    }

    // close popups
    var modals = document.querySelectorAll('.modal');
    for (var i = 0; i < modals.length; i++) {
        modals[i].addEventListener('click', function (e) {
            if (e.target == this || e.target.hasAttribute('data-close')) {
                this.classList.remove('show');
            }
        });
    }

    // ask before deleting
    var forms = document.querySelectorAll('form[data-confirm]');
    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    }
    });
