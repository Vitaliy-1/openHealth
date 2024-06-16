document.addEventListener('DOMContentLoaded', function() {
    //! Contact form elements
    const form = document.getElementById('consultation-form');
    const successModal = document.getElementById('successModal');
    const closeModalButton = document.getElementById('closeModal');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    //! Clear error messages when the form is submitted
    const clearErrorMessages = function(selector) {
        document.querySelectorAll(selector).forEach(element => element.remove());
    }

    //! Show error messages
    const showErrorMessages = function(errors) {
        for (let field in errors) {
            const inputElement = form.querySelector(`[name="${field}"]`);
            if (inputElement) {
                clearErrorMessages(`.error-${field}`);
                errors[field].forEach(error => {
                    if (field === '_token') {
                        alert(error);
                    } else {
                        const errorElement = document.createElement('p');
                        errorElement.textContent = error;
                        errorElement.classList.add(`error-${field}`, 'text-sm', 'text-danger', 'error-message');
                        inputElement.insertAdjacentElement('afterend', errorElement);
                    }
                });
            }
        }
    }

    //! Handle form submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            clearErrorMessages('.error-message');

            if (data.errors) {
                showErrorMessages(data.errors);
            }

            //* Handle successful response
            if (data.success) {
                form.reset();
                successModal.classList.remove('hidden');
            }

            if (!data.success && !data.errors) {
                console.log(data);
                alert(data.message);
            }
        })
        .catch(error => {
            // Handle error response
            console.error(error);
            alert(error.message);
        });
    });

    //! Close modal when the close button is clicked
    closeModalButton.addEventListener('click', function() {
        successModal.classList.add('hidden');
    });
});
