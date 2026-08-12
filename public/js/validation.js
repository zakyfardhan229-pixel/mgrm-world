(function () {
    'use strict';

    var FILE_SIZE_LIMIT = 2 * 1024 * 1024;
    var ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    function showError(input, message) {
        var error = input.closest('div').querySelector('[data-validation-error]');
        if (!error) {
            error = document.createElement('p');
            error.setAttribute('data-validation-error', '');
            error.className = 'mt-1 text-xs font-semibold text-red-600';
            input.closest('div').appendChild(error);
        }
        error.textContent = message;
        input.classList.add('border-red-400');
    }

    function clearError(input) {
        var error = input.closest('div').querySelector('[data-validation-error]');
        if (error) {
            error.remove();
        }
        input.classList.remove('border-red-400');
    }

    function validateField(input) {
        clearError(input);

        var value = input.value.trim();
        var label = input.getAttribute('data-label') || input.name || 'Kolom ini';

        if (input.hasAttribute('data-required') && value === '') {
            showError(input, label + ' wajib diisi.');
            return false;
        }

        if (input.hasAttribute('data-number') && value !== '') {
            if (isNaN(Number(value))) {
                showError(input, label + ' harus berupa angka.');
                return false;
            }
            var min = parseFloat(input.getAttribute('data-min'));
            var max = parseFloat(input.getAttribute('data-max'));
            if (input.hasAttribute('data-min') && Number(value) < min) {
                showError(input, label + ' minimal ' + min + '.');
                return false;
            }
            if (input.hasAttribute('data-max') && Number(value) > max) {
                showError(input, label + ' maksimal ' + max + '.');
                return false;
            }
        }

        var pattern = input.getAttribute('data-pattern');
        if (pattern && value !== '') {
            var regex = new RegExp(pattern);
            if (!regex.test(value)) {
                showError(input, input.getAttribute('data-pattern-message') || label + ' tidak valid.');
                return false;
            }
        }

        var maxLength = input.getAttribute('data-max');
        if (maxLength && value.length > parseInt(maxLength, 10)) {
            showError(input, label + ' maksimal ' + maxLength + ' karakter.');
            return false;
        }

        if (input.hasAttribute('data-file') && input.files.length > 0) {
            var file = input.files[0];
            var extension = file.name.split('.').pop().toLowerCase();
            if (ALLOWED_EXTENSIONS.indexOf(extension) === -1 || !file.type.startsWith('image/')) {
                showError(input, input.getAttribute('data-file-message') || 'Format file tidak diizinkan.');
                return false;
            }
            if (file.size > FILE_SIZE_LIMIT) {
                showError(input, 'Ukuran file maksimal 2MB.');
                return false;
            }
        }

        return true;
    }

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form.hasAttribute('data-validate')) {
            return;
        }

        var valid = true;
        var fields = form.querySelectorAll('[data-required], [data-number], [data-pattern], [data-max], [data-file]');
        fields.forEach(function (field) {
            if (!validateField(field)) {
                valid = false;
            }
        });

        if (!valid) {
            event.preventDefault();
            var firstInvalid = form.querySelector('[data-validation-error]');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
})();