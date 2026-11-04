$(function () {
    $('.ajax-product-form').on('submit', function (event) {
        event.preventDefault();

        var $form = $(this);
        var $errors = $('.ajax-errors');
        var $submitButton = $form.find('button[type="submit"]');
        var originalButtonText = $submitButton.text();

        $errors.hide().empty();
        $submitButton.prop('disabled', true).text('Saving...');

        $.ajax({
            url: $form.attr('action') || window.location.href,
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.success && response.redirect) {
                    window.location.href = response.redirect;
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON || {};
                var errors = response.errors || ['Something went wrong. Please try again.'];
                var html = '';

                $.each(errors, function (_, error) {
                    html += '<p>' + $('<div>').text(error).html() + '</p>';
                });

                $errors.html(html).show();
            },
            complete: function () {
                $submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    });

    $('.js-delete-button').on('click', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#deleteId').val(id);
        $('#deleteText').text('Delete "' + name + '" permanently?');
        $('#deleteModal').css('display', 'flex');
    });

    $('.js-close-delete').on('click', function () {
        $('#deleteModal').hide();
    });

    $('#deleteModal').on('click', function (event) {
        if (event.target === this) {
            $('#deleteModal').hide();
        }
    });

    $('#deleteForm').on('submit', function (event) {
        event.preventDefault();

        var $form = $(this);
        var id = $('#deleteId').val();
        var $submitButton = $form.find('button[type="submit"]');
        var originalButtonText = $submitButton.text();

        $submitButton.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('tr[data-product-id="' + id + '"]').remove();
                    $('#deleteModal').hide();
                    $('.js-message').text(response.message).show();

                    if ($('#productTableBody tr').length === 0) {
                        $('#productTableBody').html('<tr><td colspan="7" class="empty">No products found.</td></tr>');
                    }
                }
            },
            error: function () {
                $('#deleteModal').hide();
                $('.js-message').text('Could not delete the product. Please try again.').show();
            },
            complete: function () {
                $submitButton.prop('disabled', false).text(originalButtonText);
            }
        });
    });
});
