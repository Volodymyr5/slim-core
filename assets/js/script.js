$(document).ready(function () {
    // Link to modal
    $('body').on('click', 'a[data-target]', function (event) {
        var allowedTarget = [
            'lg-modal',
            'sm-modal'
        ];
        var target = $(this).attr('data-target').toLowerCase();

        if (typeof target == 'string' && $.inArray(target, allowedTarget) >= 0) {
            event.preventDefault();

            $('#'+target+' #modal-content').load($(this).attr('href'));
            UIkit.modal($('#'+target).get(0)).show();
        }
    });

    // Ajax form
    $('body').on('submit', '.ajax-form', function (event) {
        var form = $(this),
            target = form.attr('action'),
            method = form.attr('method').toLowerCase();

        if (
            typeof target == 'string' && target.length > 0 &&
            typeof method == 'string' && $.inArray(method, ['get', 'post']) >= 0
        ) {
            event.preventDefault();

            $.ajax({
                method: method,
                url: target,
                data: form.serialize()
            })
            .done(function(msg) {
                try {
                    msg = typeof msg == 'object' ? msg : JSON.parse(msg);
                    if (msg.success) {
                        window.location.reload();
                    }
                } catch (e) {
                    var newForm = $(msg).find('.ajax-form').html();
                    if (typeof newForm != "undefined" && newForm.length > 0) {
                        form.html(newForm);
                    } else {
                        window.location.reload();
                    }
                }
            })
            .fail(function( jqXHR, textStatus ) {
                window.location.reload();
            });
        }
    });
});