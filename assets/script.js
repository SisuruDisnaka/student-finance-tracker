document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var dropdown = document.getElementById('notifDrop');

    document.getElementById('menuBtn').addEventListener('click', function (e) {
        e.stopPropagation();
        sidebar.classList.toggle('open');
    });

    document.getElementById('notifBtn').addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
    });

    document.addEventListener('click', function () {
        sidebar.classList.remove('open');
        dropdown.classList.remove('show');
    });

    var selects = document.querySelectorAll('select[data-autosubmit]');
    for (var i = 0; i < selects.length; i++) {
        selects[i].addEventListener('change', function () {
            this.form.submit();
        });
    }

    setTimeout(function () {
        var alerts = document.querySelectorAll('.alert');
        for (var i = 0; i < alerts.length; i++) {
            alerts[i].style.display = 'none';
        }
    }, 4000);
});

document.addEventListener('DOMContentLoaded', function () {

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

    var modals = document.querySelectorAll('.modal');
    for (var i = 0; i < modals.length; i++) {
        modals[i].addEventListener('click', function (e) {
            if (e.target == this || e.target.hasAttribute('data-close')) {
                this.classList.remove('show');
            }
        });
    }

    var forms = document.querySelectorAll('form[data-confirm]');
    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    }
});
